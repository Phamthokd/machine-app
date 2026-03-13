<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditPhongMauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'messages.audit_template_phong_mau'],
            [
                'department_name' => 'Phòng mẫu',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 10; $i++) {
            $criteria[] = "messages.audit_pm_q$i";
        }

        foreach ($criteria as $index => $content) {
            AuditCriterion::updateOrCreate(
                [
                    'audit_template_id' => $template->id,
                    'content' => $content,
                ],
                [
                    'order_num' => $index + 1,
                ]
            );
        }
    }
}
