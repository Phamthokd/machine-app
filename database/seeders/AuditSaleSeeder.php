<?php
 
namespace Database\Seeders;
 
use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
 
class AuditSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['department_name' => 'Sale'],
            [
                'name' => 'messages.audit_template_sale',
                'is_active' => true,
            ]
        );
 
        $criteria = [];
        for ($i = 1; $i <= 7; $i++) {
            $criteria[] = "messages.audit_sale_q$i";
        }
 
        foreach ($criteria as $index => $content) {
            AuditCriterion::updateOrCreate(
                [
                    'audit_template_id' => $template->id,
                    'content' => $content,
                ],
                [
                    'order_num' => $index + 1,
                ]
            );
        }
    }
}
