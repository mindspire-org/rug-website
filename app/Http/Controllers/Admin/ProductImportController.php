<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductColor;
use App\Models\ProductFilterAttribute;
use App\Models\ProductFilterValue;
use App\Models\ProductDimensionPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    public function showForm()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.import', compact('categories'));
    }

    public function import(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 600);
        set_time_limit(600);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map('strtolower', array_map('trim', $headers));

        $expected = ['name', 'slug', 'description', 'price', 'category_id', 'material', 'origin', 'style', 'stock'];
        $missing = array_diff($expected, $headers);

        if (!empty($missing)) {
            fclose($handle);
            return back()->with('error', 'Missing columns: ' . implode(', ', $missing));
        }

        // ── Phase 1: Read ALL rows and GROUP by product name ──
        $grouped = []; // key = normalized product name
        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (count($row) < count($headers)) {
                continue; // skip malformed
            }
            $data = array_combine($headers, $row);
            if (empty($data['name'])) continue;

            $productKey = Str::slug(trim($data['name']));
            if (!isset($grouped[$productKey])) {
                $grouped[$productKey] = [
                    'base' => $data,
                    'rows' => [],
                ];
            }
            $grouped[$productKey]['rows'][] = $data;
        }
        fclose($handle);

        // ── Phase 2: Import product data (fast DB operations) ──
        $imported = 0;
        $errors = [];
        $batchSize = 10;
        $batchCount = 0;
        $pendingImages = []; // collect image jobs to run OUTSIDE transaction

        DB::beginTransaction();
        try {
            foreach ($grouped as $productKey => $group) {
                $base = $group['base'];
                $allRows = $group['rows'];

                // Check if product already exists in DB
                $existingProduct = Product::where('slug', Str::slug(trim($base['name'])))->first();
                $product = $existingProduct;

                if (!$product) {
                    $slug = !empty($base['slug']) ? $base['slug'] : Str::slug(trim($base['name']));
                    $slugBase = $slug;
                    $counter = 1;
                    while (Product::where('slug', $slug)->exists()) {
                        $slug = $slugBase . '-' . $counter++;
                    }

                    $firstCategory = Category::where('is_active', true)->first();
                    $categoryId = $firstCategory ? $firstCategory->id : null;
                    if (!empty($base['category_id'])) {
                        $cat = Category::find(intval($base['category_id']));
                        if ($cat) $categoryId = $cat->id;
                    }

                    $productData = [
                        'name' => trim($base['name']),
                        'slug' => $slug,
                        'description' => $base['description'] ?? '',
                        'price' => floatval(preg_replace('/[^0-9.]/', '', $base['price'] ?? 0)),
                        'category_id' => $categoryId,
                        'material' => $base['material'] ?? null,
                        'origin' => $base['origin'] ?? null,
                        'style' => $base['style'] ?? null,
                        'stock' => intval($base['stock'] ?? 0),
                        'status' => 'active',
                    ];

                    $sp = null;
                    if (!empty($base['sale_price'])) {
                        $sp = floatval(preg_replace('/[^0-9.]/', '', $base['sale_price']));
                        if ($sp > 0) $productData['sale_price'] = $sp;
                    }

                    $product = Product::create($productData);
                    $imported++;
                }

                // ── Merge all colors from all rows ──
                $mergedColors = [];
                foreach ($allRows as $rowData) {
                    $this->mergeColorsFromRow($mergedColors, $rowData);
                }
                $this->saveColors($product, $mergedColors);

                // ── Merge all dimensions from all rows ──
                foreach ($allRows as $rowData) {
                    $this->importDimensionPrices($product, $rowData);
                }

                // ── Collect image URLs for later (NOT inside transaction) ──
                $imageUrls = [];
                foreach ($allRows as $rowData) {
                    if (!empty($rowData['image_url'])) {
                        foreach (array_map('trim', explode(',', $rowData['image_url'])) as $url) {
                            if (!empty($url) && !in_array($url, $imageUrls)) {
                                $imageUrls[] = $url;
                            }
                        }
                    }
                }
                if (!empty($imageUrls)) {
                    $pendingImages[] = ['product_id' => $product->id, 'urls' => $imageUrls];
                }

                // ── Merge filter values from all rows ──
                foreach ($allRows as $rowData) {
                    $this->importFilterValues($product, $rowData, $headers);
                }

                $batchCount++;
                if ($batchCount >= $batchSize) {
                    DB::commit();
                    DB::beginTransaction();
                    $batchCount = 0;
                }
            }

            if ($batchCount > 0) {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        // ── Phase 3: Download images OUTSIDE transaction (slow network I/O) ──
        set_time_limit(0); // no timeout for image downloads
        $imageSuccess = 0;
        foreach ($pendingImages as $job) {
            $product = Product::find($job['product_id']);
            if (!$product) continue;
            foreach ($job['urls'] as $url) {
                $this->downloadImage($product, $url, $errors, count($job['urls']) === 1);
                $imageSuccess++;
            }
        }

        $msg = "Imported {$imported} products, downloaded {$imageSuccess} image(s).";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' error(s) — see details below.';
        }

        return back()->with('success', $msg)->with('import_errors', $errors);
    }

    /**
     * Import filter values from CSV columns like filter_color, filter_material
     */
    private function importFilterValues(Product $product, array $data, array $headers)
    {
        $syncData = [];
        
        foreach ($headers as $header) {
            if (str_starts_with($header, 'filter_')) {
                $attrName = substr($header, 7); // Remove 'filter_' prefix
                $valuesStr = $data[$header] ?? '';
                
                if (empty($valuesStr)) continue;
                
                // Find the filter attribute
                $attribute = ProductFilterAttribute::where('name', $attrName)->first();
                if (!$attribute) continue;
                
                // Split by comma for multiselect
                $values = array_map('trim', explode(',', $valuesStr));
                
                foreach ($values as $valName) {
                    if (empty($valName)) continue;
                    
                    $filterValue = ProductFilterValue::where('product_filter_attribute_id', $attribute->id)
                        ->where(function($q) use ($valName) {
                            $q->where('value', $valName)->orWhere('display_value', $valName);
                        })
                        ->first();
                    
                    if ($filterValue) {
                        $syncData[$filterValue->id] = ['product_filter_attribute_id' => $attribute->id];
                    }
                }
            }
        }
        
        if (!empty($syncData)) {
            $product->filterValues()->syncWithoutDetaching($syncData);
        }
    }

    /**
     * Collect colors from a CSV row into a merged list (avoids duplicates)
     */
    private function mergeColorsFromRow(array &$mergedColors, array $data)
    {
        $colorsStr = $data['colors'] ?? '';
        if (empty($colorsStr)) return;

        foreach (array_map('trim', explode(',', $colorsStr)) as $color) {
            if (empty($color)) continue;

            // Support explicit "Name: #hex" format if provided
            $hex = null;
            if (str_contains($color, ':')) {
                $parts = explode(':', $color, 2);
                $color = trim($parts[0]);
                $maybeHex = trim($parts[1]);
                if (preg_match('/^#[0-9A-Fa-f]{6}$/', $maybeHex)) {
                    $hex = $maybeHex;
                }
            }

            $name = trim($color);
            if (empty($name)) continue;

            // No explicit hex — guess from the color name
            if ($hex === null) {
                $hex = $this->guessHexFromName($name);
            }

            $key = Str::slug($name);
            $mergedColors[$key] = ['name' => $name, 'hex' => $hex];
        }
    }

    /**
     * Map a human color name to an approximate hex code.
     * Handles compound names like "Ivory - Gold" (uses the first known word).
     */
    private function guessHexFromName(string $name): string
    {
        $map = [
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

        $lower = strtolower($name);

        // Exact match first
        if (isset($map[$lower])) return $map[$lower];

        // Match any known word inside the name (e.g. "Ivory - Gold" → ivory)
        foreach (preg_split('/[\s\-_]+/', $lower) as $word) {
            if (isset($map[$word])) return $map[$word];
        }

        // Default neutral
        return '#B0ABA0';
    }

    /**
     * Save merged colors to a product (skipping existing ones)
     */
    private function saveColors(Product $product, array $mergedColors)
    {
        if (empty($mergedColors)) return;

        // Get existing color names for this product to avoid duplicates
        $existing = ProductColor::where('product_id', $product->id)
            ->pluck('color_name')
            ->map(fn($n) => Str::slug($n))
            ->toArray();

        foreach ($mergedColors as $key => $color) {
            if (in_array($key, $existing)) continue;
            ProductColor::create([
                'product_id' => $product->id,
                'color_name' => $color['name'],
                'color_hex'  => $color['hex'],
            ]);
        }
    }

    /**
     * Download image from URL using cURL (works even if allow_url_fopen is off)
     */
    private function downloadImage(Product $product, string $url, array &$errors, bool $isPrimary = false)
    {
        $url = $this->normalizeImageUrl(trim($url));
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = "Invalid image URL: {$url}";
            return;
        }

        try {
            $result = $this->fetchUrl($url);
            if (!$result || empty($result['content'])) {
                $errors[] = "Empty response for image: {$url}";
                return;
            }

            $imageContent = $result['content'];
            $contentType = $result['content_type'] ?? '';
            $finalUrl = $result['final_url'] ?? $url;

            if (strlen($imageContent) < 100) {
                $errors[] = "Image too small (" . strlen($imageContent) . " bytes): {$url}";
                return;
            }

            // Detect extension: first from Content-Type, then from final URL path
            $ext = $this->extFromContentType($contentType);
            if (!$ext) {
                $ext = pathinfo(parse_url($finalUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $ext = strtolower(preg_replace('/[^a-zA-Z]/', '', $ext));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $ext = 'jpg';
            }

            $filename = 'products/' . Str::random(20) . '.' . $ext;

            // PRIMARY: Write directly to public/storage/ — the web-facing path.
            // On shared hosting the symlink public/storage → storage/app/public is often broken.
            $publicPath = public_path('storage/' . $filename);
            $dir = dirname($publicPath);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($publicPath, $imageContent);

            // BACKUP: Also write to storage/app/public/ for Laravel Storage facade consistency
            $storagePath = storage_path('app/public/' . $filename);
            $storageDir = dirname($storagePath);
            if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);
            file_put_contents($storagePath, $imageContent);

            $hasPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->exists();

            ProductImage::create([
                'product_id' => $product->id,
                'path' => $filename,
                'is_primary' => ($isPrimary && !$hasPrimary),
                'sort_order' => ProductImage::where('product_id', $product->id)->count(),
            ]);
        } catch (\Throwable $e) {
            $errors[] = "Image download error ({$url}): " . $e->getMessage();
        }
    }

    /**
     * Map common Content-Type values to file extensions.
     */
    private function extFromContentType(string $contentType): ?string
    {
        $map = [
            'image/jpeg' => 'jpg', 'image/jpg' => 'jpg',
            'image/png' => 'png', 'image/gif' => 'gif',
            'image/webp' => 'webp', 'image/avif' => 'avif',
            'image/svg+xml' => 'svg',
        ];
        foreach ($map as $mime => $ext) {
            if (stripos($contentType, $mime) !== false) return $ext;
        }
        return null;
    }

    /**
     * Convert cloud share links (Dropbox, Google Drive) to direct-download URLs.
     * Dropbox dl=0 returns an HTML preview page, not the image.
     */
    private function normalizeImageUrl(string $url): string
    {
        if (empty($url)) return $url;

        // Dropbox: set dl=1 to force the file (cURL follows the 302 to the real file).
        if (str_contains($url, 'dropbox.com')) {
            if (str_contains($url, 'dl=0')) {
                $url = str_replace('dl=0', 'dl=1', $url);
            } elseif (!str_contains($url, 'dl=1')) {
                $url .= (str_contains($url, '?') ? '&' : '?') . 'dl=1';
            }
            return $url;
        }

        // Brandfolder: short links redirect to CDN image — just ensure we follow redirects
        if (str_contains($url, 'brandfolder.com')) {
            return $url; // cURL FOLLOWLOCATION handles the redirect chain
        }

        // Google Drive: convert share link to direct download
        if (str_contains($url, 'drive.google.com')) {
            if (preg_match('#/file/d/([^/]+)#', $url, $m)) {
                return 'https://drive.google.com/uc?export=download&id=' . $m[1];
            }
            if (preg_match('#[?&]id=([^&]+)#', $url, $m)) {
                return 'https://drive.google.com/uc?export=download&id=' . $m[1];
            }
        }

        return $url;
    }

    /**
     * Fetch URL content using cURL with user-agent (reliable on VPS).
     * Returns array with 'content', 'content_type', 'final_url' or null on failure.
     */
    private function fetchUrl(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => [
                'Accept: image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
            ],
        ]);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($content === false || $httpCode < 200 || $httpCode >= 300) {
            return null;
        }

        // Reject HTML pages (e.g. Dropbox preview) — must be a real image
        if (stripos((string) $contentType, 'text/html') !== false) {
            return null;
        }
        if (str_starts_with(ltrim($content), '<')) {
            return null;
        }

        return [
            'content' => $content,
            'content_type' => (string) $contentType,
            'final_url' => (string) $finalUrl,
        ];
    }

    /**
     * Import dimension prices from CSV columns like dim1_label, dim1_width, etc.
     */
    private function importDimensionPrices(Product $product, array $data)
    {
        $dimensions = [];
        
        // Scan for dimension columns (dim1_label, dim2_label, etc.)
        foreach ($data as $key => $value) {
            if (preg_match('/^dim(\d+)_(\w+)$/', $key, $matches)) {
                $index = $matches[1];
                $field = $matches[2];
                if (!isset($dimensions[$index])) {
                    $dimensions[$index] = [];
                }
                $dimensions[$index][$field] = $value;
            }
        }
        
        foreach ($dimensions as $i => $dim) {
            if (empty($dim['label']) && empty($dim['width']) && empty($dim['length'])) {
                continue;
            }
            
            $price = 0;
            if (!empty($dim['price'])) {
                $price = floatval(preg_replace('/[^0-9.]/', '', $dim['price']));
            }
            
            $salePrice = null;
            if (!empty($dim['sale_price'])) {
                $sp = floatval(preg_replace('/[^0-9.]/', '', $dim['sale_price']));
                if ($sp > 0) $salePrice = $sp;
            }
            
            ProductDimensionPrice::create([
                'product_id' => $product->id,
                'label' => $dim['label'] ?? null,
                'width' => !empty($dim['width']) ? floatval($dim['width']) : null,
                'length' => !empty($dim['length']) ? floatval($dim['length']) : null,
                'shape' => $dim['shape'] ?? 'rectangular',
                'price' => $price,
                'sale_price' => $salePrice,
                'stock' => !empty($dim['stock']) ? intval($dim['stock']) : 0,
                'is_default' => isset($dim['default']) && strtolower($dim['default']) === 'yes',
                'sort_order' => $i,
            ]);
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-import-template.csv"',
        ];

        $callback = function () {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, [
                'name', 'slug', 'description', 'price', 'sale_price', 'category_id',
                'material', 'origin', 'style', 'stock', 'image_url',
                'filter_color', 'filter_material', 'filter_construction', 'filter_pattern', 'filter_weave',
                'dim1_label', 'dim1_width', 'dim1_length', 'dim1_shape', 'dim1_price', 'dim1_sale_price', 'dim1_stock', 'dim1_default',
                'dim2_label', 'dim2_width', 'dim2_length', 'dim2_shape', 'dim2_price', 'dim2_sale_price', 'dim2_stock', 'dim2_default',
                'dim3_label', 'dim3_width', 'dim3_length', 'dim3_shape', 'dim3_price', 'dim3_sale_price', 'dim3_stock', 'dim3_default',
                'colors',
            ]);
            fputcsv($fh, [
                'Tabriz Heritage Rug', 'tabriz-heritage', 'Hand-knotted wool rug with medallion', '8500', '7200', '1',
                'Wool & Silk', 'Iran', 'Traditional', '8', 'https://example.com/rug.jpg',
                'Navy Blue, Cream', 'Wool & Silk', 'Hand-Knotted', 'Medallion', 'Persian',
                "6' x 9'", '6', '9', 'rectangular', '5200', '4800', '3', 'yes',
                "8' x 10'", '8', '10', 'rectangular', '8500', '7200', '5', 'no',
                "9' x 12'", '9', '12', 'rectangular', '11200', '9800', '2', 'no',
                'Navy Blue: #1a2744, Cream: #f5f0e8, Gold: #c9a227',
            ]);
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
