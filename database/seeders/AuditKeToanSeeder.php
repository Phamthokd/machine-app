<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditKeToanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::firstOrCreate(
            ['name' => 'messages.audit_template_ke_toan'],
            [
                'department_name' => 'Kế toán',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 10; $i++) {
            $criteria[] = "messages.audit_kt_q$i";
        }

        // Clean up any extra lingering criteria not in the above list
        AuditCriterion::where('audit_template_id', $template->id)
                      ->whereNotIn('content', $criteria)
                      ->delete();

        foreach ($criteria as $index => $content) {
            AuditCriterion::updateOrCreate(
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
