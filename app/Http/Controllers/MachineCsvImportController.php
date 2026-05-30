<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MachineCsvImportController extends Controller
{
    public function form()
    {
        return view('machines.import_csv');
    }

    public function downloadTemplate()
    {
        $headers = [
            'ma_thiet_bi',
            'ten_thiet_bi',
            'to_hien_tai',
            'brand',
            'model',
            'serial',
            'invoice_cd',
            'year',
            'country',
            'stock_in_date',
            'vi_tri_text',
            'ngay_vao_kho',
            'ngay_ra_kho',
            'warranty_period'
        ];

        $samples = [
            [
                '00-MM-0001-KXD',
                'Chainstitch machine',
                'Phòng mẫu máy may',
                'JOYEE',
                'JY-W480A',
                '110301004',
                'INV-2023-001',
                '2012',
                'China',
                '29/07/2023',
                'Xưởng 5',
                '29/07/2023',
                '',
                '12 tháng'
            ],
            [
                '00-MM-0002-KXD',
                'Double needle lockstitch',
                'Kho cơ khí máy may',
                'JACK',
                'KJK-5845-3',
                '70712854',
                'INV-2026-009',
                '2012',
                'Japan',
                '05/02/2026',
                'Kho cơ khí',
                '',
                '05/02/2026',
                '24 tháng'
            ]
        ];

        $fileName = 'machine_import_template.csv';

        return response()->streamDownload(function () use ($headers, $samples) {
            $output = fopen('php://output', 'w');
            
            // Write UTF-8 BOM to display Vietnamese characters correctly in Excel
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, $headers);

            foreach ($samples as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
    private function cleanCode($v): string
{
    $v = (string)$v;
    $v = preg_replace('/^\xEF\xBB\xBF/', '', $v); // BOM
    $v = str_replace(["\xC2\xA0", "\u{00A0}"], ' ', $v); // NBSP
    $v = trim($v);
    $v = preg_replace('/\s+/', '', $v); // xoá mọi khoảng trắng trong mã
    return $v;
}


    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('success', 'Không mở được file CSV');
        }

        // 1) Dò delimiter (,, ; hoặc tab)
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return back()->with('success', 'CSV rỗng');
        }
        $delimiter = $this->detectDelimiter($firstLine);

        // quay lại đầu file
        rewind($handle);

        // 2) Đọc header
        $rawHeader = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeader || count($rawHeader) === 0) {
            fclose($handle);
            return back()->with('success', 'CSV không có header');
        }

        // Chuẩn hoá header
        $normalize = function ($h) {
            $h = (string)$h;

            // remove BOM
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);

            // trim + lowercase
            $h = trim(mb_strtolower($h, 'UTF-8'));

            // bỏ dấu ":" cuối (ví dụ "Year:" -> "year")
            $h = rtrim($h, ':');

            // chuẩn hoá khoảng trắng
            $h = preg_replace('/\s+/', ' ', $h);

            // một số file có header dạng "deparment" sai chính tả -> vẫn coi như "department"
            if ($h === 'deparment') $h = 'department';

            return $h;
        };

        $header = array_map($normalize, $rawHeader);

        // helper: tìm cột theo nhiều alias
        $idxAny = function (array $aliases) use ($header, $normalize) {
            foreach ($aliases as $name) {
                $name = $normalize($name);
                $pos = array_search($name, $header, true);
                if ($pos !== false) return $pos;
            }
            return null;
        };

        /**
         * Bạn chọn PHƯƠNG ÁN A:
         * - Mã máy (MA_MAY) sẽ map vào cột DB: ma_thiet_bi
         * Vậy file CSV có thể có 1 trong các header:
         * - ma_thiet_bi (khuyến nghị)
         * - ma_may
         * - MA_MAY
         */
        $i_code      = $idxAny(['ma_thiet_bi', 'ma_may', 'MA_MAY']);
        $i_ten       = $idxAny(['ten_thiet_bi', 'ten_may', 'TEN_MAY']);
        $i_to        = $idxAny(['to_hien_tai', 'tổ hiện tại', 'to hien tai']);

        $i_brand     = $idxAny(['brand']);
        $i_model     = $idxAny(['model']);
        $i_serial    = $idxAny(['serial']);
        $i_invoice   = $idxAny(['invoice/cd', 'invoice', 'invoice cd', 'invoice_cd']);
        $i_year      = $idxAny(['year']);
        $i_country   = $idxAny(['country of origin', 'country', 'nước sản xuất', 'nuoc san xuat']);
 
        $i_stockin   = $idxAny(['stock-in date', 'stock in date', 'stock_in_date', 'ngày nhập', 'ngay nhap']);
        $i_department= $idxAny(['department', 'vi_tri', 'vị trí', 'vi_tri_text', 'vị trí (txt)', 'vi tri (txt)']);
        $i_ngayvao   = $idxAny(['ngày vào kho', 'ngay vao kho', 'ngay_vao_kho']);
        $i_ngayra    = $idxAny(['ngày ra kho', 'ngay ra kho', 'ngay_ra_kho']);
        $i_warranty  = $idxAny(['thoi_gian_bao_hanh', 'thời gian bảo hành', 'warranty_period', 'warranty period']);

        if ($i_code === null) {
            fclose($handle);
            return back()->with(
                'success',
                'Không tìm thấy cột mã máy trong CSV. Cần 1 trong các cột: ma_thiet_bi / ma_may / MA_MAY. Header hiện có: ' . implode(' | ', $header)
            );
        }

        // parse date dd/mm/yyyy (hoặc d/m/yyyy). Nếu gặp N/A hoặc năm đơn lẻ thì trả null.
        $parseDate = function ($v) {
            $v = trim((string)$v);
            if ($v === '' || mb_strtolower($v, 'UTF-8') === 'n/a') return null;

            // nếu chỉ có năm như 2012 thì không ép vào DATE
            if (preg_match('/^\d{4}$/', $v)) return null;

            $v = str_replace('-', '/', $v);
            $parts = explode('/', $v);
            if (count($parts) === 3) {
                $d = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $m = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                $y = $parts[2];
                if (preg_match('/^\d{4}$/', $y)) {
                    return "{$y}-{$m}-{$d}";
                }
            }
            return null;
        };

        $count = 0;
        $skipEmpty = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // tránh dòng rỗng
            if ($row === [null] || count($row) === 0) {
                $skipEmpty++;
                continue;
            }

            $code = $this->cleanCode($row[$i_code] ?? '');
            if ($code === '' || mb_strtolower($code, 'UTF-8') === 'n/a') {
                $skipEmpty++;
                continue;
            }

            $ten = $i_ten !== null ? trim((string)($row[$i_ten] ?? '')) : null;
            $toHienTai = $i_to !== null ? trim((string)($row[$i_to] ?? '')) : 'Kho';
            if ($toHienTai === '') $toHienTai = 'Kho';

            // Tạo/đảm bảo department tồn tại
            $dept = Department::firstOrCreate(
                ['name' => $toHienTai],
                [
                    'code' => Str::slug($toHienTai, '_'),
                    'type' => Str::contains(mb_strtolower($toHienTai, 'UTF-8'), 'kho') ? 'warehouse' : 'team',
                ]
            );

            // Update/Create máy theo ma_thiet_bi
            Machine::updateOrCreate(
                ['ma_thiet_bi' => $code],
                [
                    'ten_thiet_bi' => $ten,
                    'current_department_id' => $dept->id,

                    'brand' => $i_brand !== null ? $this->cleanCell($row[$i_brand] ?? null) : null,
                    'model' => $i_model !== null ? $this->cleanCell($row[$i_model] ?? null) : null,
                    'serial' => $i_serial !== null ? $this->cleanCell($row[$i_serial] ?? null) : null,
                    'invoice_cd' => $i_invoice !== null ? $this->cleanCell($row[$i_invoice] ?? null) : null,
                    'year' => $i_year !== null ? $this->cleanCell($row[$i_year] ?? null) : null,
                    'country' => $i_country !== null ? $this->cleanCell($row[$i_country] ?? null) : null,

                    'stock_in_date' => $i_stockin !== null ? $parseDate($row[$i_stockin] ?? null) : null,
                    'vi_tri_text' => $i_department !== null ? $this->cleanCell($row[$i_department] ?? null) : null,
                    'ngay_vao_kho' => $i_ngayvao !== null ? $parseDate($row[$i_ngayvao] ?? null) : null,
                    'ngay_ra_kho' => $i_ngayra !== null ? $parseDate($row[$i_ngayra] ?? null) : null,
                    'warranty_period' => $i_warranty !== null ? $this->cleanCell($row[$i_warranty] ?? null) : null,
                ]
            );

            $count++;
        }

        fclose($handle);

        return back()->with('success', "Import CSV xong: {$count} dòng (bỏ qua {$skipEmpty} dòng rỗng)");
    }

    private function detectDelimiter(string $line): string
    {
        $candidates = [',' , ';', "\t", '|'];
        $bestDelim = ',';
        $bestCount = 0;

        foreach ($candidates as $d) {
            $count = substr_count($line, $d);
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelim = $d;
            }
        }
        return $bestDelim;
    }

    private function cleanCell($v): ?string
    {
        if ($v === null) return null;
        $v = trim((string)$v);
        if ($v === '' || mb_strtolower($v, 'UTF-8') === 'n/a') return null;
        return $v;
    }
}
