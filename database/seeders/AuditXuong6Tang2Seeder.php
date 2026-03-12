<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditXuong6Tang2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'messages.audit_template_x6_t2'],
            [
                'department_name' => 'Xưởng 6 Tầng 2',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 20; $i++) {
            $criteria[] = "messages.audit_x6t2_q$i";
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
