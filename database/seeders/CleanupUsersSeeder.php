<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CleanupUsersSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Ensure Roles Exist
        $roles = ['admin', 'warehouse', 'team_leader', 'repair_tech'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Delete all users except admin
        // Note: We delete users that are NOT 'admin'. 
        // We will then separate the update/create of admin.
        User::where('username', '!=', 'admin')->delete();

        // 3. Ensure Admin Exists
        $admin = User::firstOrNew(['username' => 'admin']);
        $admin->name = 'PVH Admin';
        $admin->email = 'admin@pvh.com';
        if (!$admin->exists) {
            $admin->password = Hash::make('admin123');
        }
        $admin->save();

        // 4. Assign Role
        $admin->syncRoles(['admin']);

        // Explicitly delete any other admin users if they crept in (e.g. by email)
        // User::where('id', '!=', $admin->id)->delete(); 
        
        Schema::enableForeignKeyConstraints();
        
        $this->command->info('Database cleaned. Only "admin" user remains.');
    }
}
