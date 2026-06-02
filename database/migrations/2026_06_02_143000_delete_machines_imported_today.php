<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        // Delete related movements
        DB::delete("DELETE FROM machine_movements WHERE machine_id IN (SELECT id FROM machines WHERE DATE(created_at) = '2026-06-02');");

        // Delete related repair tickets
        DB::delete("DELETE FROM repair_tickets WHERE machine_id IN (SELECT id FROM machines WHERE DATE(created_at) = '2026-06-02');");
        
        // Delete machines created on June 2, 2026
        DB::delete("DELETE FROM machines WHERE DATE(created_at) = '2026-06-02';");

        // Enable foreign key checks
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
