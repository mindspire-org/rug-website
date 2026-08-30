<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Product;
use App\Models\ProductImage;

$totalProducts = Product::count();
$totalImages = ProductImage::count();
$productsWithImages = Product::has('images')->count();
$productsWithoutImages = Product::doesntHave('images')->count();

$validCount = 0;
$invalidCount = 0;
$imgs = ProductImage::all();
foreach ($imgs as $img) {
    $f = "storage/app/public/" . $img->path;
    if (file_exists($f) && filesize($f) > 100) { $validCount++; }
    else { $invalidCount++; }
}

echo "Products total: $totalProducts\n";
echo "ProductImages total: $totalImages\n";
echo "Products WITH images relation: $productsWithImages\n";
echo "Products WITHOUT images relation: $productsWithoutImages\n";
echo "Valid image files: $validCount\n";
echo "Invalid/broken records: $invalidCount\n";
