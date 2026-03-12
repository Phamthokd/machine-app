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
            AuditMayLapTrinhSeeder::class,
            AuditKeToanSeeder::class,
            AuditSaleDonHangSeeder::class,
            AuditKhoVaiPLSeeder::class,
            AuditNhaCatSeeder::class,
            AuditNhaGiatSeeder::class,
            AuditThongKeTongSeeder::class,
            AuditIESeeder::class,
            AuditKHSXSeeder::class,
        ]);
    }
}
