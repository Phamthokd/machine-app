<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Tạo role 7s nếu chưa có
        Role::firstOrCreate(['name' => '7s', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Role::where('name', '7s')->delete();
    }
};
