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

        // Lấy danh sách user theo role (Spatie)
        $endlineQcs = User::role('endline_qc')->orderBy('name')->get();
        $inlineQcs  = User::role('inline_qc_triumph')->orderBy('name')->get();
        $qaSupers   = User::role('qa_supervisor_triumph')->orderBy('name')->get();

        return view('repairs.create', compact('machine', 'endlineQcs', 'inlineQcs', 'qaSupers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'department_id' => ['required', 'exists:departments,id'],

            'ma_hang' => ['required', 'string', 'max:255'],
            'cong_doan' => ['required', 'string', 'max:255'],
            'nguyen_nhan' => ['required', 'string'],
            'noi_dung_sua_chua' => ['required', 'string'],

            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],

            'endline_qc_user_id' => ['required', 'exists:users,id'],
            'inline_qc_user_id' => ['required', 'exists:users,id'],
            'qa_supervisor_user_id' => ['required', 'exists:users,id'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'submitted';
        $validated['code'] = 'RM-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        $ticket = RepairTicket::create($validated);

        // điều hướng về trang máy
        $machine = Machine::findOrFail($validated['machine_id']);
        return redirect("/m/{$machine->ma_thiet_bi}")
            ->with('success', "Đã tạo phiếu sửa: {$ticket->code}");
    }
    public function index()
{
    $repairs = RepairTicket::with(['machine.department'])
    ->orderByDesc('id')
    ->simplePaginate(20);

    return view('repairs.index', compact('repairs'));
}

public function show(RepairTicket $repair)
{
    $repair->load(['machine.department', 'createdBy']);
    return view('repairs.show', compact('repair'));
}

}
