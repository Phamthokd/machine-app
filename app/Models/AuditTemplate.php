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
            'xnk' => 'xnk',
            'btp' => 'btp',
            'bán thành phẩm' => 'btp',
            'phòng mẫu' => 'phong mau',
            'kiểm vải' => 'kiem vai',
            'thu mua' => 'thu mua',
            'kho cơ khí' => 'kho co khi',
            'công trình + cơ điện' => 'cong trinh + co dien',
            'phòng thí nghiệm' => 'phong thi nghiem',
            'nhân quyền' => 'nhan quyen',
            'nhân sự' => 'nhan su',
            'hành chính' => 'hanh chinh',
            'it' => 'it',
            'sửa máy' => 'sua may',
            'xưởng 6 tầng 1' => 'xuong 6 tang 1',
            'xưởng 6 tầng 2' => 'xuong 6 tang 2',
        ];

        return $map[$name] ?? $name;
    }
}
