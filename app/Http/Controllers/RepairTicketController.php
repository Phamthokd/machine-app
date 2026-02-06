<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\RepairTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RepairTicketController extends Controller
{
    public function create(Request $request)
    {
        $ma = $request->query('machine');
        abort_unless($ma, 400, 'Thiếu mã thiết bị (machine)');

        $machine = Machine::with('department')->where('ma_thiet_bi', $ma)->firstOrFail();
        
        // Fetch contractors for the support dropdown
        $contractors = User::role('contractor')->get();

        return view('repairs.create', compact('machine', 'contractors'));
    }

    public function store(Request $request)
    {
        $isTeamLeader = auth()->user()->hasRole('team_leader');
        $isContractor = auth()->user()->hasRole('contractor');

        $rules = [
            'machine_id' => ['required', 'exists:machines,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'nguyen_nhan' => ['required', 'string'],
            'started_at' => ['required', 'date'],
        ];

        if ($isTeamLeader) {
            // Team Leader: Minimal validation
            $rules['ma_hang'] = ['nullable'];
            $rules['cong_doan'] = ['nullable'];
            $rules['noi_dung_sua_chua'] = ['nullable'];
            $rules['endline_qc_name'] = ['nullable'];
        } elseif ($isContractor) {
             // Contractor: specific validation (handled safely by hidden N/A values in form, but good to be explicit)
            $rules['ma_hang'] = ['nullable'];
            $rules['cong_doan'] = ['nullable'];
            $rules['noi_dung_sua_chua'] = ['required', 'string'];
            $rules['endline_qc_name'] = ['nullable'];
            $rules['nguoi_ho_tro'] = ['nullable', 'string', 'max:255'];
        } else {
            // Standard validation
            $rules['ma_hang'] = ['required', 'string', 'max:255'];
            $rules['cong_doan'] = ['required', 'string', 'max:255'];
            $rules['noi_dung_sua_chua'] = ['required', 'string'];
            $rules['endline_qc_name'] = ['required', 'string', 'max:255'];
            $rules['inline_qc_name'] = ['nullable', 'string', 'max:255'];
            $rules['qa_supervisor_name'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $validated['created_by'] = auth()->id();
        $validated['code'] = 'RM-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        // Determine Type
        if ($isContractor) {
            $validated['type'] = 'contractor';
        } elseif ($isTeamLeader) {
            // Validate type input, default to mechanic if valid
            $type = $request->input('type', 'mechanic');
            $validated['type'] = in_array($type, ['mechanic', 'contractor']) ? $type : 'mechanic';
        } else {
            $validated['type'] = 'mechanic';
        }

        if ($isTeamLeader) {
            $validated['status'] = 'pending';
            $validated['ended_at'] = null; // Open ticket
        } else {
            $validated['status'] = 'submitted'; // Or completed
            $validated['ended_at'] = now();
        }

        $ticket = RepairTicket::create($validated);

        // Redirect
        $machine = Machine::findOrFail($validated['machine_id']);
        
        if ($isTeamLeader) {
             return redirect("/m/{$machine->ma_thiet_bi}")
                ->with('success', "Đã gửi báo hỏng: {$ticket->code}. Đang chờ thợ máy tiếp nhận.");
        }

        return redirect("/m/{$machine->ma_thiet_bi}")
            ->with('success', "Đã tạo phiếu sửa: {$ticket->code}");
    }
    public function index()
    {
        $repairs = RepairTicket::with(['machine.department', 'createdBy'])
            ->where('type', 'mechanic')
            ->orderByDesc('id')
            ->simplePaginate(20);

        return view('repairs.index', compact('repairs'));
    }

    public function contractorIndex()
    {
        $repairs = RepairTicket::with(['machine.department', 'createdBy'])
            ->where('type', 'contractor')
            ->orderByDesc('id')
            ->simplePaginate(20);

        return view('repairs.contractor_index', compact('repairs'));
    }

public function show(RepairTicket $repair)
{
    $repair->load(['machine.department', 'createdBy']);
    return view('repairs.show', compact('repair'));
}
    public function export()
    {
        $repairs = RepairTicket::with(['machine.department', 'createdBy'])
            ->where('type', 'mechanic')
            ->where('status', '!=', 'pending')
            ->orderByDesc('id')
            ->get();

        // 1. Group by Department Name
        $grouped = $repairs->groupBy(function ($item) {
            return $item->machine->department->name ?? 'Khác';
        });

        // 2. Define Headers
        $headers = [
            'Mã thiết bị', 'Tên thiết bị', 'Tổ',
            'Mã hàng', 'Công đoạn', 'Nguyên nhân', 'Nội dung sửa',
            'Thời gian báo', 'Bắt đầu', 'Kết thúc',
            'Người tạo', 'Inline QC', 'Endline QC', 'Chủ quản QA'
        ];

        // 3. Helper to render a Row
        $renderRow = function ($r) {
            $cells = [
                $r->machine->ma_thiet_bi ?? '',
                $r->machine->ten_thiet_bi ?? '',
                $r->machine->department->name ?? '',
                $r->ma_hang,
                $r->cong_doan,
                $r->nguyen_nhan,
                $r->noi_dung_sua_chua,
                $r->created_at,
                $r->started_at,
                $r->ended_at,
                $r->createdBy->name ?? '',
                $r->inline_qc_name ?? '',
                $r->endline_qc_name ?? '',
                $r->qa_supervisor_name ?? '',
            ];
            
            $xml = "    <Row>\n";
            foreach ($cells as $cell) {
                // Escape XML special chars
                $safe = htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8');
                $xml .= "     <Cell><Data ss:Type=\"String\">{$safe}</Data></Cell>\n";
            }
            $xml .= "    </Row>\n";
            return $xml;
        };

        // 4. Helper to start a Worksheet
        $startSheet = function ($name) use ($headers) {
            $safeName = preg_replace('/[\\\\\\/?*:\\[\\]]/', ' ', $name); // remove illegal excel sheet chars
            if (mb_strlen($safeName) > 31) $safeName = mb_substr($safeName, 0, 31);
            
            $xml = " <Worksheet ss:Name=\"{$safeName}\">\n";
            $xml .= "  <Table>\n";
            // Header Row
            $xml .= "   <Row>\n";
            foreach ($headers as $h) {
                $xml .= "    <Cell><Data ss:Type=\"String\">{$h}</Data></Cell>\n";
            }
            $xml .= "   </Row>\n";
            return $xml;
        };

        $endSheet = "  </Table>\n </Worksheet>\n";

        // 5. Build XML Content
        // Let's use .xls to ensure it opens with Excel by default on Windows
        $fileName = 'repair-tickets-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($repairs, $grouped, $renderRow, $startSheet, $endSheet) {
            $output = fopen('php://output', 'w');
            
            // XML Spreadsheet Header
            $preamble = '<?xml version="1.0"?>' . "\n";
            $preamble .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
            $preamble .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:o="urn:schemas-microsoft-com:office:office" ' . "\n";
            $preamble .= ' xmlns:x="urn:schemas-microsoft-com:office:excel" ' . "\n";
            $preamble .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            
            fwrite($output, $preamble);

            // --- SHEET 1: TỔNG HỢP (ALL) ---
            fwrite($output, $startSheet('Tổng hợp'));
            foreach ($repairs as $r) {
                fwrite($output, $renderRow($r));
            }
            fwrite($output, $endSheet);

            // --- SHEET 2..N: BY DEPARTMENT ---
            foreach ($grouped as $deptName => $items) {
                fwrite($output, $startSheet($deptName));
                foreach ($items as $r) {
                    fwrite($output, $renderRow($r));
                }
                fwrite($output, $endSheet);
            }

            // XML Spreadsheet Footer
            fwrite($output, "</Workbook>");
            fclose($output);

        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    public function exportContractor()
    {
        $repairs = RepairTicket::with(['machine.department', 'createdBy'])
            ->where('type', 'contractor')
            ->orderByDesc('id')
            ->get();

        // 1. Define Headers
        $headers = [
            'Mã phiếu',
            'Mã thiết bị', 'Tên thiết bị', 'Tổ',
            'Sự cố', 'Khắc phục', 'Người hỗ trợ',
            'Thời gian báo', 'Bắt đầu', 'Kết thúc',
            'Người tạo'
        ];

        // 2. Helper to render a Row
        $renderRow = function ($r) {
            $cells = [
                $r->code,
                $r->machine->ma_thiet_bi ?? '',
                $r->machine->ten_thiet_bi ?? '',
                $r->machine->department->name ?? '',
                $r->nguyen_nhan,
                $r->noi_dung_sua_chua,
                $r->nguoi_ho_tro,
                $r->created_at,
                $r->started_at,
                $r->ended_at,
                $r->createdBy->name ?? '',
            ];
            
            $xml = "    <Row>\n";
            foreach ($cells as $cell) {
                $safe = htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8');
                $xml .= "     <Cell><Data ss:Type=\"String\">{$safe}</Data></Cell>\n";
            }
            $xml .= "    </Row>\n";
            return $xml;
        };

        // 3. Helper to start a Worksheet
        $startSheet = function ($name) use ($headers) {
            $xml = " <Worksheet ss:Name=\"{$name}\">\n";
            $xml .= "  <Table>\n";
            $xml .= "   <Row>\n";
            foreach ($headers as $h) {
                $xml .= "    <Cell><Data ss:Type=\"String\">{$h}</Data></Cell>\n";
            }
            $xml .= "   </Row>\n";
            return $xml;
        };

        $endSheet = "  </Table>\n </Worksheet>\n";

        $fileName = 'contractor-repairs-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($repairs, $renderRow, $startSheet, $endSheet) {
            $output = fopen('php://output', 'w');
            
            fwrite($output, '<?xml version="1.0"?>' . "\n");
            fwrite($output, '<?mso-application progid="Excel.Sheet"?>' . "\n");
            fwrite($output, '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n");
            fwrite($output, ' xmlns:o="urn:schemas-microsoft-com:office:office" ' . "\n");
            fwrite($output, ' xmlns:x="urn:schemas-microsoft-com:office:excel" ' . "\n");
            fwrite($output, ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n");
            fwrite($output, ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n");

            fwrite($output, $startSheet('Lịch sử công trình'));
            foreach ($repairs as $r) {
                fwrite($output, $renderRow($r));
            }
            fwrite($output, $endSheet);
            fwrite($output, "</Workbook>");
            fclose($output);
        }, $fileName, ['Content-Type' => 'application/vnd.ms-excel']);
    }

    public function edit(RepairTicket $repair)
    {
        $repair->load('machine');
        $machine = $repair->machine;
        $contractors = \App\Models\User::role('contractor')->get();
        return view('repairs.edit', compact('repair', 'machine', 'contractors'));
    }

    public function update(Request $request, RepairTicket $repair)
    {
        $rules = [
            'ma_hang' => ['required', 'string', 'max:255'],
            'cong_doan' => ['required', 'string', 'max:255'],
            'nguyen_nhan' => ['required', 'string'],
            'noi_dung_sua_chua' => ['required', 'string'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'endline_qc_name' => ['required', 'string', 'max:255'],
            'inline_qc_name' => ['nullable', 'string', 'max:255'],
            'qa_supervisor_name' => ['nullable', 'string', 'max:255'],
        ];

        if ($repair->type == 'contractor') {
            // Simplified rules for contractor
            $rules = [
                'nguyen_nhan' => ['required', 'string'],
                'noi_dung_sua_chua' => ['required', 'string'],
                'started_at' => ['required', 'date'],
                'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
                'nguoi_ho_tro' => ['nullable', 'string', 'max:255'],
            ];
        }

        $validated = $request->validate($rules);

        $validated['status'] = 'submitted';
        $validated['ended_at'] = now();

        $repair->update($validated);

        return redirect('/repair-requests')->with('success', "Đã hoàn thành phiếu sửa: {$repair->code}");
    }

    public function requestsIndex()
    {
        $query = RepairTicket::with(['machine.department', 'createdBy'])
            ->where('status', 'pending');

        // Filter based on role
        if (auth()->user()->hasRole('contractor')) {
            $query->where('type', 'contractor');
        } elseif (auth()->user()->hasRole('admin')) {
            // Admin sees ALL requests (both mechanic and contractor)
        } else {
            // Default to mechanic requests (Repair Tech, Warehouse, Team Leader, etc.)
            $query->where('type', 'mechanic');
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('repairs.requests', compact('requests'));
    }
}
