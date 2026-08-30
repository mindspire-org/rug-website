<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductDimensionPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportProductsCsv extends Command
{
    protected $signature = 'products:import-csv {file} {--update : Also update existing products} {--dry-run : Show what would be imported without saving}';
    protected $description = 'Import products from the Costikyan master spreadsheet CSV';

    private const CATEGORY_MAP = [
        'In Stock'      => 'in-stock',
        'Made to Order' => 'made-to-order',
        'Custom Size'   => 'custom-designs',
    ];

    private const CONSTRUCTION_MAP = [
        'Hand Knotted'              => 'Hand-Knotted',
        'Hand Tufted'               => 'Hand-Tufted',
        'Handloomed'                => 'Hand-Loomed',
        'Hand Loomed'               => 'Hand-Loomed',
        'Flatweave'                 => 'Flatweave',
        'Reversible Flatweave'      => 'Flatweave',
        'Hand Woven Flat Weave'     => 'Flatweave',
        'Power Loom'                => 'Machine Made',
        'Powerloom'                 => 'Machine Made',
        'Wilton Woven'              => 'Machine Made',
        'Tufted'                    => 'Hand-Tufted',
        'Woven'                     => 'Machine Made',
        'Nylon Hand Woven'          => 'Hand-Loomed',
        'Poly Hand Loomed'          => 'Hand-Loomed',
    ];

    private const STYLE_MAP = [
        'Traditional'  => 'Traditional',
        'Transitional' => 'Traditional',
        'Contemporary' => 'Modern',
        'Modern'       => 'Modern',
    ];

    private const SIZE_LABELS = [
        14 => '6x9',
        15 => '8x10',
        16 => '9x12',
        17 => '10x14',
        18 => '12x15',
    ];

    private const SIZE_DIMENSIONS = [
        '6x9'   => [6, 9],
        '8x10'  => [8, 10],
        '9x12'  => [9, 12],
        '10x14' => [10, 14],
        '12x15' => [12, 15],
    ];

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $dryRun = $this->option('dry-run');
        $update = $this->option('update');

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        $categories = [];
        foreach (self::CATEGORY_MAP as $label => $slug) {
            $cat = Category::where('slug', $slug)->first();
            if ($cat) $categories[$label] = $cat->id;
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || $row[0] === '*Vendor') continue;

            $sku        = trim($row[1] ?? '');
            $collection = trim($row[2] ?? '');
            $type       = trim($row[3] ?? '');
            $design     = trim($row[4] ?? '');
            $refColor   = trim($row[5] ?? '');
            $colorName  = trim($row[6] ?? '');
            $cost       = floatval($row[7] ?? 0);
            $pricePerFt = floatval($row[8] ?? 0);
            $matConst   = trim($row[10] ?? '');
            $origin     = trim($row[11] ?? '');
            $style      = trim($row[12] ?? '');
            $desc       = trim($row[13] ?? '');
            $addedOnBE  = ($row[19] ?? '') === '1';
            $visible    = ($row[20] ?? '') === '1';

            if (empty($sku)) continue;

            $parts = array_map('trim', explode(' - ', $matConst, 2));
            $material = $parts[0] ?: null;
            $rawConstruction = $parts[1] ?? $parts[0] ?? '';
            $construction = self::CONSTRUCTION_MAP[$rawConstruction] ?? null;
            if (!$construction && str_contains($rawConstruction, 'Hand Tufted')) {
                $construction = 'Hand-Tufted';
            }
            if (!$construction && str_contains($matConst, 'Hand Tufted')) {
                $construction = 'Hand-Tufted';
            }

            $mappedStyle = self::STYLE_MAP[trim($style)] ?? (self::STYLE_MAP[ucfirst(strtolower(trim($style)))] ?? null);

            $productName = $collection . ' ' . $sku;
            $categoryId = $categories[$type] ?? ($categories['In Stock'] ?? 1);

            $refinedColor = $refColor;
            if (str_contains($refinedColor, ',')) {
                $refinedColor = trim(explode(',', $refinedColor)[0]);
            }
            if ($refinedColor === 'Blue') $refinedColor = 'Blues';
            if ($refinedColor === 'Red') $refinedColor = 'Reds';

            $availability = match($type) {
                'In Stock'      => 'In Stock',
                'Made to Order' => 'Made to Order',
                'Custom Size'   => 'Custom Size',
                default         => null,
            };

            $existing = Product::where('sku', $sku)->first();

            if ($existing && !$update) {
                $skipped++;
                continue;
            }

            $smallestPrice = null;
            for ($col = 14; $col <= 18; $col++) {
                $p = floatval($row[$col] ?? 0);
                if ($p > 0 && ($smallestPrice === null || $p < $smallestPrice)) {
                    $smallestPrice = $p;
                }
            }

            if ($dryRun) {
                $action = $existing ? 'UPDATE' : 'CREATE';
                $this->line("[$action] $productName (SKU: $sku) - \${$smallestPrice} - $type - $material - $construction");
                if ($existing) $updated++; else $created++;
                continue;
            }

            try {
                $productData = [
                    'name'          => $productName,
                    'sku'           => $sku,
                    'description'   => $desc ?: null,
                    'price'         => $smallestPrice ?: $pricePerFt,
                    'category_id'   => $categoryId,
                    'status'        => $visible ? 'active' : 'draft',
                    'material'      => $material,
                    'origin'        => $origin ?: null,
                    'style'         => $mappedStyle ?? $style,
                    'refined_color' => $refinedColor ?: null,
                    'construction'  => $construction,
                    'availability'  => $availability,
                    'stock'         => $type === 'In Stock' ? 5 : 0,
                ];

                if ($existing) {
                    $existing->update($productData);
                    $product = $existing;
                    $updated++;
                } else {
                    $productData['slug'] = Str::slug($productName) . '-' . Str::random(4);
                    $product = Product::create($productData);
                    $created++;
                }

                if ($colorName) {
                    $product->colors()->delete();
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $colorName,
                        'color_hex'  => '#000000',
                    ]);
                }

                $product->dimensionPrices()->delete();
                for ($col = 14; $col <= 18; $col++) {
                    $p = floatval($row[$col] ?? 0);
                    if ($p <= 0) continue;
                    $label = self::SIZE_LABELS[$col] ?? null;
                    if (!$label) continue;
                    [$w, $l] = self::SIZE_DIMENSIONS[$label];
                    ProductDimensionPrice::create([
                        'product_id' => $product->id,
                        'label'      => $label,
                        'width'      => $w,
                        'length'     => $l,
                        'shape'      => 'rectangular',
                        'price'      => $p,
                        'stock'      => $type === 'In Stock' ? 5 : 0,
                        'is_default' => $col === 14,
                    ]);
                }

            } catch (\Exception $e) {
                $errors[] = "SKU $sku: " . $e->getMessage();
            }
        }

        fclose($handle);

        $this->info("Import complete: $created created, $updated updated, $skipped skipped.");
        if ($errors) {
            $this->warn(count($errors) . " errors:");
            foreach ($errors as $e) $this->line("  - $e");
        }

        return 0;
    }
}
