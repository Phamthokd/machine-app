<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTemplate extends Model
{
    protected $fillable = [
        'name',
        'department_name',
        'is_active',
    ];

    public function criteria()
    {
        return $this->hasMany(AuditCriterion::class)->orderBy('order_num');
    }

    public function records()
    {
        return $this->hasMany(AuditRecord::class);
    }

    /**
     * Normalize department names for consistent comparison and notifications.
     */
    public static function normalizeDepartmentName(?string $departmentName): ?string
    {
        if (empty($departmentName)) {
            return null;
        }

        $name = mb_strtolower(trim($departmentName));
        $map = [
            'xnk' => 'XNK',
            'btp' => 'Bán thành phẩm',
            'bán thành phẩm' => 'Bán thành phẩm',
            'phòng mẫu' => 'Phòng mẫu',
            'kiểm vải' => 'Kiểm vải',
            'thu mua' => 'Thu mua',
            'kho cơ khí' => 'Kho cơ khí',
            'công trình + cơ điện' => 'Công trình + cơ điện',
            'phòng thí nghiệm' => 'Phòng thí nghiệm',
            'nhân quyền' => 'Nhân quyền',
            'nhân sự' => 'Nhân sự',
            'hành chính' => 'Hành chính',
            'it' => 'IT',
            'sửa máy' => 'Sửa máy',
            'xưởng 6 tầng 1' => 'Xưởng 6 Tầng 1',
            'xưởng 6 tầng 2' => 'Xưởng 6 Tầng 2',
            'xuống 6 tầng 1' => 'Xưởng 6 Tầng 1',
            'xuống 6 tầng 2' => 'Xưởng 6 Tầng 2',
            'thống kê tổng' => 'Thống kê tổng',
            'ie' => 'IE',
            'khsx' => 'KHSX',
        ];

        return $map[$name] ?? $name;
    }
}
