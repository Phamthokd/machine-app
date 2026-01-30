<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'code' => 'KHO',
                'name' => 'Kho',
                'type' => 'warehouse',
            ],
            [
                'code' => 'TO_01',
                'name' => 'Tổ 01',
                'type' => 'team',
            ],
            [
                'code' => 'TO_02',
                'name' => 'Tổ 02',
                'type' => 'team',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
