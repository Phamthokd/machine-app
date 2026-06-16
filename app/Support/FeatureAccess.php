<?php

namespace App\Support;

use App\Models\AuditTemplate;
use App\Models\User;

class FeatureAccess
{
    public static function allows(?User $user, string $feature): bool
    {
        if (!$user) {
            return false;
        }

        return match ($feature) {
            'admin' => $user->hasRole('admin'),
            'team_leader' => $user->hasRole('team_leader'),
            'contractor' => $user->hasRole('contractor'),
            'supervisor' => $user->hasRole('supervisor'),
            'senior_manager' => $user->hasRole('senior_manager') || $user->hasRole('admin'),
            'audits.access' => $user->hasRole('admin') || $user->hasRole('audit') || $user->can('audits.access'),
            'audits.manage' => self::allows($user, 'audits.access') && !$user->hasManagedDepartments(),
            'seven_s.access' => $user->hasRole('admin') || $user->hasRole('7s') || $user->can('seven_s.access'),
            'seven_s.manage' => self::allows($user, 'seven_s.access') && !$user->hasManagedDepartments(),
            'environment_reports.access' => $user->hasAnyRole(['admin', 'environment']) || $user->can('environment_reports.access'),
            'repairs.manage' => $user->hasAnyRole(['admin', 'warehouse', 'repair_tech', 'contractor', 'team_leader', 'supervisor']) || $user->can('repairs.manage'),
            'repairs.view' => $user->hasAnyRole(['admin', 'warehouse', 'repair_tech', 'contractor', 'team_leader', 'audit', '7s', 'supervisor']) || $user->can('repairs.view'),
            'repairs.contractor' => true,
            'repairs.approve' => $user->hasAnyRole(['admin', 'senior_manager']) || $user->can('repairs.approve'),
            'machines.move' => $user->hasAnyRole(['admin', 'warehouse', 'team_leader']) || $user->can('machines.move'),
            'movement_history.view' => $user->hasAnyRole(['admin', 'warehouse', 'team_leader', 'audit', '7s', 'supervisor']) || $user->can('movement_history.view'),
            'machines.manage' => $user->hasAnyRole(['admin', 'warehouse', 'audit', '7s']) || $user->can('machines.manage'),
            'machines.import_csv' => $user->hasAnyRole(['admin', 'warehouse']) || $user->can('machines.import_csv'),
            'users.manage' => $user->hasRole('admin') || $user->can('users.manage'),
            'users.view' => $user->hasRole('admin') || $user->hasRole('warehouse') || $user->can('users.view'),
            default => $user->can($feature),
        };
    }

    public static function any(?User $user, array $features): bool
    {
        foreach ($features as $feature) {
            if (self::allows($user, $feature)) {
                return true;
            }
        }

        return false;
    }

    public static function belongsToDepartment(?User $user, ?string $departmentName): bool
    {
        if (!$user) {
            return false;
        }

        return $user->managesDepartment($departmentName);
    }
}
