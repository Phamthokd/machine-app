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
            'bÃ¡n thÃ nh pháº©m' => 'Bán thành phẩm',
            'bán thành phẩm' => 'Bán thành phẩm',

            'phÃ²ng máº«u' => 'Phòng mẫu',
            'phòng mẫu' => 'Phòng mẫu',

            'kiá»ƒm váº£i' => 'Kiểm vải',
            'kiểm vải' => 'Kiểm vải',

            'thu mua' => 'Thu mua',

            'kho cÆ¡ khÃ­' => 'Kho cơ khí',
            'kho cơ khí' => 'Kho cơ khí',

            'kho váº£i' => 'Kho vải',
            'kho váº£i + pl' => 'Kho vải',
            'kho vải' => 'Kho vải',
            'kho vải + pl' => 'Kho vải',

            'kho phá»¥ liá»‡u' => 'Kho phụ liệu',
            'kho phụ liệu' => 'Kho phụ liệu',

            'cÃ´ng trÃ¬nh + cÆ¡ Ä‘iá»‡n' => 'Công trình + cơ điện',
            'công trình + cơ điện' => 'Công trình + cơ điện',

            'phÃ²ng thÃ­ nghiá»‡m' => 'Phòng thí nghiệm',
            'phòng thí nghiệm' => 'Phòng thí nghiệm',

            'nhÃ¢n quyá» n' => 'Nhân quyền',
            'nhân quyền' => 'Nhân quyền',

            'nhÃ¢n sá»±' => 'Nhân sự',
            'nhân sự' => 'Nhân sự',

            'hÃ nh chÃ­nh' => 'Hành chính',
            'hành chính' => 'Hành chính',

            'it' => 'IT',

            'sá»­a mÃ¡y' => 'Sửa máy',
            'sửa máy' => 'Sửa máy',

            'xÆ°á»Ÿng 6 táº§ng 1' => 'Xưởng 6 Tầng 1',
            'xuá»‘ng 6 táº§ng 1' => 'Xưởng 6 Tầng 1',
            'xưởng 6 tầng 1' => 'Xưởng 6 Tầng 1',
            'xuống 6 tầng 1' => 'Xưởng 6 Tầng 1',

            'xÆ°á»Ÿng 6 táº§ng 2' => 'Xưởng 6 Tầng 2',
            'xuá»‘ng 6 táº§ng 2' => 'Xưởng 6 Tầng 2',
            'xưởng 6 tầng 2' => 'Xưởng 6 Tầng 2',
            'xuống 6 tầng 2' => 'Xưởng 6 Tầng 2',

            'thá»‘ng kÃª tá»•ng' => 'Thống kê tổng',
            'thống kê tổng' => 'Thống kê tổng',

            'ie' => 'IE',
            'khsx' => 'KHSX',
            'audit' => 'Audit',
            'bảo vệ' => 'Bảo vệ',
            'kho tồn lỗi' => 'Kho tồn lỗi',
            'nhà cắt' => 'Nhà cắt',
            'nhà giặt' => 'Nhà giặt',
        ];

        return $map[$name] ?? $name;
    }

    /**
     * Get all possible database representations/aliases for a list of managed departments.
     */
    public static function getDepartmentQueryNames(array $managedDepartments): array
    {
        $normalized = array_map(fn($d) => self::normalizeDepartmentName($d), $managedDepartments);
        $normalized = array_filter($normalized);

        $result = [];
        foreach ($normalized as $norm) {
            $result[] = $norm;
            
            // To ensure all aliases are loaded matching the normalizations
            $map = [
                'xnk' => 'XNK',
                'btp' => 'Bán thành phẩm',
                'bÃ¡n thÃ nh pháº©m' => 'Bán thành phẩm',
                'bán thành phẩm' => 'Bán thành phẩm',
                'phÃ²ng máº«u' => 'Phòng mẫu',
                'phòng mẫu' => 'Phòng mẫu',
                'kiá»ƒm váº£i' => 'Kiểm vải',
                'kiểm vải' => 'Kiểm vải',
                'thu mua' => 'Thu mua',
                'kho cÆ¡ khÃ­' => 'Kho cơ khí',
                'kho cơ khí' => 'Kho cơ khí',
                'kho váº£i' => 'Kho vải',
                'kho váº£i + pl' => 'Kho vải',
                'kho vải' => 'Kho vải',
                'kho vải + pl' => 'Kho vải',
                'kho phá»¥ liá»‡u' => 'Kho phụ liệu',
                'kho phụ liệu' => 'Kho phụ liệu',
                'cÃ´ng trÃ¬nh + cÆ¡ Ä‘iá»‡n' => 'Công trình + cơ điện',
                'công trình + cơ điện' => 'Công trình + cơ điện',
                'phÃ²ng thÃ­ nghiá»‡m' => 'Phòng thí nghiệm',
                'phòng thí nghiệm' => 'Phòng thí nghiệm',
                'nhÃ¢n quyá» n' => 'Nhân quyền',
                'nhân quyền' => 'Nhân quyền',
                'nhÃ¢n sá»±' => 'Nhân sự',
                'nhân sự' => 'Nhân sự',
                'hÃ nh chÃ­nh' => 'Hành chính',
                'hành chính' => 'Hành chính',
                'it' => 'IT',
                'sá»­a mÃ¡y' => 'Sửa máy',
                'sửa máy' => 'Sửa máy',
                'xÆ°á»Ÿng 6 táº§ng 1' => 'Xưởng 6 Tầng 1',
                'xuá»‘ng 6 táº§ng 1' => 'Xưởng 6 Tầng 1',
                'xưởng 6 tầng 1' => 'Xưởng 6 Tầng 1',
                'xuống 6 tầng 1' => 'Xưởng 6 Tầng 1',
                'xÆ°á»Ÿng 6 táº§ng 2' => 'Xưởng 6 Tầng 2',
                'xuá»‘ng 6 táº§ng 2' => 'Xưởng 6 Tầng 2',
                'xưởng 6 tầng 2' => 'Xưởng 6 Tầng 2',
                'xuống 6 tầng 2' => 'Xưởng 6 Tầng 2',
                'thá»‘ng kÃª tá»•ng' => 'Thống kê tổng',
                'thống kê tổng' => 'Thống kê tổng',
                'ie' => 'IE',
                'khsx' => 'KHSX',
                'audit' => 'Audit',
                'bảo vệ' => 'Bảo vệ',
                'kho tồn lỗi' => 'Kho tồn lỗi',
                'nhà cắt' => 'Nhà cắt',
                'nhà giặt' => 'Nhà giặt',
            ];

            foreach ($map as $key => $val) {
                if (self::normalizeDepartmentName($val) === self::normalizeDepartmentName($norm) ||
                    self::normalizeDepartmentName($key) === self::normalizeDepartmentName($norm)) {
                    $result[] = $val;
                    $result[] = $key;
                    $result[] = strtoupper($key);
                    $result[] = ucfirst($key);
                }
            }
        }

        return array_values(array_unique(array_filter($result)));
    }
}
