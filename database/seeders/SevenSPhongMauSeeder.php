<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSPhongMauSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Phòng mẫu')->delete();

        $items = [
            // S1 & S2 & S3
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_phong_mau_q1'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_phong_mau_q2'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_phong_mau_q3'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_phong_mau_q4'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 5, 'content' => 'messages.seven_s_phong_mau_q5'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 6, 'content' => 'messages.seven_s_phong_mau_q6'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 7, 'content' => 'messages.seven_s_phong_mau_q7'],
            ['section' => 'messages.seven_s_phong_mau_section_1', 'sort_order' => 8, 'content' => 'messages.seven_s_phong_mau_q8'],

            // S4 & S5
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_phong_mau_q9'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_phong_mau_q10'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 11, 'content' => 'messages.seven_s_phong_mau_q11'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 12, 'content' => 'messages.seven_s_phong_mau_q12'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 13, 'content' => 'messages.seven_s_phong_mau_q13'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 14, 'content' => 'messages.seven_s_phong_mau_q14'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 15, 'content' => 'messages.seven_s_phong_mau_q15'],
            ['section' => 'messages.seven_s_phong_mau_section_2', 'sort_order' => 16, 'content' => 'messages.seven_s_phong_mau_q16'],

            // S6 & S7
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_phong_mau_q17'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_phong_mau_q18'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 19, 'content' => 'messages.seven_s_phong_mau_q19'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 20, 'content' => 'messages.seven_s_phong_mau_q20'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 21, 'content' => 'messages.seven_s_phong_mau_q21'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 22, 'content' => 'messages.seven_s_phong_mau_q22'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 23, 'content' => 'messages.seven_s_phong_mau_q23'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 24, 'content' => 'messages.seven_s_phong_mau_q24'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 25, 'content' => 'messages.seven_s_phong_mau_q25'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 26, 'content' => 'messages.seven_s_phong_mau_q26'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 27, 'content' => 'messages.seven_s_phong_mau_q27'],
            ['section' => 'messages.seven_s_phong_mau_section_3', 'sort_order' => 28, 'content' => 'messages.seven_s_phong_mau_q28'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Phòng mẫu',
                'section'    => $item['section'],
                'sort_order' => $item['sort_order'],
                'content'    => $item['content'],
            ]);
        }
    }
}
