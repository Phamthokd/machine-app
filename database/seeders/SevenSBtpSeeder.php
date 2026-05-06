<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSBtpSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'BTP')->delete();

        $items = [
            ['section' => 'messages.seven_s_btp_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_btp_q1'],
            ['section' => 'messages.seven_s_btp_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_btp_q2'],
            ['section' => 'messages.seven_s_btp_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_btp_q3'],
            ['section' => 'messages.seven_s_btp_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_btp_q4'],

            ['section' => 'messages.seven_s_btp_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_btp_q5'],
            ['section' => 'messages.seven_s_btp_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_btp_q6'],
            ['section' => 'messages.seven_s_btp_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_btp_q7'],

            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 8, 'content' => 'messages.seven_s_btp_q8'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 9, 'content' => 'messages.seven_s_btp_q9'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 10, 'content' => 'messages.seven_s_btp_q10'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 11, 'content' => 'messages.seven_s_btp_q11'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 12, 'content' => 'messages.seven_s_btp_q12'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 13, 'content' => 'messages.seven_s_btp_q13'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 14, 'content' => 'messages.seven_s_btp_q14'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 15, 'content' => 'messages.seven_s_btp_q15'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 16, 'content' => 'messages.seven_s_btp_q16'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_btp_q17'],
            ['section' => 'messages.seven_s_btp_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_btp_q18'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'BTP',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
