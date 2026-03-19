<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App::setLocale('vi');
echo "VI: " . __('messages.XNK') . "\n";
App::setLocale('en');
echo "EN: " . __('messages.XNK') . "\n";
App::setLocale('zh');
echo "ZH: " . __('messages.XNK') . "\n";
