<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSKhoTonLoiSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Kho tồn lỗi')->delete();

        $items = [
            ['section' => 'messages.seven_s_kho_ton_loi_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_kho_ton_loi_q1'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_kho_ton_loi_q2'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_kho_ton_loi_q3'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_kho_ton_loi_q4'],

            ['section' => 'messages.seven_s_kho_ton_loi_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_kho_ton_loi_q5'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_kho_ton_loi_q6'],

            ['section' => 'messages.seven_s_kho_ton_loi_section_3', 'sort_order' => 7, 'content' => 'messages.seven_s_kho_ton_loi_q7'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_3', 'sort_order' => 8, 'content' => 'messages.seven_s_kho_ton_loi_q8'],
            ['section' => 'messages.seven_s_kho_ton_loi_section_3', 'sort_order' => 9, 'content' => 'messages.seven_s_kho_ton_loi_q9'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Kho tồn lỗi',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}

