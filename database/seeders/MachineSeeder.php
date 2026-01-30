<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;
use App\Models\Department;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        $to01 = Department::where('code', 'TO_01')->first();

        Machine::create([
            'ma_thiet_bi' => 'MAY-001',
            'ten_thiet_bi' => 'Máy may JUKI DDL-8700',
            'current_department_id' => $to01->id,
        ]);

        Machine::create([
            'ma_thiet_bi' => 'MAY-002',
            'ten_thiet_bi' => 'Máy vắt sổ PEGASUS',
            'current_department_id' => $to01->id,
        ]);
    }
}
