<?php
/**
 * Complete Product CSV Generator
 * Parses the full product data and generates import CSV with realistic images
 */

// Image mapping by style and color for realistic rug images
$imageLibrary = [
    'Modern' => [
        'Blue' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Blue - Green' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Blue - Camel' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Blue - Ivory' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Blue - Sage' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
        'Lt Blue' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Multi' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Natural' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
        'Natural Ivory Grey' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Ivory' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Ivory - Toffee' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Ivory - Multi' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Grey' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Grey - Blue' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Silver - Cream' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Slate' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Sand' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Merlot' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
        'Brown - Blue' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Camel - Blue' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Denim' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
    ],
    'Traditional' => [
        'Oatmeal' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Oatmeal/Ivory' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Charcoal' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Charcoal Grey' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Vintage' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
        'Vintage Greys' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Steel - Celery' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
        'Steel' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
        'Silver' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Beach' => 'https://images.unsplash.com/photo-1575414723220-294f90e8d8a0?w=800&q=80',
        'Sand' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
    ],
    'Transitional' => [
        'Blue' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Camel' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Sand' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Shell' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Fog' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Mist' => 'https://images.unsplash.com/photo-1575414723220-294f90e8d8a0?w=800&q=80',
        'Ivory' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Grey' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
    ],
    'Bohemian' => [
        'default' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
    ],
    'Contemporary' => [
        'default' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
    ],
    'default' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
];

// Category mapping
$categoryMap = [
    'Samad' => 1,
    'Kalaty' => 1,
    'KALATY' => 1,
    'Stanton' => 2,
    'Parasol' => 3,
];

function getImageForProduct($style, $color, $brand, $originalUrl) {
    global $imageLibrary;
    
    // If it's a Dropbox URL, use it directly (convert to dl=1)
    if (strpos($originalUrl, 'dropbox.com') !== false) {
        return str_replace('dl=0', 'dl=1', $originalUrl);
    }
    
    // If it's a Kalaty URL, use it directly
    if (strpos($originalUrl, 'kalaty.com') !== false) {
        return $originalUrl;
    }
    
    // For Brandfolder or missing images, use style/color based mapping
    $styleKey = isset($imageLibrary[$style]) ? $style : 'default';
    
    // Try exact color match first
    if (isset($imageLibrary[$styleKey][$color])) {
        return $imageLibrary[$styleKey][$color];
    }
    
    // Try partial color match
    foreach ($imageLibrary[$styleKey] as $colorKey => $url) {
        if ($colorKey !== 'default' && stripos($color, $colorKey) !== false) {
            return $url;
        }
    }
    
    // Return style default or global default
    return $imageLibrary[$styleKey]['default'] ?? $imageLibrary['default'];
}

function parsePrice($priceStr) {
    $clean = preg_replace('/[^0-9.]/', '', $priceStr);
    return floatval($clean);
}

function slugify($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
}

echo "<h1>Complete Product CSV Generator</h1>";
echo "<pre>";

// Read the product data file
$dataFile = __DIR__ . '/products_data_full.txt';
if (!file_exists($dataFile)) {
    die("Product data file not found: $dataFile");
}

$data = file_get_contents($dataFile);
$lines = explode("\n", $data);

echo "Total lines in file: " . count($lines) . "\n";

$products = [];
$currentLine = 0;

foreach ($lines as $line) {
    $currentLine++;
    $line = trim($line);
    if (empty($line)) continue;
    
    // Parse tab-separated values
    $cols = explode("\t", $line);
    if (count($cols) < 10) continue;
    
    $brand = trim($cols[0]);
    $sku = trim($cols[1]);
    $collection = trim($cols[2]);
    $stockStatus = trim($cols[3]);
    $design = trim($cols[4]);
    $color = trim($cols[5]);
    $costPrice = parsePrice($cols[6]);
    $price = parsePrice($cols[7]);
    $unit = trim($cols[8]);
    $material = trim($cols[9]);
    $origin = trim($cols[10]);
    $style = trim($cols[11]);
    $imageUrl = isset($cols[12]) ? trim($cols[12]) : '';
    $description = isset($cols[13]) ? trim($cols[13]) : '';
    
    // Skip if missing critical data
    if (empty($sku) || empty($design)) continue;
    
    // Get category ID
    $categoryId = $categoryMap[$brand] ?? 1;
    
    // Generate product name
    $name = "$design $color - $collection";
    
    // Generate slug
    $slug = slugify($name);
    
    // Get appropriate image
    $finalImageUrl = getImageForProduct($style, $color, $brand, $imageUrl);
    
    // Short description
    $shortDesc = "$style $material in $color";
    
    // Create product row
    $products[] = [
        'name' => $name,
        'slug' => $slug,
        'sku' => $sku ?: 'SKU-' . strtoupper(substr(md5($name), 0, 8)),
        'description' => $description ?: "$name - $material rug crafted in $origin",
        'short_description' => $shortDesc,
        'price' => $price,
        'sale_price' => round($price * 0.9, 2), // 10% off
        'cost_price' => $costPrice,
        'category_id' => $categoryId,
        'status' => 'active',
        'stock_status' => 'in_stock',
        'featured' => 0,
        'material' => $material,
        'origin' => $origin,
        'style' => $style,
        'construction' => $material,
        'pile_height' => '0.4 inches',
        'width' => 8.00,
        'length' => 10.00,
        'shape' => 'rectangular',
        'color' => $color,
        'pattern' => 'Abstract',
        'stock' => 5,
        'meta_title' => "$name | Costikyan",
        'meta_description' => "$style rug in $color - $material",
        'image_url' => $finalImageUrl,
    ];
}

echo "Successfully parsed " . count($products) . " products\n";

// Generate CSV
$csvFile = __DIR__ . '/costikyan_complete_catalog.csv';
$fp = fopen($csvFile, 'w');

// CSV Header
$headers = ['name','slug','sku','description','short_description','price','sale_price','cost_price','category_id','status','stock_status','featured','material','origin','style','construction','pile_height','width','length','shape','color','pattern','stock','meta_title','meta_description','image_url'];
fputcsv($fp, $headers);

// Write products
foreach ($products as $product) {
    fputcsv($fp, [
        $product['name'],
        $product['slug'],
        $product['sku'],
        $product['description'],
        $product['short_description'],
        $product['price'],
        $product['sale_price'],
        $product['cost_price'],
        $product['category_id'],
        $product['status'],
        $product['stock_status'],
        $product['featured'],
        $product['material'],
        $product['origin'],
        $product['style'],
        $product['construction'],
        $product['pile_height'],
        $product['width'],
        $product['length'],
        $product['shape'],
        $product['color'],
        $product['pattern'],
        $product['stock'],
        $product['meta_title'],
        $product['meta_description'],
        $product['image_url'],
    ]);
}

fclose($fp);

echo "\n=== CSV Generated Successfully ===\n";
echo "File: $csvFile\n";
echo "Total products: " . count($products) . "\n";

// Show sample
if (count($products) > 0) {
    echo "\n=== Sample Products ===\n";
    for ($i = 0; $i < min(5, count($products)); $i++) {
        $p = $products[$i];
        echo ($i+1) . ". {$p['name']}\n";
        echo "   SKU: {$p['sku']}, Price: {$p['price']}, Image: " . substr($p['image_url'], 0, 50) . "...\n\n";
    }
}

echo "\n=== Import Instructions ===\n";
echo "1. Go to Admin → Products → Import\n";
echo "2. Upload: costikyan_complete_import.csv\n";
echo "3. All images will be automatically downloaded\n";
echo "</pre>";
