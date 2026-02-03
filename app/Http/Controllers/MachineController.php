<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Department;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request)
    {
        $query = Machine::with('department');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ma_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('ten_thiet_bi', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%");
            });
        }
        
        if ($dept = $request->input('department_id')) {
            $query->where('current_department_id', $dept);
        }

        $machines = $query->orderBy('ma_thiet_bi')->simplePaginate(20);
        $departments = Department::orderBy('name')->get();

        return view('machines.index', compact('machines', 'departments'));
    }

    public function edit(Machine $machine)
    {
        $departments = Department::orderBy('name')->get();
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
            'year' => 'nullable|string|max:4',
            'country' => 'nullable|string|max:255',
            'invoice_cd' => 'nullable|string|max:255',
            'stock_in_date' => 'nullable|date',
            'vi_tri_text' => 'nullable|string|max:255',
            'ngay_vao_kho' => 'nullable|date',
            'ngay_ra_kho' => 'nullable|date',
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
}
