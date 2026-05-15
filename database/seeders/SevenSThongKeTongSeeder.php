<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSThongKeTongSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Thống kê tổng')->delete();

        $items = [
            ['section' => 'messages.seven_s_thong_ke_tong_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_thong_ke_tong_q1'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_thong_ke_tong_q2'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_thong_ke_tong_q3'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_thong_ke_tong_q4'],

            ['section' => 'messages.seven_s_thong_ke_tong_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_thong_ke_tong_q5'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_thong_ke_tong_q6'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_thong_ke_tong_q7'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_2', 'sort_order' => 8, 'content' => 'messages.seven_s_thong_ke_tong_q8'],

            ['section' => 'messages.seven_s_thong_ke_tong_section_3', 'sort_order' => 9, 'content' => 'messages.seven_s_thong_ke_tong_q9'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_3', 'sort_order' => 10, 'content' => 'messages.seven_s_thong_ke_tong_q10'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_3', 'sort_order' => 11, 'content' => 'messages.seven_s_thong_ke_tong_q11'],
            ['section' => 'messages.seven_s_thong_ke_tong_section_3', 'sort_order' => 12, 'content' => 'messages.seven_s_thong_ke_tong_q12'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Thống kê tổng',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
