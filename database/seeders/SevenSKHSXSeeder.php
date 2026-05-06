<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSKHSXSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'KHSX')->delete();

        $items = [
            ['section' => 'messages.seven_s_khsx_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_khsx_q1'],
            ['section' => 'messages.seven_s_khsx_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_khsx_q2'],
            ['section' => 'messages.seven_s_khsx_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_khsx_q3'],
            ['section' => 'messages.seven_s_khsx_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_khsx_q4'],
            ['section' => 'messages.seven_s_khsx_section_2', 'sort_order' => 5, 'content' => 'messages.seven_s_khsx_q5'],
            ['section' => 'messages.seven_s_khsx_section_2', 'sort_order' => 6, 'content' => 'messages.seven_s_khsx_q6'],
            ['section' => 'messages.seven_s_khsx_section_3', 'sort_order' => 7, 'content' => 'messages.seven_s_khsx_q7'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'KHSX',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
