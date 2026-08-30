<?php
/**
 * Product Image Repair Script
 * Use this to fix missing images by re-importing with corrected URLs
 * 
 * Access: https://your-domain.com/repair_images.php?token=repair_2024
 */

$token = $_GET['token'] ?? '';
$expectedToken = 'repair_2024';

if ($token !== $expectedToken) {
    http_response_code(403);
    die('Access denied. Use: ?token=repair_2024');
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

echo "<h1>Product Image Repair Tool</h1>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;max-width:1200px;margin:0 auto}
.product{border:1px solid #ddd;padding:10px;margin:10px 0;border-radius:5px}
.valid{color:green}.invalid{color:red}.warning{color:orange}</style>";

$action = $_GET['action'] ?? 'check';

// CHECK - Show current status
if ($action === 'check') {
    echo "<h2>Current Image Status</h2>";
    
    $products = Product::with('images')->get();
    $validCount = 0;
    $invalidCount = 0;
    
    foreach ($products as $product) {
        echo "<div class='product'>";
        echo "<strong>#{$product->id} - {$product->sku}</strong><br>";
        echo "Name: {$product->name}<br>";
        
        if ($product->images->count() === 0) {
            echo "<span class='invalid'>❌ No images</span>";
            $invalidCount++;
        } else {
            foreach ($product->images as $img) {
                $fullPath = storage_path('app/public/' . $img->path);
                $exists = file_exists($fullPath);
                $size = $exists ? filesize($fullPath) : 0;
                
                if ($exists && $size > 100) {
                    echo "<span class='valid'>✅ {$img->path} (" . round($size/1024, 1) . " KB)</span><br>";
                    $validCount++;
                } else {
                    echo "<span class='invalid'>❌ {$img->path} - " . ($exists ? "Corrupted ($size bytes)" : "Missing") . "</span><br>";
                    $invalidCount++;
                }
            }
        }
        echo "</div>";
    }
    
    echo "<hr><h3>Summary</h3>";
    echo "Valid images: $validCount<br>";
    echo "Invalid/Missing: $invalidCount<br>";
    echo "<hr>";
    echo "<h3>Next Steps</h3>";
    echo "<ol>";
    echo "<li><a href='?token=$token&action=clear_invalid'>Click here to clear invalid image records</a></li>";
    echo "<li>Import the fixed CSV file: <code>costikyan_bulk_import_15_fixed.csv</code></li>";
    echo "<li><a href='/admin/products'>Go to admin products</a> to verify</li>";
    echo "</ol>";
}

// CLEAR INVALID - Remove broken image records
if ($action === 'clear_invalid') {
    echo "<h2>Clearing Invalid Image Records</h2>";
    
    $images = ProductImage::all();
    $cleared = 0;
    
    foreach ($images as $img) {
        $fullPath = storage_path('app/public/' . $img->path);
        $exists = file_exists($fullPath);
        $size = $exists ? filesize($fullPath) : 0;
        
        if (!$exists || $size < 100) {
            echo "Deleting invalid record: {$img->path}<br>";
            $img->delete();
            $cleared++;
            
            // Also delete file if it exists but is corrupted
            if ($exists) {
                @unlink($fullPath);
            }
        }
    }
    
    echo "<hr>Cleared $cleared invalid image records.<br>";
    echo "<a href='?token=$token&action=check'>Check status again</a> | ";
    echo "<a href='/admin/products/import'>Go to Import Page</a>";
}

echo "<hr><small>Token: $token</small>";
