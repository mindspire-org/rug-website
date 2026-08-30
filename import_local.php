<?php
/**
 * Localhost Import Script - Run via: php import_local.php
 * This imports ALL products at once on localhost (no timeout issues)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

// Find CSV file
$csvFiles = [
    'E:/costikyan_complete_catalog.csv',
    __DIR__ . '/../costikyan_complete_catalog.csv',
    __DIR__ . '/costikyan_complete_catalog.csv',
];

$csvPath = null;
foreach ($csvFiles as $file) {
    if (file_exists($file)) {
        $csvPath = $file;
        break;
    }
}

if (!$csvPath) {
    echo "❌ CSV file not found!\n";
    echo "Searched:\n";
    foreach ($csvFiles as $f) {
        echo "  - $f\n";
    }
    exit(1);
}

echo "📁 Found CSV: $csvPath\n";
echo "🚀 Starting import...\n\n";

// Read CSV
$data = array_map('str_getcsv', file($csvPath));
$headers = array_shift($data);
$total = count($data);

echo "📊 Total products to import: $total\n";
echo str_repeat("-", 60) . "\n";

$imported = 0;
$skipped = 0;
$errors = [];

foreach ($data as $index => $row) {
    if (count($row) !== count($headers)) {
        $errors[] = "Row " . ($index + 2) . ": Column count mismatch";
        continue;
    }
    
    $rowData = array_combine($headers, $row);
    
    // Skip if no name
    if (empty($rowData['name'])) {
        $skipped++;
        continue;
    }
    
    try {
        // Parse price (remove $ and commas)
        $price = 0;
        if (!empty($rowData['price'])) {
            $price = floatval(preg_replace('/[^0-9.]/', '', $rowData['price']));
        }
        
        $salePrice = null;
        if (!empty($rowData['sale_price'])) {
            $salePrice = floatval(preg_replace('/[^0-9.]/', '', $rowData['sale_price']));
        }
        
        // Get category - validate it exists
        $categoryId = 1; // Default to first category
        if (!empty($rowData['category_id'])) {
            $requestedCategory = intval($rowData['category_id']);
            // Check if category exists
            $categoryExists = Category::where('id', $requestedCategory)->exists();
            if ($categoryExists) {
                $categoryId = $requestedCategory;
            } else {
                echo "  ⚠️  Category $requestedCategory not found, using default (1)\n";
            }
        }
        
        // Generate slug
        $slug = !empty($rowData['slug']) ? $rowData['slug'] : Str::slug($rowData['name']) . '-' . Str::random(4);
        
        // Check for existing by SKU
        $product = null;
        $sku = $rowData['sku'] ?? null;
        
        if (!empty($sku)) {
            $existing = Product::where('sku', $sku)->first();
            if ($existing) {
                // Update existing
                $existing->update([
                    'name' => $rowData['name'],
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'category_id' => $categoryId,
                    'status' => $rowData['status'] ?? 'active',
                    'description' => $rowData['description'] ?? null,
                    'material' => $rowData['material'] ?? null,
                    'origin' => $rowData['origin'] ?? null,
                    'style' => $rowData['style'] ?? null,
                ]);
                $product = $existing;
                echo "  🔄 Updated: {$rowData['name']} (SKU: $sku)\n";
                $imported++;
            }
        }
        
        if (!$product) {
            // Prepare product data
            $productData = [
                'name' => $rowData['name'],
                'slug' => $slug,
                'sku' => $sku,
                'description' => $rowData['description'] ?? null,
                'short_description' => $rowData['short_description'] ?? null,
                'price' => $price,
                'category_id' => $categoryId,
                'status' => $rowData['status'] ?? 'active',
                'stock_status' => $rowData['stock_status'] ?? 'in_stock',
                'featured' => (isset($rowData['featured']) && $rowData['featured'] == '1'),
                'material' => $rowData['material'] ?? null,
                'origin' => $rowData['origin'] ?? null,
                'style' => $rowData['style'] ?? null,
                'construction' => $rowData['construction'] ?? null,
                'pile_height' => $rowData['pile_height'] ?? null,
                'width' => !empty($rowData['width']) ? floatval($rowData['width']) : null,
                'length' => !empty($rowData['length']) ? floatval($rowData['length']) : null,
                'shape' => $rowData['shape'] ?? 'rectangular',
                'color' => $rowData['color'] ?? null,
                'pattern' => $rowData['pattern'] ?? null,
                'stock' => !empty($rowData['stock']) ? intval($rowData['stock']) : 5,
            ];
            
            // Only add sale_price if it has a value
            if ($salePrice !== null && $salePrice > 0) {
                $productData['sale_price'] = $salePrice;
            }
            
            // Create new product
            $product = Product::create($productData);
            echo "  ✅ Created: {$rowData['name']}\n";
            $imported++;
        }
        
        // Add image if URL exists (skip on localhost for speed)
        if (!empty($rowData['image_url']) && $product && strpos($rowData['image_url'], 'http') === 0) {
            // Check if product already has images
            $existingImages = ProductImage::where('product_id', $product->id)->count();
            if ($existingImages == 0) {
                try {
                    // For localhost, we'll add the image URL as a placeholder
                    // Real download should happen on server with proper storage
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => 'products/placeholder.jpg', // You'll need to download images separately
                        'sort_order' => 0,
                        'is_primary' => true,
                    ]);
                } catch (\Exception $imgEx) {
                    // Silent fail for images on localhost
                }
            }
        }
        
    } catch (\Exception $e) {
        $errors[] = "Row " . ($index + 2) . " ({$rowData['name']}): " . $e->getMessage();
        echo "  ❌ Error: {$rowData['name']} - {$e->getMessage()}\n";
    }
    
    // Progress every 10 products
    if (($index + 1) % 10 === 0) {
        echo "\n📈 Progress: " . ($index + 1) . "/$total (" . round((($index + 1) / $total) * 100) . "%)\n";
        echo str_repeat("-", 60) . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ IMPORT COMPLETE!\n";
echo "📊 Results:\n";
echo "  - Imported/Updated: $imported\n";
echo "  - Skipped (no name): $skipped\n";
echo "  - Errors: " . count($errors) . "\n";

if (!empty($errors)) {
    echo "\n⚠️  Errors encountered:\n";
    foreach (array_slice($errors, 0, 10) as $error) {
        echo "  - $error\n";
    }
    if (count($errors) > 10) {
        echo "  ... and " . (count($errors) - 10) . " more\n";
    }
}

echo "\n🎉 Done! Check your admin panel at: http://127.0.0.1:8000/admin/products\n";
