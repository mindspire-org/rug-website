<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Product::first();
echo $p->primary_image_url . "\n";
echo "File exists: " . (file_exists("public/storage/" . basename($p->primary_image_url)) ? "YES" : "NO") . "\n";
