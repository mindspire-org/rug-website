<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
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

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < count($headers)) {
                    $errors[] = "Row {$rowNum}: Column count mismatch.";
                    continue;
                }

                $data = array_combine($headers, $row);

                if (empty($data['name'])) {
                    $errors[] = "Row {$rowNum}: Name is required.";
                    continue;
                }

                $slug = !empty($data['slug']) ? $data['slug'] : Str::slug($data['name']);
                $slugBase = $slug;
                $counter = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $counter++;
                }

                $product = Product::create([
                    'name' => $data['name'],
                    'slug' => $slug,
                    'description' => $data['description'] ?? '',
                    'price' => floatval($data['price'] ?? 0),
                    'sale_price' => !empty($data['sale_price']) ? floatval($data['sale_price']) : null,
                    'category_id' => !empty($data['category_id']) ? intval($data['category_id']) : null,
                    'material' => $data['material'] ?? null,
                    'origin' => $data['origin'] ?? null,
                    'style' => $data['style'] ?? null,
                    'stock' => intval($data['stock'] ?? 0),
                    'status' => 'active',
                ]);

                // Handle image URL if provided
                if (!empty($data['image_url'])) {
                    try {
                        $imageContent = file_get_contents($data['image_url']);
                        if ($imageContent) {
                            $ext = pathinfo(parse_url($data['image_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $filename = 'products/' . Str::random(20) . '.' . $ext;
                            Storage::disk('public')->put($filename, $imageContent);
                            ProductImage::create([
                                'product_id' => $product->id,
                                'path' => $filename,
                                'is_primary' => true,
                                'sort_order' => 0,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        $errors[] = "Row {$rowNum}: Failed to download image.";
                    }
                }

                $imported++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        fclose($handle);

        $msg = "Imported {$imported} products successfully.";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' rows had errors.';
        }

        return back()->with('success', $msg)->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-import-template.csv"',
        ];

        $callback = function () {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['name', 'slug', 'description', 'price', 'sale_price', 'category_id', 'material', 'origin', 'style', 'stock', 'image_url']);
            fputcsv($fh, ['Tabriz Heritage Rug', 'tabriz-heritage', 'Hand-knotted wool rug', '4500', '', '1', 'Wool', 'Iran', 'Traditional', '5', 'https://example.com/rug.jpg']);
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }
}
