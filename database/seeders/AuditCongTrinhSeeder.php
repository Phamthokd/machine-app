<?php
  
namespace Database\Seeders;
  
use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
  
class AuditCongTrinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['department_name' => 'Công trình + cơ điện'],
            [
                'name' => 'messages.audit_template_cong_trinh',
                'is_active' => true,
            ]
        );
  
        $criteria = [];
        for ($i = 1; $i <= 5; $i++) {
            $criteria[] = "messages.audit_ct_q1"; // Wait, I made a mistake here in my thought process, should be q$i
        }
        // Correcting criteria names
        $criteria = [
            'messages.audit_ct_q1',
            'messages.audit_ct_q2',
            'messages.audit_ct_q3',
            'messages.audit_ct_q4',
        ];

        // Delete existing criteria for this template to ensure only new ones exist
        AuditCriterion::where('audit_template_id', $template->id)->delete();
  
        foreach ($criteria as $index => $content) {
            AuditCriterion::create([
                'audit_template_id' => $template->id,
                'content' => $content,
                'order_num' => $index + 1
            ]);
        }
    }
}
