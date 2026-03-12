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
            ['name' => 'messages.audit_template_btp'],
            [
                'department_name' => 'BTP',
                'is_active' => true,
            ]
        );

        $criteriaIdx = 1;
        for ($i = 1; $i <= 11; $i++) {
            $criteria[] = "messages.audit_btp_q$i";
        }

        // Delete existing criteria for this template to ensure only new ones exist
        AuditCriterion::where('audit_template_id', $template->id)->delete();

        foreach ($criteria as $index => $content) {
            AuditCriterion::create([
                'audit_template_id' => $template->id,
                'content' => $content,
                'order_num' => $index + 1
            ]);
        }
    }
}
