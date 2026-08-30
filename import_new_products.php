<?php
/**
 * Import 69 new products from CCC Products Excel (8-26-2026).
 * Run on production server via: php import_new_products.php
 * Idempotent: skips products that already exist by slug.
 */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductDimensionPrice;
use Illuminate\Support\Str;

$data = json_decode(file_get_contents(__DIR__ . '/products_data.json'), true);
if (!$data) {
    fwrite(STDERR, "Could not read products_data.json\n");
    exit(1);
}

$categoryId = 8; // Custom Size / made-to-order
$created = 0;
$skipped = 0;
$errors = [];

// Color name → hex map (extended for the new color names in this batch)
$colorHexMap = [
    'ivory' => '#F4F0E6', 'cream' => '#F5F0E1', 'white' => '#FFFFFF',
    'silver' => '#C0C0C0', 'taupe' => '#B8A99A', 'beige' => '#E8DCC4',
    'sand' => '#D9C7A3', 'stone' => '#A89F91', 'blue' => '#3A6EA8',
    'azure' => '#3A6EA8', 'navy' => '#1A2744', 'cashmere' => '#D4C5B0',
    'gabardine' => '#B8A78D', 'ash' => '#9E9E9E', 'steel' => '#7C8B96',
    'glacier' => '#A3C1D4', 'canyon' => '#B8956B', 'poplar' => '#C4B89C',
    'cavern' => '#6B6258', 'birch' => '#E8DCC4', 'earth' => '#8B7355',
    'honey maple' => '#D4B57E', 'blue spruce' => '#5A7A6B', 'denim' => '#4A6B8A',
    'sandstone' => '#D9C7A3', 'oak heather' => '#A89F91', 'titanium heather' => '#8A8A8A',
    'zinc heather' => '#9E9E9E', 'cobalt' => '#1E4D8B', 'mint' => '#A3D4B8',
    'shadow' => '#6B6B6B', 'driftwood' => '#A89F91', 'marble' => '#E8E8E8',
    'oatmeal' => '#D4CAB8', 'plateau' => '#B8A99A', 'ravine' => '#8B7355',
    'harvest' => '#C9A227', 'marine' => '#3A6EA8', 'north sea' => '#2A4A6B',
    'pearl river' => '#A3C1D4', 'nutmeg' => '#8B5A3C', 'toffee' => '#9C6B3F',
    'indigo' => '#3A4A8B', 'slate' => '#7C8B96', 'charcoal' => '#36454F',
    'grey' => '#808080', 'gray' => '#808080', 'black' => '#1A1A1A',
    'gold' => '#C9A227', 'green' => '#2D5C3A', 'red' => '#8B2020',
    'brown' => '#7B5B3F', 'tan' => '#D2B48C', 'warm' => '#D4B57E',
    'cool' => '#A3C1D4', 'neutral' => '#D4CAB8',
];

function guessHex(string $name, array $map): string
{
    $lower = strtolower(trim($name));
    if (isset($map[$lower])) return $map[$lower];
    // Try each word
    foreach (preg_split('/[\s\-_]+/', $lower) as $word) {
        if (isset($map[$word])) return $map[$word];
    }
    return '#B0ABA0'; // neutral default
}

foreach ($data as $row) {
    $collection = trim($row['collection']);
    $color = trim($row['color']);
    $color = preg_replace('/\s+/', ' ', $color); // normalize double spaces

    // Product name: "{Collection} {Color}" e.g. "Gems Ivory"
    $name = $collection . ' ' . $color;
    $name = preg_replace('/\s+/', ' ', $name);
    $slug = Str::slug($name);

    // Check if already exists
    $existing = Product::where('slug', $slug)->first();
    if ($existing) {
        $skipped++;
        echo "SKIP: $name (slug already exists)\n";
        continue;
    }

    // Ensure unique slug
    $slugBase = $slug;
    $counter = 1;
    while (Product::where('slug', $slug)->exists()) {
        $slug = $slugBase . '-' . $counter++;
    }

    // Visibility flags
    $visibility = strtolower(trim($row['visibility']));
    $featured = $visibility === 'featured' ? 1 : 0;
    $bestseller = $visibility === 'best seller' ? 1 : 0;
    $newArrival = $visibility === 'new arrival' ? 1 : 0;

    // Clean up construction (remove leading space)
    $construction = trim($row['construction']);
    $construction = preg_replace('/\s+/', ' ', $construction);

    // Clean up origin (fix typos like "Inda")
    $origin = trim($row['origin']);
    if (strtolower($origin) === 'inda') $origin = 'India';

    // Price per sqft (column I)
    $pricePerSqft = floatval($row['price_per_sqft']);

    // Refined color (normalize "blue" → "Blue")
    $refinedColor = trim($row['refined_color']);
    $refinedColor = ucfirst(strtolower($refinedColor));
    if ($refinedColor === 'Cool tones') $refinedColor = 'Cool Tones';
    if ($refinedColor === 'Warm tone') $refinedColor = 'Warm Tone';

    // Pattern (column K) - fix typo "Geomtric"
    $pattern = trim($row['pattern']);
    if (strtolower($pattern) === 'geomtric') $pattern = 'Geometric';

    // Style (column O) - fix "Causal" typo
    $style = trim($row['style']);
    if (strtolower($style) === 'causal') $style = 'Casual';

    // Build the product
    $product = Product::create([
        'name' => $name,
        'slug' => $slug,
        'description' => trim($row['description']),
        'price' => $pricePerSqft,
        'category_id' => $categoryId,
        'material' => trim($row['material']),
        'origin' => $origin,
        'style' => $style,
        'construction' => $construction,
        'refined_color' => $refinedColor,
        'featured' => $featured,
        'is_bestseller' => $bestseller,
        'is_new_arrival' => $newArrival,
        'stock' => 0,
        'status' => 'active',
        'use_type' => 'residential',
    ]);

    // Add color
    $hex = guessHex($color, $colorHexMap);
    ProductColor::create([
        'product_id' => $product->id,
        'color_name' => $color,
        'color_hex' => $hex,
    ]);

    // Add dimension prices
    $dims = [
        ['label' => '6x9', 'width' => 6, 'length' => 9, 'price' => $row['price_6x9']],
        ['label' => '8x10', 'width' => 8, 'length' => 10, 'price' => $row['price_8x10']],
        ['label' => '9x12', 'width' => 9, 'length' => 12, 'price' => $row['price_9x12']],
        ['label' => '10x14', 'width' => 10, 'length' => 14, 'price' => $row['price_10x14']],
        ['label' => '12x15', 'width' => 12, 'length' => 15, 'price' => $row['price_12x15']],
    ];

    foreach ($dims as $i => $dim) {
        $price = floatval($dim['price']);
        if ($price <= 0) continue;

        ProductDimensionPrice::create([
            'product_id' => $product->id,
            'label' => $dim['label'],
            'width' => $dim['width'],
            'length' => $dim['length'],
            'shape' => 'rectangular',
            'price' => $price,
            'stock' => 0,
            'is_default' => $i === 0,
            'sort_order' => $i,
        ]);
    }

    $created++;
    echo "CREATE: $name (id:{$product->id}) [$visibility] \$$pricePerSqft/sqft\n";
}

echo "\n=== IMPORT COMPLETE ===\n";
echo "Created: $created\n";
echo "Skipped: $skipped\n";
if (!empty($errors)) {
    echo "Errors: " . count($errors) . "\n";
    foreach ($errors as $e) echo "  - $e\n";
}
