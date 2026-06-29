<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSCongTrinhCoDienSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Công trình + cơ điện')->delete();

        $items = [
            ['section' => 'messages.seven_s_ctcd_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_ctcd_q1'],
            ['section' => 'messages.seven_s_ctcd_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_ctcd_q2'],
            ['section' => 'messages.seven_s_ctcd_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_ctcd_q3'],

            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 4, 'content' => 'messages.seven_s_ctcd_q4'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_ctcd_q5'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_ctcd_q6'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_ctcd_q7'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 8, 'content' => 'messages.seven_s_ctcd_q8'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_ctcd_q9'],
            ['section' => 'messages.seven_s_ctcd_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_ctcd_q10'],

            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 11, 'content' => 'messages.seven_s_ctcd_q11'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 12, 'content' => 'messages.seven_s_ctcd_q12'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 13, 'content' => 'messages.seven_s_ctcd_q13'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 14, 'content' => 'messages.seven_s_ctcd_q14'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 15, 'content' => 'messages.seven_s_ctcd_q15'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 16, 'content' => 'messages.seven_s_ctcd_q16'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_ctcd_q17'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_ctcd_q18'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 19, 'content' => 'messages.seven_s_ctcd_q19'],
            ['section' => 'messages.seven_s_ctcd_section_3', 'sort_order' => 20, 'content' => 'messages.seven_s_ctcd_q20'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Công trình + cơ điện',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
