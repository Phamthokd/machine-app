<?php
  
namespace Database\Seeders;
  
use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
  
class AuditSuaMaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = AuditTemplate::updateOrCreate(
            ['department_name' => 'Sửa máy'],
            [
                'name' => 'messages.audit_template_sua_may',
                'is_active' => true,
            ]
        );
  
        $criteria = [
            'messages.audit_sua_may_q1',
            'messages.audit_sua_may_q2',
            'messages.audit_sua_may_q3',
            'messages.audit_sua_may_q4',
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
