<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);

        $user = User::firstOrNew(['username' => 'admin']);
        $user->name = 'Administrator';
        $user->email = 'admin@example.com';
        $user->password = Hash::make('admin123');
        $user->is_active = true;
        $user->save();

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->command->info("Admin user created successfully!");
        $this->command->info("Username: admin");
        $this->command->info("Password: admin123");
    }
}
