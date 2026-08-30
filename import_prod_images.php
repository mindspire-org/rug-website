<?php
/**
 * PRODUCTION importer: fills product images for the Costikyan live DB, where
 * products are named "Base SKU" (e.g. "Haven HV-134") with the colour held in the
 * colors relation. Matches each folder file (named "Name - Colour - View") to a
 * product by  base-name + colour.  Only products that currently have ZERO images
 * are filled; products that already have photos are left untouched.
 *
 *   php import_prod_images.php            (dry run)
 *   php import_prod_images.php --commit   (write)
 *
 * IMPORT_DIR env var points at the extracted image folder on the server.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\ProductImage;

$COMMIT = in_array('--commit', $argv);
$dir = getenv('IMPORT_DIR') ?: 'C:/Users/kotaebah/Downloads/All Costikyan Custom Carpet Products (1)/All Costikyan Custom Carpet Products';

// Files that base-name fallback mis-routes onto the wrong-colour product.
// Their correct product is reported as still-empty instead (safer than a wrong photo).
$blocklist = [
    'MALIBU-RESERVE-TOPANGA-CLARET Overall.jpg',  // Topanga Claret, not Malibu Teal
    'TRIUMPH-WONDER-STEEL-CELERY Overall.jpg',    // Wonder Steel Celery, not Triumph Wheat
    'Murano - Rivera Russet - Overall.jpg',       // Riviera Russet (MR-563), not Mediterranean Sky
];

$files = array_values(array_filter(scandir($dir), fn($f) => preg_match('/\.(jpe?g)$/i', $f) && !in_array($f, $blocklist, true)));

/** Normalise any string to lowercase words: strip punctuation/separators. */
function nrm($s) {
    $s = strtolower($s);
    $s = str_replace(['/', '_', "\t"], ' ', $s);
    $s = preg_replace('/[-]+/', ' ', $s);
    $s = preg_replace('/[^a-z0-9 ]/', ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

/** Strip view keywords + trailing junk from a FILE name -> "name colour". */
function fileKey($f) {
    $n = preg_replace('/\.(jpe?g)$/i', '', $f);
    $n = str_replace(['/', '_', "\t"], ' ', $n);
    $n = preg_replace('/[-]+/', ' ', $n);
    $n = strtolower($n);
    // remove view / junk tokens
    $kill = ['overall','overal','ovewrall','resized','closeup','close up','close',
             'room view','room','floor','corner','side view','side','shot',
             '100 knot','knot','opt','img','resize'];
    foreach ($kill as $k) $n = str_replace($k, ' ', $n);
    $n = preg_replace('/\bimg\b/', ' ', $n);
    $n = preg_replace('/\(.*?\)/', ' ', $n);          // (2) etc
    $n = preg_replace('/\b[a-z]?\d{1,4}\b/', ' ', $n);// stray codes/numbers a3 i3 085 699
    $n = preg_replace('/[^a-z0-9 ]/', ' ', $n);
    $n = preg_replace('/\s+/', ' ', $n);
    return trim($n);
}

/** Remove a trailing SKU token from a product name -> base. */
function prodBase($name) {
    $b = $name;
    // e.g. "HV-134", "KT-1361", "KT1360", "LL - 750", "GS1715", "SG-628", "MV-699"
    $b = preg_replace('/\b[A-Za-z]{2,3}\s*-?\s*\d{2,5}\b\s*$/', '', $b);
    // trailing pure number id e.g. "172505"
    $b = preg_replace('/\b\d{4,6}\b\s*$/', '', $b);
    return trim($b, " \t-");
}

// Build candidate keys per product
$products = Product::with('colors')->get();
$prodKeys = []; // [ ['id'=>, 'name'=>, 'keys'=>[...], 'hasImgs'=>bool], ... ]
foreach ($products as $p) {
    $base = prodBase($p->name);
    $colour = optional($p->colors->first())->color_name;
    $keys = [];
    $keys[] = nrm($p->name);                        // full name (covers "Flowstone Glacier")
    if ($colour) $keys[] = nrm($base.' '.$colour);  // base + colour ("haven sky natural")
    $keys[] = nrm($base);                           // base alone (last resort)
    $keys = array_values(array_unique(array_filter($keys)));
    // longest keys first for best specificity
    usort($keys, fn($a,$b)=>strlen($b)-strlen($a));
    $prodKeys[] = ['id'=>$p->id, 'name'=>$p->name, 'keys'=>$keys,
                   'hasImgs'=>$p->images()->count()>0];
}

function viewRank($f) {
    $n = strtolower($f);
    if (strpos($n,'closeup')!==false || strpos($n,'close up')!==false) return 4;
    if (strpos($n,'overall')!==false || strpos($n,'overal')!==false || strpos($n,'ovewrall')!==false) return 0;
    if (strpos($n,'resized')!==false) return 1;
    if (strpos($n,'room')!==false || strpos($n,'floor')!==false || strpos($n,'corner')!==false || strpos($n,'side')!==false) return 3;
    return 2;
}

// Match each file to the best product (longest key that the file key starts with / equals)
$assign = [];      // pid => [files]
$unmatched = [];
foreach ($files as $f) {
    $fk = fileKey($f);
    $best = null; $bestLen = 0;
    foreach ($prodKeys as $pk) {
        foreach ($pk['keys'] as $key) {
            if ($key === '') continue;
            if ($fk === $key || str_starts_with($fk.' ', $key.' ') || str_starts_with($key.' ', $fk.' ')) {
                if (strlen($key) > $bestLen) { $best = $pk; $bestLen = strlen($key); }
            }
        }
    }
    if ($best) $assign[$best['id']][] = $f;
    else $unmatched[] = $f;
}

$byId = [];
foreach ($prodKeys as $pk) $byId[$pk['id']] = $pk;

// Sort files per product
foreach ($assign as $pid=>&$fl) {
    usort($fl, fn($a,$b)=> viewRank($a)===viewRank($b) ? strnatcasecmp($a,$b) : viewRank($a)-viewRank($b));
}
unset($fl);

// Split into: fill-empty vs skip-already-imaged
$fill = []; $skipHasImgs = [];
foreach ($assign as $pid=>$fl) {
    if ($byId[$pid]['hasImgs']) $skipHasImgs[$pid] = $fl;
    else $fill[$pid] = $fl;
}

echo ($COMMIT?"*** COMMIT ***\n":"=== DRY RUN ===\n");
echo "Files: ".count($files)."  matched-to-a-product: ".(count($files)-count($unmatched))."  unmatched: ".count($unmatched)."\n";
echo "EMPTY products that will be FILLED: ".count($fill)."\n";
echo "Products already having images (files skipped): ".count($skipHasImgs)."\n\n";

echo "=== WILL FILL (empty products) ===\n";
$tot=0;
foreach ($fill as $pid=>$fl) {
    echo sprintf("[#%d] %s  (%d imgs)\n", $pid, $byId[$pid]['name'], count($fl));
    foreach ($fl as $i=>$f) { echo "     ".($i===0?"PRIMARY ":"        ").$f."\n"; $tot++; }
}
echo "\nTotal images to attach: $tot\n";

// list empty products that got NOTHING
$emptyNoMatch = [];
foreach ($prodKeys as $pk) {
    if (!$pk['hasImgs'] && !isset($fill[$pk['id']])) $emptyNoMatch[] = $pk['name'];
}
sort($emptyNoMatch);
echo "\n=== EMPTY products with NO folder match (".count($emptyNoMatch).") ===\n";
foreach ($emptyNoMatch as $n) echo "  $n\n";

echo "\n=== Unmatched files (".count($unmatched).") ===\n";
foreach ($unmatched as $u) echo "  $u\n";

if (!$COMMIT) { echo "\n(dry run)\n"; return; }

// COMMIT: only fill empties
$base = storage_path('app/public/');
@mkdir($base.'products', 0755, true);
$pubBase = public_path('storage/');
@mkdir($pubBase.'products', 0755, true);
$done=0;
foreach ($fill as $pid=>$fl) {
    foreach ($fl as $i=>$f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        $rel = 'products/'.bin2hex(random_bytes(20)).'.'.$ext;
        if (!@copy($dir.'/'.$f, $base.$rel)) { echo "  !! copy fail $f\n"; continue; }
        @copy($base.$rel, $pubBase.$rel); // real dir on Hostinger (no symlink)
        ProductImage::create(['product_id'=>$pid,'path'=>$rel,'sort_order'=>$i,'is_primary'=>$i===0]);
    }
    $done++;
}
echo "\nDone. Filled $done products with $tot images.\n";
