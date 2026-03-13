<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditKiemVaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'messages.audit_template_kiem_vai'],
            [
                'department_name' => 'Kiểm vải',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 11; $i++) {
            $criteria[] = "messages.audit_kv_q$i";
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
