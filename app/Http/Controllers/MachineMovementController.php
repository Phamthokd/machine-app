<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Machine;
use Illuminate\Http\Request;

use App\Models\MachineMovement;
use Illuminate\Support\Facades\Auth;

class MachineMovementController extends Controller
{
    public function index()
    {
        $movements = MachineMovement::with(['machine', 'fromDepartment', 'toDepartment', 'user'])
            ->orderByDesc('created_at')
            ->simplePaginate(20);
            
        return view('machines.move_history', compact('movements'));
    }

    public function edit($id)
    {
        $machine = Machine::with('department')->findOrFail($id);
        $departments = Department::has('machines')->orderBy('name')->get();
        return view('machines.move', compact('machine', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $machine = Machine::findOrFail($id);
        $oldDeptId = $machine->current_department_id;

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'note' => 'nullable|string|max:255',
        ]);
        
        // Prevent moving to same department
        if ($oldDeptId == $validated['department_id']) {
            return back()->withErrors(['department_id' => 'Máy đang ở tổ này rồi.']);
        }

        // Record History
        MachineMovement::create([
            'machine_id' => $machine->id,
            'from_department_id' => $oldDeptId,
            'to_department_id' => $validated['department_id'],
            'user_id' => Auth::id(),
            'note' => $validated['note'],
        ]);

        // Update department
        $machine->update([
            'current_department_id' => $validated['department_id'],
            'vi_tri_text' => $validated['note'] ? $validated['note'] : $machine->vi_tri_text
        ]);

        return redirect("/m/{$machine->ma_thiet_bi}")
            ->with('success', 'Đã chuyển máy sang tổ mới thành công.');
    }

    public function export()
    {
        $movements = MachineMovement::with(['machine', 'fromDepartment', 'toDepartment', 'user'])
            ->orderByDesc('created_at')
            ->get();

        $fileName = 'machine-movements-' . now()->format('Ymd-His') . '.xls';
        
        $headers = ['Thời gian', 'Mã thiết bị', 'Tên thiết bị', 'Từ tổ', 'Đến tổ', 'Người chuyển', 'Ghi chú'];

        return response()->streamDownload(function () use ($movements, $headers) {
            $output = fopen('php://output', 'w');
            
            // XML Spreadsheet Header
            fwrite($output, '<?xml version="1.0"?>' . "\n");
            fwrite($output, '<?mso-application progid="Excel.Sheet"?>' . "\n");
            fwrite($output, '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n");
            fwrite($output, ' <Worksheet ss:Name="Lịch sử chuyển tổ">' . "\n");
            fwrite($output, '  <Table>' . "\n");
            
            // Header Row
            fwrite($output, '   <Row>' . "\n");
            foreach ($headers as $h) {
                fwrite($output, "    <Cell><Data ss:Type=\"String\">{$h}</Data></Cell>\n");
            }
            fwrite($output, '   </Row>' . "\n");

            // Data Rows
            foreach ($movements as $m) {
                $cells = [
                    $m->created_at,
                    $m->machine->ma_thiet_bi ?? '',
                    $m->machine->ten_thiet_bi ?? '',
                    $m->fromDepartment->name ?? '',
                    $m->toDepartment->name ?? '',
                    $m->user->name ?? '',
                    $m->note ?? ''
                ];
                
                fwrite($output, '   <Row>' . "\n");
                foreach ($cells as $c) {
                    $safe = htmlspecialchars((string)$c, ENT_XML1, 'UTF-8');
                    fwrite($output, "    <Cell><Data ss:Type=\"String\">{$safe}</Data></Cell>\n");
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
