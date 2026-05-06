<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSQASeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'QA')->delete();

        $items = [
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_qa_q1'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_qa_q2'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_qa_q3'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_qa_q4'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 5, 'content' => 'messages.seven_s_qa_q5'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 6, 'content' => 'messages.seven_s_qa_q6'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 7, 'content' => 'messages.seven_s_qa_q7'],
            ['section' => 'messages.seven_s_qa_section_1', 'sort_order' => 8, 'content' => 'messages.seven_s_qa_q8'],

            ['section' => 'messages.seven_s_qa_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_qa_q9'],
            ['section' => 'messages.seven_s_qa_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_qa_q10'],
            ['section' => 'messages.seven_s_qa_section_2', 'sort_order' => 11, 'content' => 'messages.seven_s_qa_q11'],
            ['section' => 'messages.seven_s_qa_section_2', 'sort_order' => 12, 'content' => 'messages.seven_s_qa_q12'],

            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 13, 'content' => 'messages.seven_s_qa_q13'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 14, 'content' => 'messages.seven_s_qa_q14'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 15, 'content' => 'messages.seven_s_qa_q15'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 16, 'content' => 'messages.seven_s_qa_q16'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_qa_q17'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_qa_q18'],
            ['section' => 'messages.seven_s_qa_section_3', 'sort_order' => 19, 'content' => 'messages.seven_s_qa_q19'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'QA',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
