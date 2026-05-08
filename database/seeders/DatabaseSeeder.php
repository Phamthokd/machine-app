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
            SevenSDonHangSeeder::class,
            SevenSXnkSeeder::class,
            SevenSIESeeder::class,
            SevenSBtpSeeder::class,
            SevenSKHSXSeeder::class,
            SevenSKeToanSeeder::class,
            SevenSQASeeder::class,
            SevenSTheuSeeder::class,
            SevenSSuaMaySeeder::class,
            SevenSNhaGiatSeeder::class,
            SevenSNhaCatSeeder::class,
            SevenSKhoCoKhiSeeder::class,
            SevenSKhoVaiSeeder::class,
            AuditBtpSeeder::class,
            AuditXuong6Tang1Seeder::class,
            AuditXuong6Tang2Seeder::class,
            AuditTheuSeeder::class,
            AuditMayLapTrinhSeeder::class,
            AuditKeToanSeeder::class,
            AuditSaleSeeder::class,
            AuditDonHangSeeder::class,
            AuditKhoVaiSeeder::class,
            AuditKhoPhuLieuSeeder::class,
            AuditNhaCatSeeder::class,
            AuditNhaGiatSeeder::class,
            AuditThongKeTongSeeder::class,
            AuditIESeeder::class,
            AuditHanhChinhSeeder::class,
            AuditKHSXSeeder::class,
            AuditPhongMauSeeder::class,
            AuditQASeeder::class,
            CleanupSaleDonHangSeeder::class,
        ]);
    }
}

