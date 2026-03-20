<?php
 
namespace Database\Seeders;
 
use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;
 
class CleanupSaleDonHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Delete the old combined template
        AuditTemplate::where('department_name', 'Sale + Đơn hàng')->delete();
 
        // 2. Fix Sale template name if it has a trailing comma or incorrect translation key
        $saleTemplate = AuditTemplate::where('department_name', 'Sale')->first();
        if ($saleTemplate) {
            $saleTemplate->name = 'messages.audit_template_sale';
            $saleTemplate->save();
        }
 
        // 3. Fix Don Hang template name
        $dhTemplate = AuditTemplate::where('department_name', 'Đơn hàng')->first();
        if ($dhTemplate) {
            $dhTemplate->name = 'messages.audit_template_don_hang';
            $dhTemplate->save();
        }

        $this->command->info('Cleanup of Sale and Đơn hàng departments completed.');
    }
}
