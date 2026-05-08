<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditKhoPhuLieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['name' => 'messages.audit_template_kho_phu_lieu'],
            [
                'department_name' => 'Kho phụ liệu',
                'is_active' => true,
            ]
        );

        $criteria = [];
        for ($i = 1; $i <= 9; $i++) {
            $criteria[] = "messages.audit_kpl_q$i";
        }

        AuditCriterion::where('audit_template_id', $template->id)->delete();

        foreach ($criteria as $index => $content) {
            AuditCriterion::create([
                'audit_template_id' => $template->id,
                'content' => $content,
                'order_num' => $index + 1,
            ]);
        }
    }
}
