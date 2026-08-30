<?php
/**
 * Product Image Repair Tool
 * Identifies and re-downloads missing/corrupted product images
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

echo "<h2>Product Image Repair Tool</h2>";
echo "<pre>";

// Get all products with images
$products = Product::with('images')->get();
$fixed = 0;
$missing = [];

foreach ($products as $product) {
    $hasValidImage = false;
    
    foreach ($product->images as $image) {
        $fullPath = storage_path('app/public/' . $image->path);
        
        if (!file_exists($fullPath) || filesize($fullPath) < 100) {
            echo "❌ Product #{$product->id} ({$product->sku}): Invalid image {$image->path}\n";
            $missing[] = [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'image_id' => $image->id,
                'path' => $image->path
            ];
            
            // Delete invalid image record
            $image->delete();
        } else {
            $hasValidImage = true;
        }
    }
    
    if (!$hasValidImage && $product->images->count() > 0) {
        echo "⚠️ Product #{$product->id} ({$product->sku}): No valid images remaining\n";
    }
}

echo "\n=== Summary ===\n";
echo "Products checked: {$products->count()}\n";
echo "Missing/invalid images: " . count($missing) . "\n";

if (count($missing) > 0) {
    echo "\nTo repair these images, you need to:\n";
    echo "1. Provide working image URLs for these products\n";
    echo "2. Re-import with corrected CSV\n";
    
    echo "\nMissing images list:\n";
    foreach ($missing as $item) {
        echo "- SKU: {$item['sku']}, Name: {$item['name']}\n";
    }
}

echo "</pre>";
