<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditBtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'Đánh giá bộ phận BTP'],
            [
                'department_name' => 'BTP',
                'is_active' => true,
            ]
        );

        $criteria = [
            'Có phân lỗi đúng sọt hàng Ok hay không Ok hay không?',
            'Có quy định sản xuất và sử dụng dây chuyền cho từng mã hàng hay không?'
        ];

        foreach ($criteria as $index => $content) {
            AuditCriterion::firstOrCreate(
                [
                    'audit_template_id' => $template->id,
                    'content' => $content
                ],
                [
                    'order_num' => $index + 1
                ]
            );
        }
    }
}
