<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AuditTemplate;

$ts = AuditTemplate::all();
foreach($ts as $t) {
    echo "ID: {$t->id}, Name: {$t->name}, Dept: {$t->department_name}\n";
}
