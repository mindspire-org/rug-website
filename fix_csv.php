<?php
/**
 * Fixes the product CSV:
 *  1. Converts Dropbox dl=0 → dl=1 in image_url
 *  2. Converts color names to "Name: #hex" format in the colors column
 * 
 * Run: php fix_csv.php
 * Output: product_import_fixed.csv (in same directory as this script)
 */

$input  = 'C:\\Users\\kotaebah\\Downloads\\product_import_ready.csv';
$output = __DIR__ . '\\product_import_fixed.csv';

$colorMap = [
    'ivory' => '#F4F0E6', 'cream' => '#F5F0E1', 'white' => '#FFFFFF', 'snow' => '#FFFAFA',
    'gold' => '#C9A227', 'golden' => '#C9A227', 'wheat' => '#E8D6A8', 'sand' => '#D9C7A3',
    'fawn' => '#C9A87C', 'toffee' => '#9C6B3F', 'cafe' => '#6F4E37', 'café' => '#6F4E37',
    'coffee' => '#6F4E37', 'henna' => '#9C4722', 'merlot' => '#5E1A2B', 'wine' => '#722F37',
    'oyster' => '#DAD3C4', 'pewter' => '#96989B', 'silver' => '#C0C0C0', 'steel' => '#7C8B96',
    'grey' => '#808080', 'gray' => '#808080', 'charcoal' => '#36454F', 'black' => '#1A1A1A',
    'blue' => '#3A6EA8', 'navy' => '#1A2744', 'teal' => '#2A6E6E', 'lagoon' => '#3C8C8C',
    'royal' => '#27408B', 'green' => '#2D5C3A', 'celery' => '#B7C68B', 'apple' => '#7CB342',
    'sage' => '#9CAF88', 'olive' => '#808000', 'red' => '#8B2020', 'rust' => '#B7410E',
    'orange' => '#D2691E', 'mandarin' => '#E2725B', 'rose' => '#C28285', 'mauve' => '#B784A7',
    'pink' => '#E8B4B8', 'purple' => '#6A4C93', 'iris' => '#5A4FCF', 'brown' => '#7B5B3F',
    'tan' => '#D2B48C', 'beige' => '#E8DCC4', 'taupe' => '#B8A99A', 'multi' => '#B0ABA0',
    'yellow' => '#D4C832', 'mustard' => '#C9A227', 'aqua' => '#5BC8C8',
];

function nameToHex(string $name, array $map): string
{
    $lower = strtolower(trim($name));
    if (isset($map[$lower])) return $map[$lower];
    foreach (preg_split('/[\s\-_]+/', $lower) as $word) {
        if (isset($map[$word])) return $map[$word];
    }
    return '#B0ABA0';
}

function fixColors(string $colorsStr, array $map): string
{
    if (empty(trim($colorsStr))) return '';
    $parts = array_map('trim', explode(',', $colorsStr));
    $fixed = [];
    foreach ($parts as $p) {
        if (empty($p)) continue;
        // Already has hex?
        if (str_contains($p, '#')) {
            $fixed[] = $p;
        } else {
            $hex = nameToHex($p, $map);
            $fixed[] = "{$p}:{$hex}";
        }
    }
    return implode(', ', $fixed);
}

function fixDropboxUrl(string $url): string
{
    if (str_contains($url, 'dropbox.com')) {
        if (str_contains($url, 'dl=0')) {
            return str_replace('dl=0', 'dl=1', $url);
        } elseif (!str_contains($url, 'dl=1')) {
            return $url . (str_contains($url, '?') ? '&' : '?') . 'dl=1';
        }
    }
    return $url;
}

// Process
$in = fopen($input, 'r');
$out = fopen($output, 'w');

$headers = fgetcsv($in);
fputcsv($out, $headers);

$colorsIdx = array_search('colors', array_map('strtolower', $headers));
$imageIdx  = array_search('image_url', array_map('strtolower', $headers));

$count = 0;
while (($row = fgetcsv($in)) !== false) {
    // Fix colors column
    if ($colorsIdx !== false && isset($row[$colorsIdx])) {
        $row[$colorsIdx] = fixColors($row[$colorsIdx], $colorMap);
    }
    // Fix image URL
    if ($imageIdx !== false && isset($row[$imageIdx])) {
        $row[$imageIdx] = fixDropboxUrl($row[$imageIdx]);
    }
    fputcsv($out, $row);
    $count++;
}

fclose($in);
fclose($out);

echo "Done! Fixed {$count} rows.\n";
echo "Output: {$output}\n";

// Show first 3 rows as preview
$preview = fopen($output, 'r');
$h = fgetcsv($preview);
echo "\nPreview (colors + image_url):\n";
for ($i = 0; $i < 3; $i++) {
    $r = fgetcsv($preview);
    if (!$r) break;
    echo "  [{$r[0]}] colors: " . ($r[$colorsIdx] ?? '?') . "\n";
    echo "          image:  " . substr($r[$imageIdx] ?? '', 0, 80) . "...\n";
}
fclose($preview);
