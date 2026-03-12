<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AuditTemplate;
use App\Models\AuditCriterion;

// Find the template with the hardcoded Vietnamese name (ID 2 in our check)
$tOld = AuditTemplate::where('name', 'Đánh giá bộ phận Phòng mẫu')->first();
if($tOld) {
    AuditCriterion::where('audit_template_id', $tOld->id)->delete();
    $tOld->delete();
    echo "Deleted old hardcoded template ID: {$tOld->id}\n";
}

// Find the template with the translation key (seeded by our updated seeder)
$tNew = AuditTemplate::where('name', 'messages.audit_template_phong_mau')->first();
if($tNew) {
    echo "Found new template ID: {$tNew->id}\n";
    $count = AuditCriterion::where('audit_template_id', $tNew->id)->count();
    echo "Criteria Count: $count\n";
    
    $criteria = AuditCriterion::where('audit_template_id', $tNew->id)->orderBy('order_num')->get();
    foreach($criteria as $c) {
        $raw = $c->getRawOriginal('content');
        echo "Order: {$c->order_num}, Raw: {$raw}\n";
    }
} else {
    echo "New template not found! Something went wrong.\n";
}
