<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SevenSV2Record;
use App\Models\User;

$user = User::first();
auth()->login($user);

$locales = ['vi', 'zh', 'en'];

foreach ($locales as $loc) {
    app()->setLocale($loc);
    echo "Testing locale: $loc\n";

    // Test index
    $req = new \Illuminate\Http\Request();
    $controller = app(\App\Http\Controllers\SevenSV2Controller::class);
    $viewIndex = $controller->index($req);
    $renderedIndex = $viewIndex->render();
    echo " - Index rendered successfully (" . strlen($renderedIndex) . " bytes)\n";

    // Test create
    $viewCreate = $controller->create($req);
    $renderedCreate = $viewCreate->render();
    echo " - Create rendered successfully (" . strlen($renderedCreate) . " bytes)\n";

    // Test show if record exists
    $rec = SevenSV2Record::first();
    if ($rec) {
        $viewShow = $controller->show($rec->id);
        $renderedShow = $viewShow->render();
        echo " - Show #{$rec->id} rendered successfully (" . strlen($renderedShow) . " bytes)\n";
    }
}

echo "ALL LOCALES RENDERED CLEANLY!\n";
