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
            'audits.access' => $user->hasRole('admin') || $user->hasRole('audit') || $user->can('audits.access'),
            'audits.manage' => self::allows($user, 'audits.access') && empty($user->managed_department),
            'seven_s.access' => $user->hasRole('admin') || $user->hasRole('7s') || $user->can('seven_s.access'),
            'seven_s.manage' => self::allows($user, 'seven_s.access') && empty($user->managed_department),
            'environment_reports.access' => $user->hasAnyRole(['admin', 'environment']) || $user->can('environment_reports.access'),
            'repairs.manage' => $user->hasAnyRole(['admin', 'warehouse', 'repair_tech', 'contractor', 'team_leader']) || $user->can('repairs.manage'),
            'repairs.view' => $user->hasAnyRole(['admin', 'warehouse', 'repair_tech', 'contractor', 'team_leader', 'audit', '7s']) || $user->can('repairs.view'),
            'repairs.contractor' => $user->hasAnyRole(['admin', 'warehouse', 'contractor', 'audit', '7s']) || $user->can('repairs.contractor'),
            'machines.move' => $user->hasAnyRole(['admin', 'warehouse', 'team_leader']) || $user->can('machines.move'),
            'movement_history.view' => $user->hasAnyRole(['admin', 'warehouse', 'team_leader', 'audit', '7s']) || $user->can('movement_history.view'),
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

        $userDepartment = AuditTemplate::normalizeDepartmentName($user->managed_department);
        $targetDepartment = AuditTemplate::normalizeDepartmentName($departmentName);

        return !empty($userDepartment) && !empty($targetDepartment) && $userDepartment === $targetDepartment;
    }
}
