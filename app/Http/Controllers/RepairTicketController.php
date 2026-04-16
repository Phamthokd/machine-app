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

        // Prevent duplicate creation if machine is already pending
        if (auth()->check()) {
            $hasPending = RepairTicket::where('machine_id', $machine->id)
                ->whereNull('ended_at')
                ->exists();

            if ($hasPending) {
                return redirect("/m/{$machine->ma_thiet_bi}")
                    ->with('error', 'Máy này đã được báo trước đó. Vui lòng hoàn tất phiếu báo cũ trước khi tạo báo lỗi mới!');
            }
        }

        // Fetch contractors for the support dropdown
        $contractors = User::role('contractor')->get();

        return view('repairs.create', compact('machine', 'contractors'));
    }

    public function store(Request $request)
    {
        $isTeamLeader = auth()->user()->isTeamLeaderUser();
        $isContractor = auth()->user()->isContractorUser();

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
            $rules['endline_qc_name'] = ['nullable', 'string', 'max:255'];
            $rules['inline_qc_name'] = ['nullable', 'string', 'max:255'];
            $rules['qa_supervisor_name'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $validated['created_by'] = auth()->id();
        $validated['code'] = 'RM-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        // If not a team leader and not a contractor, the creator is implicitly the mechanic
        if (!$isTeamLeader && !$isContractor) {
            $validated['mechanic_id'] = auth()->id();
        }

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

        $hasPending = RepairTicket::where('machine_id', $validated['machine_id'])
            ->whereNull('ended_at')
            ->exists();

        if ($hasPending) {
            return back()->withInput()->with('error', 'Máy này đã được báo trước đó. Vui lòng hoàn tất phiếu báo cũ trước khi tạo lỗi mới!');
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
    public function index(Request $request)
    {
        $query = RepairTicket::with(['machine.department', 'department', 'createdBy', 'mechanic'])
            ->where('type', 'mechanic');

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $repairs = $query->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $departments = \App\Models\Department::whereHas('machines')->orderBy('name')->get();

        return view('repairs.index', compact('repairs', 'departments'));
    }

    public function contractorIndex()
    {
        $repairs = RepairTicket::with(['machine.department', 'createdBy', 'mechanic'])
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
    public function export(Request $request)
    {
        $query = RepairTicket::with(['machine.department', 'department', 'createdBy', 'mechanic'])
            ->where('type', 'mechanic')
            ->where('status', '!=', 'pending');

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $repairs = $query->orderByDesc('id')
            ->get();

        // 1. Group by Department Name (from Ticket primarily)
        $grouped = $repairs->groupBy(function ($item) {
            return $item->department->name ?? ($item->machine->department->name ?? 'Khác');
        });

        // 2. Define Headers
        $headers = [
            'Mã thiết bị',
            'Tên thiết bị',
            'Tổ',
            'Mã hàng',
            'Công đoạn',
            'Nguyên nhân',
            'Nội dung sửa',
            'Thời gian báo',
            'Bắt đầu',
            'Kết thúc',
            'Thời gian chờ (phút)',
            'Thời gian sửa (phút)',
            'Người tạo phiếu',
            'Thợ sửa',
            'Inline QC',
            'Endline QC',
            'Chủ quản QA'
        ];

        // 3. Helper to render a Row
        $renderRow = function ($r) {
            $creator = $r->createdBy->name ?? '';
            $mechanic = $r->mechanic->name ?? '';

            // Theo yêu cầu: nếu người tạo phiếu và thợ sửa là 1 người thì thời gian báo = thời gian bắt đầu
            $reportedTime = $r->created_at;
            if ($creator !== '' && $creator === $mechanic && $r->started_at) {
                $reportedTime = $r->started_at;
            }

            $waitTime = '';
            if ($r->started_at) {
                $waitTime = \Carbon\Carbon::parse($reportedTime)->diffInMinutes(\Carbon\Carbon::parse($r->started_at));
            }

            $repairTime = '';
            if ($r->started_at && $r->ended_at) {
                $repairTime = \Carbon\Carbon::parse($r->started_at)->diffInMinutes(\Carbon\Carbon::parse($r->ended_at));
            }

            $cells = [
                $r->machine->ma_thiet_bi ?? '',
                $r->machine->ten_thiet_bi ?? '',
                $r->department->name ?? ($r->machine->department->name ?? ''),
                $r->ma_hang,
                $r->cong_doan,
                $r->nguyen_nhan,
                $r->noi_dung_sua_chua,
                $reportedTime,
                $r->started_at,
                $r->ended_at,
                $waitTime,
                $repairTime,
                $creator,
                $mechanic,
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

    public function exportContractor(Request $request)
    {
        $query = RepairTicket::with(['machine.department', 'department', 'createdBy', 'mechanic'])
            ->where('type', 'contractor');

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $repairs = $query->orderByDesc('id')
            ->get();

        // 1. Define Headers
        $headers = [
            'Mã phiếu',
            'Mã thiết bị',
            'Tên thiết bị',
            'Tổ',
            'Sự cố',
            'Khắc phục',
            'Người hỗ trợ',
            'Thời gian báo',
            'Bắt đầu',
            'Kết thúc',
            'Thời gian chờ (phút)',
            'Thời gian sửa (phút)',
            'Người tạo phiếu',
            'Người sửa (Nhà thầu)'
        ];

        // 2. Helper to render a Row
        $renderRow = function ($r) {
            $reportedTime = $r->created_at;
            $waitTime = '';
            if ($r->started_at) {
                $waitTime = \Carbon\Carbon::parse($reportedTime)->diffInMinutes(\Carbon\Carbon::parse($r->started_at));
            }

            $repairTime = '';
            if ($r->started_at && $r->ended_at) {
                $repairTime = \Carbon\Carbon::parse($r->started_at)->diffInMinutes(\Carbon\Carbon::parse($r->ended_at));
            }

            $cells = [
                $r->code,
                $r->machine->ma_thiet_bi ?? '',
                $r->machine->ten_thiet_bi ?? '',
                $r->department->name ?? ($r->machine->department->name ?? ''),
                $r->nguyen_nhan,
                $r->noi_dung_sua_chua,
                $r->nguoi_ho_tro,
                $reportedTime,
                $r->started_at,
                $r->ended_at,
                $waitTime,
                $repairTime,
                $r->createdBy->name ?? '',
                $r->mechanic->name ?? '',
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
            'endline_qc_name' => ['nullable', 'string', 'max:255'],
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
        $validated['mechanic_id'] = auth()->id();

        $repair->update($validated);

        return redirect('/repair-requests')->with('success', "Đã hoàn thành phiếu sửa: {$repair->code}");
    }

    public function editCompleted(RepairTicket $repair)
    {
        abort_unless(auth()->user()->isAdminUser(), 403);
        $repair->load('machine');
        $machine = $repair->machine;
        $mechanics = \App\Models\User::role(['repair_tech', 'admin'])->get();
        $contractors = \App\Models\User::role('contractor')->get();
        return view('repairs.edit_completed', compact('repair', 'machine', 'contractors', 'mechanics'));
    }

    public function updateCompleted(Request $request, RepairTicket $repair)
    {
        abort_unless(auth()->user()->isAdminUser(), 403);

        $rules = [
            'ma_hang' => ['nullable', 'string', 'max:255'],
            'cong_doan' => ['nullable', 'string', 'max:255'],
            'nguyen_nhan' => ['required', 'string'],
            'noi_dung_sua_chua' => ['required', 'string'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'endline_qc_name' => ['nullable', 'string', 'max:255'],
            'inline_qc_name' => ['nullable', 'string', 'max:255'],
            'qa_supervisor_name' => ['nullable', 'string', 'max:255'],
            'mechanic_id' => ['required', 'exists:users,id'],
        ];

        if ($repair->type == 'contractor') {
            $rules = [
                'nguyen_nhan' => ['required', 'string'],
                'noi_dung_sua_chua' => ['required', 'string'],
                'started_at' => ['required', 'date'],
                'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
                'nguoi_ho_tro' => ['nullable', 'string', 'max:255'],
                'mechanic_id' => ['required', 'exists:users,id'],
            ];
        }

        $validated = $request->validate($rules);
        $repair->update($validated);

        return back()->with('success', "Đã cập nhật phiếu sửa đã hoàn thành: {$repair->code}");
    }

    public function accept(RepairTicket $repair)
    {
        abort_unless(auth()->user()->canManageRepairs(), 403);

        // Allow taking unassigned tickets.
        if (empty($repair->mechanic_id)) {
            $repair->update([
                'mechanic_id' => auth()->id(),
                'started_at' => now(),
            ]);
        }

        // Redirect to edit page
        return redirect("/repairs/{$repair->id}/edit");
    }

    public function requestsIndex()
    {
        $query = RepairTicket::with(['machine.department', 'createdBy', 'mechanic'])
            ->where('status', 'pending');

        // Filter based on role
        if (auth()->user()->isContractorUser()) {
            $query->where('type', 'contractor');
        } elseif (auth()->user()->isAdminUser()) {
            // Admin sees ALL requests (both mechanic and contractor)
        } else {
            // Default to mechanic requests (Repair Tech, Warehouse, Team Leader, etc.)
            $query->where('type', 'mechanic');
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('repairs.requests', compact('requests'));
    }

    public function destroy(RepairTicket $repair)
    {
        abort_unless(auth()->user()->isAdminUser(), 403, 'Bạn không có quyền xoá phiếu này.');
        $repair->delete();
        return back()->with('success', 'Đã xoá thành công phiếu báo hỏng.');
    }
}
