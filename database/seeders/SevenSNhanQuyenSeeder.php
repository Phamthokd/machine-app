<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSNhanQuyenSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Nhân quyền')->delete();

        $items = [
            // Section 1: S1 & S2 & S3
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_nhan_quyen_q1'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_nhan_quyen_q2'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_nhan_quyen_q3'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_nhan_quyen_q4'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 5, 'content' => 'messages.seven_s_nhan_quyen_q5'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 6, 'content' => 'messages.seven_s_nhan_quyen_q6'],
            ['section' => 'messages.seven_s_nhan_quyen_section_1', 'sort_order' => 7, 'content' => 'messages.seven_s_nhan_quyen_q7'],

            // Section 2: S4 & S5
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 8, 'content' => 'messages.seven_s_nhan_quyen_q8'],
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_nhan_quyen_q9'],
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_nhan_quyen_q10'],
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 11, 'content' => 'messages.seven_s_nhan_quyen_q11'],
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 12, 'content' => 'messages.seven_s_nhan_quyen_q12'],
            ['section' => 'messages.seven_s_nhan_quyen_section_2', 'sort_order' => 13, 'content' => 'messages.seven_s_nhan_quyen_q13'],

            // Section 3: S6 & S7
            ['section' => 'messages.seven_s_nhan_quyen_section_3', 'sort_order' => 14, 'content' => 'messages.seven_s_nhan_quyen_q14'],
            ['section' => 'messages.seven_s_nhan_quyen_section_3', 'sort_order' => 15, 'content' => 'messages.seven_s_nhan_quyen_q15'],
            ['section' => 'messages.seven_s_nhan_quyen_section_3', 'sort_order' => 16, 'content' => 'messages.seven_s_nhan_quyen_q16'],
            ['section' => 'messages.seven_s_nhan_quyen_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_nhan_quyen_q17'],
            ['section' => 'messages.seven_s_nhan_quyen_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_nhan_quyen_q18'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Nhân quyền',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
