<?php
/**
 * Bulk import Costikyan product photos from the downloads folder.
 * Matches each file to a product by base name, then REPLACES that product's
 * existing images with all matching folder files (Overall = primary, then
 * closeups/room). Unmatched files are skipped and reported.
 *
 * Usage:
 *   php import_product_images.php          (DRY RUN — prints plan, writes nothing)
 *   php import_product_images.php --commit (actually writes to DB + storage)
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;

$COMMIT = in_array('--commit', $argv);
// Source folder: overridable via IMPORT_DIR env var (used on the production server).
$dir = getenv('IMPORT_DIR') ?: 'C:/Users/kotaebah/Downloads/All Costikyan Custom Carpet Products (1)/All Costikyan Custom Carpet Products';

$files = array_values(array_filter(scandir($dir), fn($f) => preg_match('/\.(jpe?g)$/i', $f)));

function norm($s) {
    $s = strtolower(preg_replace('/\.(jpe?g)$/i', '', $s));
    $s = str_replace('_', ' ', $s);
    $s = preg_replace('/[-]+/', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

// Lower rank = shown first. Overall/base become primary; closeups last.
function viewRank($f) {
    $n = strtolower($f);
    // Closeups always rank last, even if the filename also says "resized".
    if (strpos($n,'closeup')!==false || strpos($n,'close up')!==false) return 4;
    if (strpos($n,'overall')!==false || strpos($n,'overal')!==false || strpos($n,'ovewrall')!==false) return 0;
    if (strpos($n,'resized')!==false) return 1;
    if (strpos($n,'room')!==false || strpos($n,'floor')!==false || strpos($n,'corner')!==false || strpos($n,'side')!==false) return 3;
    return 2; // plain / no view keyword
}

$products = Product::orderByRaw('LENGTH(name) DESC')->get();

$map = [];        // product_id => ['product'=>P, 'files'=>[]]
$unmatched = [];

foreach ($files as $f) {
    $nf = norm($f);
    $hit = null;
    foreach ($products as $p) {
        $np = norm($p->name);
        if ($np === '') continue;
        if ($nf === $np || str_starts_with($nf, $np.' ')) { $hit = $p; break; }
    }
    if ($hit) {
        $map[$hit->id]['product'] = $hit;
        $map[$hit->id]['files'][] = $f;
    } else {
        $unmatched[] = $f;
    }
}

// Sort each product's files by view rank then natural name
foreach ($map as $pid => &$entry) {
    usort($entry['files'], function($a,$b){
        $ra=viewRank($a); $rb=viewRank($b);
        return $ra===$rb ? strnatcasecmp($a,$b) : $ra-$rb;
    });
}
unset($entry);

echo ($COMMIT ? "*** COMMIT MODE ***\n" : "=== DRY RUN (no changes) ===\n");
echo "Products receiving images: ".count($map)."\n";
echo "Matched files: ".(count($files)-count($unmatched))."   Unmatched: ".count($unmatched)."\n\n";

$totalImgs = 0;
foreach ($map as $entry) {
    $p = $entry['product'];
    $n = count($entry['files']);
    $totalImgs += $n;
    echo sprintf("[#%d] %s  (%d imgs, replacing %d existing)\n", $p->id, $p->name, $n, $p->images()->count());
    foreach ($entry['files'] as $i => $f) {
        echo "     ".($i===0 ? "PRIMARY " : "        ").$f."\n";
    }
}
echo "\nTotal images to attach: $totalImgs\n";

if (!$COMMIT) {
    echo "\n(dry run — rerun with --commit to apply)\n";
    return;
}

// ---- COMMIT ----
$storageBase = storage_path('app/public/');
@mkdir($storageBase.'products', 0755, true);
$done = 0;
foreach ($map as $entry) {
    $p = $entry['product'];

    // Remove existing images (rows + files)
    foreach ($p->images()->get() as $old) {
        foreach ([storage_path('app/public/'.$old->path), public_path('storage/'.$old->path)] as $op) {
            if (is_file($op)) @unlink($op);
        }
        $old->delete();
    }

    foreach ($entry['files'] as $i => $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        $rel = 'products/'.bin2hex(random_bytes(20)).'.'.$ext;
        $dest = $storageBase.$rel;
        if (!@copy($dir.'/'.$f, $dest)) {
            echo "  !! copy failed: $f\n";
            continue;
        }
        ProductImage::create([
            'product_id' => $p->id,
            'path'       => $rel,
            'sort_order' => $i,
            'is_primary' => $i === 0,
        ]);
    }
    $done++;
}
echo "\nDone. Updated $done products with $totalImgs images.\n";
