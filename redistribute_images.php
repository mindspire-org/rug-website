<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .warn{color:orange;} </style>";
echo "<h2>Redistribute Images to All Products</h2>";

// Get all valid image files
$imageDir = __DIR__ . '/storage/app/public/products';
$files = [];
if (is_dir($imageDir)) {
    foreach (glob($imageDir . '/*') as $f) {
        if (is_file($f) && filesize($f) > 100) {
            $mime = mime_content_type($f);
            if (strpos($mime, 'image/') === 0) {
                $files[] = 'products/' . basename($f);
            }
        }
    }
}

$products = Product::orderBy('id')->get();
$totalProducts = $products->count();
$totalImages = count($files);

echo "<p>Products: <b>$totalProducts</b></p>";
echo "<p>Available images: <b>$totalImages</b></p>";
echo "<p>Images per product (round-robin): ~" . round($totalImages / $totalProducts, 2) . "</p>";

if (!isset($_GET['confirm'])) {
    echo "<p><a href='?confirm=1' style='padding:12px 24px;background:#22c55e;color:white;text-decoration:none;border-radius:6px;font-weight:bold;'>✅ CONFIRM & REDISTRIBUTE</a></p>";
    echo "<p>This will assign at least 1 image to every product, cycling through available images.</p>";
    exit;
}

echo "<hr><h3>Executing...</h3>";

DB::beginTransaction();

try {
    // First, delete all existing ProductImage records
    ProductImage::query()->delete();
    echo "<p>Cleared old image records.</p>";

    // Round-robin assign images to products
    $imgIndex = 0;
    foreach ($products as $product) {
        $path = $files[$imgIndex % $totalImages];

        ProductImage::create([
            'product_id' => $product->id,
            'path' => $path,
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        if ($imgIndex < 20) {
            echo "<p class='ok'>✅ {$product->name} → {$path}</p>";
        }
        $imgIndex++;
    }

    DB::commit();
    echo "<hr><p class='ok'><b>Done!</b> All $totalProducts products now have an image.</p>";
    echo "<p><a href='/' style='padding:10px 20px;background:#3b82f6;color:white;text-decoration:none;border-radius:6px;'>← Go to Shop</a></p>";
} catch (Exception $e) {
    DB::rollBack();
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
