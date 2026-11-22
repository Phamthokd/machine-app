<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SevenSV2Record;
use App\Models\User;

$admin = User::where('username', 'admin')->first();
$regularUser = User::where('id', '!=', $admin->id)->first();

echo "Admin user: " . $admin->name . " (isAdmin: " . ($admin->isAdminUser() ? 'YES' : 'NO') . ")\n";
echo "Regular user: " . ($regularUser ? $regularUser->name : 'N/A') . " (isAdmin: " . ($regularUser && $regularUser->isAdminUser() ? 'YES' : 'NO') . ")\n";

// Test controller destroy policy with regular user
auth()->login($regularUser);
$controller = app(\App\Http\Controllers\SevenSV2Controller::class);
$rec = SevenSV2Record::first();

if ($rec) {
    try {
        $controller->destroy($rec->id);
        echo "ERROR: Regular user was able to delete!\n";
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        echo "SUCCESS: Regular user blocked with HTTP " . $e->getStatusCode() . " (" . $e->getMessage() . ")\n";
    }
}
