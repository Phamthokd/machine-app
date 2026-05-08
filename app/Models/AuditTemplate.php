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
            'btp' => 'BÃ¡n thÃ nh pháº©m',
            'bÃ¡n thÃ nh pháº©m' => 'BÃ¡n thÃ nh pháº©m',
            'phÃ²ng máº«u' => 'PhÃ²ng máº«u',
            'kiá»ƒm váº£i' => 'Kiá»ƒm váº£i',
            'thu mua' => 'Thu mua',
            'kho cÆ¡ khÃ­' => 'Kho cÆ¡ khÃ­',
            'kho váº£i' => 'Kho váº£i',
            'kho váº£i + pl' => 'Kho váº£i',
            'kho phá»¥ liá»‡u' => 'Kho phá»¥ liá»‡u',
            'cÃ´ng trÃ¬nh + cÆ¡ Ä‘iá»‡n' => 'CÃ´ng trÃ¬nh + cÆ¡ Ä‘iá»‡n',
            'phÃ²ng thÃ­ nghiá»‡m' => 'PhÃ²ng thÃ­ nghiá»‡m',
            'nhÃ¢n quyá»n' => 'NhÃ¢n quyá»n',
            'nhÃ¢n sá»±' => 'NhÃ¢n sá»±',
            'hÃ nh chÃ­nh' => 'HÃ nh chÃ­nh',
            'it' => 'IT',
            'sá»­a mÃ¡y' => 'Sá»­a mÃ¡y',
            'xÆ°á»Ÿng 6 táº§ng 1' => 'XÆ°á»Ÿng 6 Táº§ng 1',
            'xÆ°á»Ÿng 6 táº§ng 2' => 'XÆ°á»Ÿng 6 Táº§ng 2',
            'xuá»‘ng 6 táº§ng 1' => 'XÆ°á»Ÿng 6 Táº§ng 1',
            'xuá»‘ng 6 táº§ng 2' => 'XÆ°á»Ÿng 6 Táº§ng 2',
            'thá»‘ng kÃª tá»•ng' => 'Thá»‘ng kÃª tá»•ng',
            'ie' => 'IE',
            'khsx' => 'KHSX',
        ];

        return $map[$name] ?? $name;
    }
}
