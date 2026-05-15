<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSAuditSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Audit')->delete();

        $items = [
            ['section' => 'messages.seven_s_audit_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_audit_q1'],
            ['section' => 'messages.seven_s_audit_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_audit_q2'],
            ['section' => 'messages.seven_s_audit_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_audit_q3'],
            ['section' => 'messages.seven_s_audit_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_audit_q4'],

            ['section' => 'messages.seven_s_audit_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_audit_q5'],
            ['section' => 'messages.seven_s_audit_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_audit_q6'],
            ['section' => 'messages.seven_s_audit_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_audit_q7'],

            ['section' => 'messages.seven_s_audit_section_3', 'sort_order' => 8, 'content' => 'messages.seven_s_audit_q8'],
            ['section' => 'messages.seven_s_audit_section_3', 'sort_order' => 9, 'content' => 'messages.seven_s_audit_q9'],
            ['section' => 'messages.seven_s_audit_section_3', 'sort_order' => 10, 'content' => 'messages.seven_s_audit_q10'],
            ['section' => 'messages.seven_s_audit_section_3', 'sort_order' => 11, 'content' => 'messages.seven_s_audit_q11'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Audit',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
