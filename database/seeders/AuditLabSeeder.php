<?php
  
namespace Database\Seeders;
  
use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
  
class AuditLabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['department_name' => 'Phòng thí nghiệm'],
            [
                'name' => 'messages.audit_template_lab',
                'is_active' => true,
            ]
        );
  
        $criteria = [];
        for ($i = 1; $i <= 12; $i++) {
            $criteria[] = "messages.audit_lab_q$i";
        }

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
