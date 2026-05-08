<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSKhoPhuLieuSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Kho phụ liệu')->delete();

        $items = [
            ['section' => 'messages.seven_s_kho_phu_lieu_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_kho_phu_lieu_q1'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_kho_phu_lieu_q2'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_kho_phu_lieu_q3'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_kho_phu_lieu_q4'],

            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_kho_phu_lieu_q5'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_kho_phu_lieu_q6'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_kho_phu_lieu_q7'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 8, 'content' => 'messages.seven_s_kho_phu_lieu_q8'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_kho_phu_lieu_q9'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_kho_phu_lieu_q10'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_2', 'sort_order' => 11, 'content' => 'messages.seven_s_kho_phu_lieu_q11'],

            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 12, 'content' => 'messages.seven_s_kho_phu_lieu_q12'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 13, 'content' => 'messages.seven_s_kho_phu_lieu_q13'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 14, 'content' => 'messages.seven_s_kho_phu_lieu_q14'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 15, 'content' => 'messages.seven_s_kho_phu_lieu_q15'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 16, 'content' => 'messages.seven_s_kho_phu_lieu_q16'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 17, 'content' => 'messages.seven_s_kho_phu_lieu_q17'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 18, 'content' => 'messages.seven_s_kho_phu_lieu_q18'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 19, 'content' => 'messages.seven_s_kho_phu_lieu_q19'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 20, 'content' => 'messages.seven_s_kho_phu_lieu_q20'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 21, 'content' => 'messages.seven_s_kho_phu_lieu_q21'],
            ['section' => 'messages.seven_s_kho_phu_lieu_section_3', 'sort_order' => 22, 'content' => 'messages.seven_s_kho_phu_lieu_q22'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Kho phụ liệu',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}

