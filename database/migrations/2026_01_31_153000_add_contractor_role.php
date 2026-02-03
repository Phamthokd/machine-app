<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        if (!Role::where('name', 'contractor')->exists()) {
            Role::create(['name' => 'contractor']);
        }
    }

    public function down(): void
    {
        // Don't remove roles in down to prevent data loss or breakage
    }
};
