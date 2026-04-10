<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\FeatureAccess;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'managed_department',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdminUser(): bool
    {
        return FeatureAccess::allows($this, 'admin');
    }

    public function isTeamLeaderUser(): bool
    {
        return FeatureAccess::allows($this, 'team_leader');
    }

    public function isContractorUser(): bool
    {
        return FeatureAccess::allows($this, 'contractor');
    }

    public function canAccessAuditModule(): bool
    {
        return FeatureAccess::allows($this, 'audits.access');
    }

    public function canManageAuditModule(): bool
    {
        return FeatureAccess::allows($this, 'audits.manage');
    }

    public function canAccessSevenSModule(): bool
    {
        return FeatureAccess::allows($this, 'seven_s.access');
    }

    public function canManageSevenSModule(): bool
    {
        return FeatureAccess::allows($this, 'seven_s.manage');
    }

    public function canAccessEnvironmentReports(): bool
    {
        return FeatureAccess::allows($this, 'environment_reports.access');
    }

    public function canManageRepairs(): bool
    {
        return FeatureAccess::allows($this, 'repairs.manage');
    }

    public function canViewRepairs(): bool
    {
        return FeatureAccess::allows($this, 'repairs.view');
    }

    public function canViewContractorRepairs(): bool
    {
        return FeatureAccess::allows($this, 'repairs.contractor');
    }

    public function canMoveMachines(): bool
    {
        return FeatureAccess::allows($this, 'machines.move');
    }

    public function canViewMovementHistory(): bool
    {
        return FeatureAccess::allows($this, 'movement_history.view');
    }

    public function canManageMachines(): bool
    {
        return FeatureAccess::allows($this, 'machines.manage');
    }

    public function canImportMachines(): bool
    {
        return FeatureAccess::allows($this, 'machines.import_csv');
    }

    public function canManageUsers(): bool
    {
        return FeatureAccess::allows($this, 'users.manage');
    }

    public function canViewUsers(): bool
    {
        return FeatureAccess::allows($this, 'users.view');
    }

    public function belongsToDepartment(?string $departmentName): bool
    {
        return FeatureAccess::belongsToDepartment($this, $departmentName);
    }
}
