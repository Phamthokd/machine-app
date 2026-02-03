<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CleanupRolesSeeder extends Seeder
{
    public function run(): void
    {
        // List of roles to keep
        $keep = ['admin', 'warehouse', 'team_leader', 'repair_tech'];

        // Get all roles
        $roles = Role::all();

        foreach ($roles as $role) {
            if (!in_array($role->name, $keep)) {
                $role->delete();
                $this->command->info("Deleted role: {$role->name}");
            }
        }
        
        // Ensure the 4 roles exist
        foreach ($keep as $k) {
            Role::firstOrCreate(['name' => $k]);
        }

        $this->command->info('Roles cleaned up. Only 4 basic roles remain.');
    }
}
