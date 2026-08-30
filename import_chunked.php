<?php
/**
 * Chunked CSV Import - Process products in batches to avoid timeout
 * Access: https://costikyan.mindspire.org/import_chunked.php?token=import_2024&file=costikyan_complete_catalog.csv&batch=50
 */

$token = $_GET['token'] ?? '';
$expectedToken = 'import_2024';

if ($token !== $expectedToken) {
    http_response_code(403);
    die('Access denied. Invalid token.');
}

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

ini_set('memory_limit', '512M');
set_time_limit(300);

$csvFile = $_GET['file'] ?? 'costikyan_complete_catalog.csv';
$batchSize = intval($_GET['batch'] ?? 50);
$offset = intval($_GET['offset'] ?? 0);

// Allow CSV file from parent directory (for localhost)
$csvPaths = [
    __DIR__ . '/' . $csvFile,
    __DIR__ . '/../' . $csvFile,
    'E:/' . $csvFile,
];

$csvPath = null;
foreach ($csvPaths as $path) {
    if (file_exists($path)) {
        $csvPath = $path;
        break;
    }
}

if (!$csvPath) {
    die("CSV file not found. Searched: " . implode(", ", $csvPaths));
}

echo "Using CSV: $csvPath\n\n";

// Read CSV
$data = array_map('str_getcsv', file($csvPath));
$headers = array_shift($data);
$total = count($data);

// Get batch
$batch = array_slice($data, $offset, $batchSize);
$imported = 0;
$errors = [];

echo "<h2>Chunked Import - Batch $offset to " . ($offset + $batchSize) . "</h2>";
echo "<pre>";
echo "Total products: $total\n";
echo "Processing: " . count($batch) . " products\n";
echo str_repeat("-", 50) . "\n";

foreach ($batch as $index => $row) {
    if (count($row) !== count($headers)) {
        $errors[] = "Row " . ($offset + $index + 2) . ": Column count mismatch";
        continue;
    }
    
    $rowData = array_combine($headers, $row);
    
    // Skip if no name
    if (empty($rowData['name'])) {
        continue;
    }
    
    try {
        // Find or create category
        $categoryId = 1;
        if (!empty($rowData['category_id'])) {
            $categoryId = intval($rowData['category_id']);
        }
        
        // Generate slug
        $slug = !empty($rowData['slug']) ? $rowData['slug'] : Str::slug($rowData['name']) . '-' . Str::random(4);
        
        // Check for existing
        $product = null;
        if (!empty($rowData['sku'])) {
            $existing = Product::where('sku', $rowData['sku'])->first();
            if ($existing) {
                $existing->update([
                    'name' => $rowData['name'],
                    'price' => floatval(preg_replace('/[^0-9.]/', '', $rowData['price'] ?? 0)),
                    'category_id' => $categoryId,
                    'status' => $rowData['status'] ?? 'active',
                ]);
                $product = $existing;
            }
        }
        
        if (!$product) {
            $product = Product::create([
                'name' => $rowData['name'],
                'slug' => $slug,
                'sku' => $rowData['sku'] ?? null,
                'description' => $rowData['description'] ?? null,
                'price' => floatval(preg_replace('/[^0-9.]/', '', $rowData['price'] ?? 0)),
                'sale_price' => !empty($rowData['sale_price']) ? floatval(preg_replace('/[^0-9.]/', '', $rowData['sale_price'])) : null,
                'category_id' => $categoryId,
                'status' => $rowData['status'] ?? 'active',
                'stock_status' => $rowData['stock_status'] ?? 'in_stock',
                'material' => $rowData['material'] ?? null,
                'origin' => $rowData['origin'] ?? null,
                'style' => $rowData['style'] ?? null,
                'stock' => 5,
            ]);
        }
        
        echo ($offset + $index + 1) . ". Imported: {$rowData['name']}\n";
        $imported++;
        
    } catch (\Exception $e) {
        $errors[] = "Row " . ($offset + $index + 2) . ": " . $e->getMessage();
        echo "ERROR: {$rowData['name']} - {$e->getMessage()}\n";
    }
}

echo str_repeat("-", 50) . "\n";
echo "Imported: $imported / " . count($batch) . "\n";
echo "Errors: " . count($errors) . "\n";

$nextOffset = $offset + $batchSize;
if ($nextOffset < $total) {
    $nextUrl = "import_chunked.php?token=$token&file=$csvFile&batch=$batchSize&offset=$nextOffset";
    echo "\n<a href='$nextUrl' style='font-size: 18px; color: #E8651A; font-weight: bold;'>→ Import Next Batch ($nextOffset - " . min($nextOffset + $batchSize, $total) . ")</a>\n";
} else {
    echo "\n✅ ALL PRODUCTS IMPORTED!\n";
}

echo "</pre>";
