<?php

// Security token - delete this file immediately after use
$token = $_GET['token'] ?? '';
if ($token !== 'run_migrate_2024') {
    die('Unauthorized');
}

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('migrate', ['--force' => true]);

echo "<pre>";
echo $kernel->output();
echo "Exit status: " . $status;
echo "</pre>";