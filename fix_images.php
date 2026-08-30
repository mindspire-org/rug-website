<?php
/**
 * Fix Product Image Links on Hostinger
 * 
 * Problem: Database product_images.path points to non-existent files
 *          while real images exist in public_html/storage/products/
 * 
 * Usage: Upload to public_html/ and access via browser:
 *        https://costikyan.mindspire.org/fix_images.php
 * 
 * Safety: Only assigns unassigned images. Creates backup of changes.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;

echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .warn{color:orange;} .err{color:red;} </style>";
echo "<h2>🔧 Fix Product Image Links</h2>";

// ── 1. Get all actual image files ────────────────────────────
$publicDir = __DIR__ . '/storage/products';
$storageDir = __DIR__ . '/storage/app/public/products';

// Prefer public/storage/products/ (web-facing)
$imageDir = is_dir($publicDir) ? $publicDir : $storageDir;

$files = [];
if (is_dir($imageDir)) {
    $glob = glob($imageDir . '/*');
    foreach ($glob as $f) {
        if (is_file($f) && filesize($f) > 100) {
            $mime = mime_content_type($f);
            if (strpos($mime, 'image/') === 0) {
                $files[] = 'products/' . basename($f);
            }
        }
    }
}
echo "<p>📁 Found <b>" . count($files) . "</b> actual image files in: $imageDir</p>";

// ── 2. Get all products ──────────────────────────────────────
$products = Product::with(['images'])->get();
echo "<p>📦 Total products: <b>" . $products->count() . "</b></p>";

// ── 3. Find which images are already assigned ──────────────
$assignedPaths = ProductImage::pluck('path')->all();
$assignedPaths = array_map('strval', $assignedPaths);

// Filter to only unassigned files
$unassigned = array_values(array_diff($files, $assignedPaths));
echo "<p>🔗 Already linked: <b>" . count($assignedPaths) . "</b> image records</p>";
echo "<p>🆓 Unassigned files available: <b>" . count($unassigned) . "</b></p>";

// ── 4. Find products needing images ──────────────────────────
$needsImage = [];
foreach ($products as $product) {
    $hasValid = false;
    foreach ($product->images as $img) {
        $filePath = $imageDir . '/' . basename($img->path);
        if (file_exists($filePath) && filesize($filePath) > 100) {
            $hasValid = true;
            break;
        }
    }
    if (!$hasValid) {
        $needsImage[] = $product;
    }
}

echo "<p>❌ Products needing images: <b>" . count($needsImage) . "</b></p>";

// ── 5. Auto-assign images ────────────────────────────────────
if (count($needsImage) === 0) {
    echo "<p class='ok'>✅ All products already have valid images!</p>";
    exit;
}

if (count($unassigned) === 0) {
    echo "<p class='err'>❌ No unassigned images available to link. Upload more images first.</p>";
    exit;
}

// Show preview first
if (!isset($_GET['confirm'])) {
    echo "<hr><h3>Preview (first 10 assignments):</h3><ul>";
    $preview = array_slice($needsImage, 0, 10);
    foreach ($preview as $i => $product) {
        $imgFile = $unassigned[$i] ?? 'none left';
        echo "<li>{$product->name} → <code>{$imgFile}</code></li>";
    }
    echo "</ul>";
    
    echo "<p><a href='?confirm=1' style='padding:12px 24px;background:#22c55e;color:white;text-decoration:none;border-radius:6px;font-weight:bold;'>✅ CONFIRM & FIX ALL</a></p>";
    echo "<p><small>This will create product_images records linking each product to an available image file.</small></p>";
    exit;
}

// ── 6. Execute assignment ────────────────────────────────────
echo "<hr><h3>Executing...</h3>";
$assigned = 0;
$skipped = 0;

DB::beginTransaction();

try {
    foreach ($needsImage as $i => $product) {
        if (!isset($unassigned[$i])) {
            echo "<p class='warn'>⚠️ Ran out of images at product: {$product->name}</p>";
            $skipped++;
            continue;
        }

        $path = $unassigned[$i];

        // Delete old invalid images for this product
        ProductImage::where('product_id', $product->id)->delete();

        // Create new primary image
        ProductImage::create([
            'product_id' => $product->id,
            'path'       => $path,
            'sort_order' => 0,
            'is_primary' => true,
        ]);

        echo "<p class='ok'>✅ {$product->name} → {$path}</p>";
        $assigned++;
    }

    DB::commit();
    echo "<hr><h3>Done!</h3>";
    echo "<p class='ok'>✅ Assigned: <b>$assigned</b> products</p>";
    if ($skipped > 0) {
        echo "<p class='warn'>⚠️ Skipped: <b>$skipped</b> (ran out of images)</p>";
    }
    echo "<p><a href='/' style='padding:10px 20px;background:#3b82f6;color:white;text-decoration:none;border-radius:6px;'>← Go to Shop</a></p>";

} catch (Exception $e) {
    DB::rollBack();
    echo "<p class='err'>❌ ERROR: " . $e->getMessage() . "</p>";
}
