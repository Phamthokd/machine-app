<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditSaleDonHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'messages.audit_template_sale_don_hang'],
            [
                'department_name' => 'Sale + Đơn hàng',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 7; $i++) {
            $criteria[] = "messages.audit_sdh_q$i";
        }

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
