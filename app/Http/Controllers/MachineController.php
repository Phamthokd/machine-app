<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $query = Machine::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('ten_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('purchase_order', 'like', "%{$search}%");
            });
        }
        
        if ($dept = $request->input('department_id')) {
            $query->where('current_department_id', $dept);
        }

        $machines = $query->orderBy('ma_thiet_bi')->simplePaginate(20);
        $departments = Department::has('machines')->orderBy('name')->get();

        return view('machines.index', compact('machines', 'departments'));
    }

    public function edit(Machine $machine)
    {
        $departments = Department::has('machines')->orderBy('name')->get();
        return view('machines.edit', compact('machine', 'departments'));
    }

    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'ten_thiet_bi' => 'required|string|max:255',
            'current_department_id' => 'required|exists:departments,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',
            'invoice_cd' => 'nullable|string|max:255',
            'stock_in_date' => 'nullable|date',
            'vi_tri_text' => 'nullable|string|max:255',
            'ngay_vao_kho' => 'nullable|date',
            'ngay_ra_kho' => 'nullable|date',
            'warranty_period' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'supplier' => 'nullable|string|max:255',
            'purchase_order' => 'nullable|string|max:255',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')
            ->with('success', 'Cập nhật máy thành công: ' . $machine->ma_thiet_bi);
    }

    public function destroy(Machine $machine)
    {
        // Delete related data first
        $machine->repairTickets()->delete();
        $machine->movements()->delete();
        
        $machine->delete();
        
        return redirect()->route('machines.index')
            ->with('success', 'Đã xoá máy và dữ liệu liên quan: ' . $machine->ma_thiet_bi);
    }

    public function printQr(Machine $machine)
    {
        $qrCode = QrCode::encoding('UTF-8')->size(200)->generate($machine->ma_thiet_bi);
        return view('machines.print_qr', compact('machine', 'qrCode'));
    }

    public function printDepartmentQr(Department $department)
    {
        $machines = $department->machines()->orderBy('ma_thiet_bi')->get();
        return view('machines.print_batch_qr', compact('department', 'machines'));
    }

    public function export(Request $request)
    {
        $query = Machine::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('ten_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('purchase_order', 'like', "%{$search}%");
            });
        }

        $deptName = 'Tat_ca_to';
        $sheetName = 'Danh sách thiết bị';
        if ($deptId = $request->input('department_id')) {
            $query->where('current_department_id', $deptId);
            $dept = Department::find($deptId);
            if ($dept) {
                $deptName = Str::slug($dept->name, '_');
                $sheetName = mb_substr($dept->name, 0, 30);
            }
        }

        $machines = $query->orderBy('ma_thiet_bi')->get();

        $fileName = 'danh_sach_thiet_bi_' . $deptName . '_' . now()->format('Ymd_His') . '.xls';

        $headers = [
            'MÃ THIẾT BỊ',
            'TÊN THIẾT BỊ',
            'TỔ HIỆN TẠI',
            'BRAND',
            'MODEL',
            'SERIAL',
            'INVOICE/CD',
            'NĂM SX',
            'NƯỚC SX',
            'NGÀY NHẬP',
            'VỊ TRÍ (TXT)',
            'NGÀY VÀO KHO',
            'NGÀY RA KHO',
            'THỜI GIAN BẢO HÀNH',
            'NGÀY MUA',
            'NHÀ CUNG CẤP',
            'ĐƠN ĐẶT HÀNG'
        ];

        return response()->streamDownload(function () use ($machines, $headers, $sheetName) {
            $output = fopen('php://output', 'w');

            $preamble = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $preamble .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
            $preamble .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";
            $preamble .= ' <Styles>' . "\n";
            $preamble .= '  <Style ss:ID="Default" ss:Name="Normal"><Font ss:FontName="Calibri" ss:Size="11"/></Style>' . "\n";
            $preamble .= '  <Style ss:ID="HeaderStyle">' . "\n";
            $preamble .= '   <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
            $preamble .= '   <Interior ss:Color="#4F46E5" ss:Pattern="Solid"/>' . "\n";
            $preamble .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>' . "\n";
            $preamble .= '   <Borders>' . "\n";
            $preamble .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>' . "\n";
            $preamble .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>' . "\n";
            $preamble .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>' . "\n";
            $preamble .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>' . "\n";
            $preamble .= '   </Borders>' . "\n";
            $preamble .= '  </Style>' . "\n";
            $preamble .= '  <Style ss:ID="DataStyle">' . "\n";
            $preamble .= '   <Alignment ss:Vertical="Center"/>' . "\n";
            $preamble .= '   <Borders>' . "\n";
            $preamble .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>' . "\n";
            $preamble .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>' . "\n";
            $preamble .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>' . "\n";
            $preamble .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>' . "\n";
            $preamble .= '   </Borders>' . "\n";
            $preamble .= '  </Style>' . "\n";
            $preamble .= ' </Styles>' . "\n";

            $safeSheetName = htmlspecialchars($sheetName, ENT_XML1, 'UTF-8');
            $preamble .= " <Worksheet ss:Name=\"{$safeSheetName}\">\n";
            $preamble .= '  <Table>' . "\n";

            fwrite($output, $preamble);

            // Header Row
            fwrite($output, '   <Row ss:Height="26">' . "\n");
            foreach ($headers as $h) {
                $safeHeader = htmlspecialchars($h, ENT_XML1, 'UTF-8');
                fwrite($output, "    <Cell ss:StyleID=\"HeaderStyle\"><Data ss:Type=\"String\">{$safeHeader}</Data></Cell>\n");
            }
            fwrite($output, '   </Row>' . "\n");

            // Data Rows
            foreach ($machines as $m) {
                $cells = [
                    $m->ma_thiet_bi,
                    $m->ten_thiet_bi,
                    $m->department->name ?? '',
                    $m->brand ?? '',
                    $m->model ?? '',
                    $m->serial ?? '',
                    $m->invoice_cd ?? '',
                    $m->year ?? '',
                    $m->country ?? '',
                    $m->stock_in_date ? \Carbon\Carbon::parse($m->stock_in_date)->format('d/m/Y') : '',
                    $m->vi_tri_text ?? '',
                    $m->ngay_vao_kho ? \Carbon\Carbon::parse($m->ngay_vao_kho)->format('d/m/Y') : '',
                    $m->ngay_ra_kho ? \Carbon\Carbon::parse($m->ngay_ra_kho)->format('d/m/Y') : '',
                    $m->warranty_period ?? '',
                    $m->purchase_date ? \Carbon\Carbon::parse($m->purchase_date)->format('d/m/Y') : '',
                    $m->supplier ?? '',
                    $m->purchase_order ?? '',
                ];

                fwrite($output, '   <Row ss:Height="20">' . "\n");
                foreach ($cells as $c) {
                    $safe = htmlspecialchars((string)$c, ENT_XML1, 'UTF-8');
                    fwrite($output, "    <Cell ss:StyleID=\"DataStyle\"><Data ss:Type=\"String\">{$safe}</Data></Cell>\n");
                }
                fwrite($output, '   </Row>' . "\n");
            }

            fwrite($output, '  </Table>' . "\n");
            fwrite($output, ' </Worksheet>' . "\n");
            fwrite($output, '</Workbook>');
            fclose($output);
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}
