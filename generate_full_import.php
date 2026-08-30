<?php
/**
 * Generate Full Product Import CSV with Realistic Images
 * This script reads product data and creates import CSV with matching Unsplash images
 */

require __DIR__.'/vendor/autoload.php';

// Image mapping by style/color for realistic rug images
$imageMap = [
    'Modern' => [
        'Blue' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Multi' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Natural' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
        'Grey' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Ivory' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
    ],
    'Traditional' => [
        'Oatmeal' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Charcoal' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
        'Vintage' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
        'Beach' => 'https://images.unsplash.com/photo-1575414723220-294f90e8d8a0?w=800&q=80',
        'Sand' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
    ],
    'Transitional' => [
        'Blue' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
        'Camel' => 'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80',
        'Shell' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80',
        'Fog' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
        'Mist' => 'https://images.unsplash.com/photo-1575414723220-294f90e8d8a0?w=800&q=80',
        'default' => 'https://images.unsplash.com/photo-1564078516393-cf04bd966897?w=800&q=80',
    ],
    'Bohemian' => [
        'default' => 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=800&q=80',
    ],
    'default' => 'https://images.unsplash.com/photo-1567016432779-094069958ea5?w=800&q=80',
];

function getImageForProduct($style, $color) {
    global $imageMap;
    
    $styleKey = isset($imageMap[$style]) ? $style : 'default';
    
    // Try to match color
    foreach ($imageMap[$styleKey] as $colorKey => $url) {
        if (stripos($color, $colorKey) !== false) {
            return $url;
        }
    }
    
    // Return style default or global default
    return $imageMap[$styleKey]['default'] ?? $imageMap['default'];
}

// Read products data from various sources
function readProductData() {
    $sources = [
        __DIR__ . '/products_data.csv',
        __DIR__ . '/products data.txt',
        __DIR__ . '/products.txt',
    ];
    
    foreach ($sources as $source) {
        if (file_exists($source) && filesize($source) > 0) {
            echo "Found data source: $source\n";
            return file_get_contents($source);
        }
    }
    
    return null;
}

// Generate CSV header
$csvHeader = "name,slug,sku,description,short_description,price,sale_price,cost_price,category_id,status,stock_status,featured,material,origin,style,construction,pile_height,width,length,shape,color,pattern,stock,meta_title,meta_description,image_url\n";

// Example data structure - replace with actual data reading
$sampleProducts = [
    // Add all products here from your data
];

echo "<h1>Product Import CSV Generator</h1>";
echo "<pre>";

$data = readProductData();

if (!$data) {
    echo "No product data file found.\n";
    echo "Please export your Excel data to CSV format and save as:\n";
    echo "- products_data.csv\n";
    echo "- products data.txt\n";
    echo "\nExpected columns:\n";
    echo "name | sku | price | category | style | color | material | description | etc.\n";
    exit;
}

// Parse the data and generate CSV
// This is a placeholder - actual parsing depends on data format

echo "Data found! Processing...\n";
echo "Length: " . strlen($data) . " bytes\n";

// TODO: Parse actual data format
echo "\nTo complete, please provide the data format or export as CSV.";
echo "</pre>";
