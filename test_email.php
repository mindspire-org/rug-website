<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email from Costikyan - ' . now(), function ($message) {
        $message->to('test@example.com')
            ->subject('Hostinger Mail Test');
    });
    echo "SUCCESS: Mail sent (check logs if not delivered)\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
