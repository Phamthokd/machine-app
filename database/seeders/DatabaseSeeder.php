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
            SevenSThuMuaSeeder::class,
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
            SevenSITSeeder::class,
            SevenSKhoVaiSeeder::class,
            SevenSKhoPhuLieuSeeder::class,
            SevenSKhoTonLoiSeeder::class,
            SevenSAuditSeeder::class,
            SevenSBaoVeSeeder::class,
            SevenSSaleSeeder::class,
            SevenSThongKeTongSeeder::class,
            SevenSHanhChinhSeeder::class,
            SevenSPhongMauSeeder::class,
            SevenSKiemVaiSeeder::class,
            SevenSCongTrinhCoDienSeeder::class,
            SevenSNhanQuyenSeeder::class,
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

