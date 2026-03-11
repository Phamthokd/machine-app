<?php

namespace Database\Seeders;

use App\Models\AuditCriterion;
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAuditTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Update BTP
        $btpTemplate = AuditTemplate::where('department_name', 'BTP')->first();
        if ($btpTemplate) {
            $btpTemplate->name = 'messages.audit_template_btp';
            $btpTemplate->save();
            $criteria = $btpTemplate->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_btp_q' . ($index + 1);
                $criterion->save();
            }
        }

        // 2. Update Phòng Mẫu
        $pmTemplate = AuditTemplate::where('department_name', 'Phòng mẫu')->first();
        if ($pmTemplate) {
            $pmTemplate->name = 'messages.audit_template_phong_mau';
            $pmTemplate->save();
            $criteria = $pmTemplate->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_pm_q' . ($index + 1);
                $criterion->save();
            }
        }

        // 3. Update Kiểm Vải
        $kvTemplate = AuditTemplate::where('department_name', 'Kiểm vải')->first();
        if ($kvTemplate) {
            $kvTemplate->name = 'messages.audit_template_kiem_vai';
            $kvTemplate->save();
            $criteria = $kvTemplate->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_kv_q' . ($index + 1);
                $criterion->save();
            }
        }

        // 4. Update Xưởng 6 Tầng 1
        $x6t1Template = AuditTemplate::where('department_name', 'Xưởng 6 tầng 1')->first();
        if ($x6t1Template) {
            $x6t1Template->name = 'messages.audit_template_x6_t1';
            $x6t1Template->save();
            $criteria = $x6t1Template->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_x6t1_q' . ($index + 1);
                $criterion->save();
            }
        }

        // 5. Update Xưởng 6 Tầng 2
        // Handling both possible cases since I fixed it recently
        $x6t2Template = AuditTemplate::where('department_name', 'Xưởng 6 tầng 2')->orWhere('department_name', 'Xưởng 6 Tầng 2')->first();
        if ($x6t2Template) {
            $x6t2Template->name = 'messages.audit_template_x6_t2';
            $x6t2Template->save();
            $criteria = $x6t2Template->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_x6t2_q' . ($index + 1);
                $criterion->save();
            }
        }

        // 6. Update Thêu
        $theuTemplate = AuditTemplate::where('department_name', 'Thêu')->first();
        if ($theuTemplate) {
            $theuTemplate->name = 'messages.audit_template_theu';
            $theuTemplate->save();
            $criteria = $theuTemplate->criteria()->orderBy('order_num')->get();
            foreach ($criteria as $index => $criterion) {
                $criterion->content = 'messages.audit_theu_q' . ($index + 1);
                $criterion->save();
            }
        }

        $this->command->info('Audit Templates and Criteria translations updated successfully!');
    }
}
