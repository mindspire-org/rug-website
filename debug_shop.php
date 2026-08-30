<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Product;

$p = Product::with('primaryImage')->first();
echo "Product: " . $p->name . "\n";
echo "primaryImage loaded: " . ($p->relationLoaded('primaryImage') ? 'YES' : 'NO') . "\n";
echo "primaryImage is null: " . ($p->primaryImage === null ? 'YES' : 'NO') . "\n";
if ($p->primaryImage) {
    echo "primaryImage path: " . $p->primaryImage->path . "\n";
}
echo "primary_image_url: " . $p->primary_image_url . "\n";
echo "images count: " . $p->images()->count() . "\n";
