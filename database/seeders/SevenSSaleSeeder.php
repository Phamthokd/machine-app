<?php

namespace Database\Seeders;

use App\Models\SevenSChecklist;
use Illuminate\Database\Seeder;

class SevenSSaleSeeder extends Seeder
{
    public function run(): void
    {
        SevenSChecklist::where('department', 'Sale')->delete();

        $items = [
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 1, 'content' => 'messages.seven_s_sale_q1'],
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 2, 'content' => 'messages.seven_s_sale_q2'],
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 3, 'content' => 'messages.seven_s_sale_q3'],
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 4, 'content' => 'messages.seven_s_sale_q4'],
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 5, 'content' => 'messages.seven_s_sale_q5'],
            ['section' => 'messages.seven_s_sale_section_1', 'sort_order' => 6, 'content' => 'messages.seven_s_sale_q6'],

            ['section' => 'messages.seven_s_sale_section_2', 'sort_order' => 7, 'content' => 'messages.seven_s_sale_q7'],
            ['section' => 'messages.seven_s_sale_section_2', 'sort_order' => 8, 'content' => 'messages.seven_s_sale_q8'],
            ['section' => 'messages.seven_s_sale_section_2', 'sort_order' => 9, 'content' => 'messages.seven_s_sale_q9'],
            ['section' => 'messages.seven_s_sale_section_2', 'sort_order' => 10, 'content' => 'messages.seven_s_sale_q10'],

            ['section' => 'messages.seven_s_sale_section_3', 'sort_order' => 11, 'content' => 'messages.seven_s_sale_q11'],
            ['section' => 'messages.seven_s_sale_section_3', 'sort_order' => 12, 'content' => 'messages.seven_s_sale_q12'],
        ];

        foreach ($items as $item) {
            SevenSChecklist::create([
                'department' => 'Sale',
                'section' => $item['section'],
                'sort_order' => $item['sort_order'],
                'content' => $item['content'],
            ]);
        }
    }
}
