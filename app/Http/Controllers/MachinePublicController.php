<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\ItRepair;
use Illuminate\Http\Request;   
use App\Models\Department; 

class MachinePublicController extends Controller
{
    public function show($ma_thiet_bi)
    {
        $machine = Machine::with(['department', 'repairTickets' => function($q){
            $q->orderByDesc('id')->limit(20);
        }])
        ->where('ma_thiet_bi', $ma_thiet_bi)
        ->first();
    
        if (!$machine) {
            abort(404, "Không tìm thấy máy: {$ma_thiet_bi}. Hãy kiểm tra DB / dữ liệu import.");
        }

        $itRepairs = ItRepair::with(['reporter', 'resolver'])
            ->where('machine_id', $machine->id)
            ->latest()
            ->limit(10)
            ->get();
    
        return view('machines.public_show', compact('machine', 'itRepairs'));
    }
}
