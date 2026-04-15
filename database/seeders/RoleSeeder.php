<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'admin',
            'repair_tech',
            'warehouse',
            'team_leader',
            'audit',
            '7s',
            'contractor',
            'environment',
        ];

        // Xoá các role không nằm trong danh sách chuẩn (để dọn dẹp VPS)
        Role::whereNotIn('name', $roles)->delete();

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = collect(config('feature_permissions', []))
            ->flatMap(fn ($group) => array_keys($group['items'] ?? []))
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
