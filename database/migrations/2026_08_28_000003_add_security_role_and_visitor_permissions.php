<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Tạo role bảo vệ
        if (!Role::where('name', 'security')->exists()) {
            Role::create(['name' => 'security']);
        }

        // Tạo permission tạo phiếu khách (admin tích cho user)
        if (!Permission::where('name', 'visitors.create')->exists()) {
            Permission::create(['name' => 'visitors.create']);
        }

        // Tạo permission bảo vệ (gán mặc định cho role security)
        if (!Permission::where('name', 'visitors.security')->exists()) {
            Permission::create(['name' => 'visitors.security']);
        }

        // Gán permission visitors.security cho role security
        $securityRole = Role::where('name', 'security')->first();
        if ($securityRole) {
            $securityRole->givePermissionTo('visitors.security');
        }
    }

    public function down(): void
    {
        // Don't remove roles in down to prevent data loss
    }
};
