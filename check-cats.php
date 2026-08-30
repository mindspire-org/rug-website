<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$cats = Illuminate\Support\Facades\DB::table('categories')->get();
foreach($cats as $c) {
    echo $c->id . ': ' . $c->name . PHP_EOL;
}
echo 'Total categories: ' . count($cats) . PHP_EOL;
