<?php
 
namespace Database\Seeders;
 
use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
 
class AuditQASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['department_name' => 'QA'],
            [
                'name' => 'messages.audit_template_qa',
                'is_active' => true,
            ]
        );
 
        $criteria = [];
        for ($i = 1; $i <= 17; $i++) {
            $criteria[] = "messages.audit_qa_q$i";
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
