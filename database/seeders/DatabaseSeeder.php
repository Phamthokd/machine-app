<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            MachineSeeder::class,
            AuditBtpSeeder::class,
            AuditXuong6Tang1Seeder::class,
            AuditXuong6Tang2Seeder::class,
            AuditTheuSeeder::class,

        ]);
    }
}
