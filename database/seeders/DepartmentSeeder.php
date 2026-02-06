<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Defined departments
        $departments = [
            ['code' => 'KHO', 'name' => 'Kho', 'type' => 'warehouse'],
            ['code' => 'LAP_TRINH', 'name' => 'Lập trình', 'type' => 'team'],
            ['code' => 'CD1', 'name' => 'CD1', 'type' => 'team'],
            ['code' => 'CD2', 'name' => 'CD2', 'type' => 'team'],
            ['code' => 'PHONG_MAU', 'name' => 'Phòng mẫu', 'type' => 'team'],
        ];

        // Add Team 01 to 40
        for ($i = 1; $i <= 40; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $departments[] = [
                'code' => "TO_{$num}",
                'name' => "Tổ {$num}",
                'type' => 'team',
            ];
        }

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['code' => $dept['code']],
                ['name' => $dept['name'], 'type' => $dept['type']]
            );
        }
    }
}
