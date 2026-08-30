<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductFilterAttribute;
use App\Models\ProductFilterValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'images')->latest();
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
                // Only search sku if the column exists (some older schemas may not have it yet)
                if (\Illuminate\Support\Facades\Schema::hasColumn('products', 'sku')) {
                    $q->orWhere('sku', 'like', '%' . $search . '%');
                }
            });
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Stock status filter
        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }
        
        // Featured filter
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured == '1' ? 1 : 0);
        }
        
        // Material filter
        if ($request->filled('material')) {
            $query->where('material', $request->material);
        }
        
        // Origin filter
        if ($request->filled('origin')) {
            $query->where('origin', $request->origin);
        }
        
        // Style filter
        if ($request->filled('style')) {
            $query->where('style', $request->style);
        }
        
        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Custom filter attributes
        if ($request->filled('filter_attribute') && $request->filled('filter_value')) {
            $query->whereHas('filterValues', function($q) use ($request) {
                $q->where('product_filter_attribute_id', $request->filter_attribute)
                  ->where('value', $request->filter_value);
            });
        }
        
        $products = $query->paginate(20)->withQueryString();
        $categories = Category::all();
        
        // Get unique values for filter dropdowns
        $materials = Product::distinct()->pluck('material')->filter()->values();
        $origins = Product::distinct()->pluck('origin')->filter()->values();
        $styles = Product::distinct()->pluck('style')->filter()->values();
        
        // Get filter attributes for dynamic filters
        $filterAttributes = ProductFilterAttribute::with('values')->where('is_active', true)->get();
        
        return view('admin.products.index', compact(
            'products', 'categories', 'materials', 'origins', 'styles', 'filterAttributes'
        ));
    }
    
    /**
     * Bulk edit form
     */
    public function bulkEdit(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        $selectAll = $request->input('select_all', false);
        
        // If select_all is enabled, get ALL product IDs
        if ($selectAll) {
            $productIds = Product::pluck('id')->toArray();
        } else {
            // Decode JSON string if needed
            if (is_string($productIds)) {
                $productIds = json_decode($productIds, true) ?? [];
            }
            
            if (empty($productIds) || !is_array($productIds)) {
                return back()->with('error', 'Please select at least one product.');
            }
        }
        
        $products = Product::whereIn('id', $productIds)->get();
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.products.bulk-edit', compact('products', 'categories'));
    }
    
    /**
     * Update multiple products
     */
    public function bulkUpdate(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        
        // Decode JSON string if needed
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true) ?? [];
        }
        
        if (empty($productIds) || !is_array($productIds)) {
            return back()->with('error', 'No products selected.');
        }
        
        $updates = [];
        
        // Build update array based on provided fields
        if ($request->filled('status')) {
            $updates['status'] = $request->status;
        }
        if ($request->filled('category_id')) {
            $updates['category_id'] = $request->category_id;
        }
        if ($request->filled('featured')) {
            $updates['featured'] = $request->featured == '1' ? 1 : 0;
        }
        if ($request->filled('is_bestseller')) {
            $updates['is_bestseller'] = $request->is_bestseller == '1' ? 1 : 0;
        }
        if ($request->filled('is_new_arrival')) {
            $updates['is_new_arrival'] = $request->is_new_arrival == '1' ? 1 : 0;
        }
        if ($request->filled('stock')) {
            $updates['stock'] = intval($request->stock);
        }
        if ($request->filled('price_adjustment')) {
            $adjustment = floatval($request->price_adjustment);
            $adjustmentType = $request->price_adjustment_type;
            
            if ($adjustment != 0) {
                foreach ($productIds as $id) {
                    $product = Product::find($id);
                    if ($product) {
                        if ($adjustmentType == 'fixed') {
                            $product->price = max(0, $product->price + $adjustment);
                        } elseif ($adjustmentType == 'percent') {
                            $product->price = max(0, $product->price * (1 + ($adjustment / 100)));
                        }
                        $product->save();
                    }
                }
            }
        }
        
        // Update other fields for all selected products
        if (!empty($updates)) {
            Product::whereIn('id', $productIds)->update($updates);
        }
        
        return redirect()->route('admin.products.index')
            ->with('success', count($productIds) . ' products updated successfully!');
    }
    
    /**
     * Export products to CSV
     */
    public function export(Request $request)
    {
        $query = Product::with('category');
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $products = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products-export-' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID', 'Name', 'Slug', 'SKU', 'Price', 'Sale Price', 'Category',
                'Status', 'Stock Status', 'Featured', 'Material', 'Origin', 'Style',
                'Construction', 'Description', 'Short Description', 'Weight', 'Stock',
                'Created At', 'Updated At'
            ]);
            
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->slug,
                    $product->sku,
                    $product->price,
                    $product->sale_price,
                    $product->category ? $product->category->name : '',
                    $product->status,
                    $product->stock_status,
                    $product->featured ? 'Yes' : 'No',
                    $product->material,
                    $product->origin,
                    $product->style,
                    $product->construction,
                    $product->description,
                    $product->short_description,
                    $product->weight,
                    $product->stock,
                    $product->created_at,
                    $product->updated_at,
                ]);
            }
            
            fclose($file);
        };
        
        return new StreamedResponse($callback, 200, $headers);
    }
    
    /**
     * Bulk delete products
     */
    public function bulkDestroy(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        $selectAll = $request->input('select_all', false);
        $confirmed = $request->input('confirm_delete_all', false);
        
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true) ?? [];
        }
        
        // If select_all is enabled, get ALL product IDs
        if ($selectAll) {
            $productIds = Product::pluck('id')->toArray();
        }
        
        if (empty($productIds) || !is_array($productIds)) {
            return back()->with('error', 'Please select at least one product.');
        }
        
        // Safety: if deleting all products, require explicit confirmation
        $totalProducts = Product::count();
        if (count($productIds) >= $totalProducts && $totalProducts > 1 && !$confirmed) {
            return back()->with('error', 'You are about to delete ALL products. Add confirm_delete_all=1 to proceed.');
        }
        
        $products = Product::whereIn('id', $productIds)->get();
        $count = 0;
        
        foreach ($products as $product) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->path);
            }
            $product->delete();
            $count++;
        }
        
        return back()->with('success', $count . ' products deleted successfully.');
    }
    
    /**
     * Toggle product status (active/inactive)
     */
    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        
        return back()->with('success', 'Product status updated to ' . $product->status);
    }
    
    /**
     * Bulk toggle status
     */
    public function bulkToggleStatus(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        $status = $request->input('status');
        $selectAll = $request->input('select_all', false);
        
        // If select_all is enabled, get ALL product IDs
        if ($selectAll) {
            $productIds = Product::pluck('id')->toArray();
        } elseif (is_string($productIds)) {
            $productIds = json_decode($productIds, true) ?? [];
        }
        
        if (empty($productIds) || !is_array($productIds)) {
            return back()->with('error', 'Please select at least one product.');
        }
        
        Product::whereIn('id', $productIds)->update(['status' => $status]);
        
        return back()->with('success', count($productIds) . ' products status updated to ' . $status);
    }
    
    /**
     * Import products from CSV with automatic image download
     */
    public function import(Request $request)
    {
        // Increase limits for long-running imports
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        
        // Prevent session timeout during import
        config(['session.lifetime' => 120]); // 2 hours
        
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:51200',
        ]);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $data = array_map('str_getcsv', file($path));
        $headers = array_shift($data);
        
        $imported = 0;
        $imageDownloaded = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            $totalRows = count($data);
            
            foreach ($data as $index => $row) {
                // Keep session alive every 10 products
                if ($index % 10 === 0) {
                    session()->regenerate();
                    \Log::info('Import progress', ['row' => $index, 'total' => $totalRows]);
                }
                
                if (count($row) !== count($headers)) {
                    $errors[] = "Row " . ($index + 2) . ": Column count mismatch";
                    continue;
                }
                
                $rowData = array_combine($headers, $row);
                
                // Find or create category - validate it exists
                $categoryId = 1; // Default to first category
                if (!empty($rowData['category_id'])) {
                    $requestedCategory = intval($rowData['category_id']);
                    // Check if category exists
                    $categoryExists = Category::where('id', $requestedCategory)->exists();
                    if ($categoryExists) {
                        $categoryId = $requestedCategory;
                    } else {
                        \Log::warning("Category $requestedCategory not found, using default (1)");
                    }
                } elseif (!empty($rowData['category'])) {
                    $category = Category::firstOrCreate(
                        ['name' => $rowData['category']],
                        ['slug' => Str::slug($rowData['category']), 'is_active' => true]
                    );
                    $categoryId = $category->id;
                }
                
                // Generate slug if not provided
                $slug = !empty($rowData['slug']) ? $rowData['slug'] : Str::slug($rowData['name']) . '-' . Str::random(4);
                
                // Check for existing product by SKU
                $product = null;
                if (!empty($rowData['sku'])) {
                    $existing = Product::where('sku', $rowData['sku'])->first();
                    if ($existing) {
                        // Update existing product
                        $existing->update([
                            'name' => $rowData['name'] ?? $existing->name,
                            'price' => $rowData['price'] ?? $existing->price,
                            'sale_price' => $rowData['sale_price'] ?? $existing->sale_price,
                            'category_id' => $categoryId ?? $existing->category_id,
                            'status' => $rowData['status'] ?? $existing->status,
                            'stock' => $rowData['stock'] ?? $existing->stock,
                            'material' => $rowData['material'] ?? $existing->material,
                            'origin' => $rowData['origin'] ?? $existing->origin,
                            'style' => $rowData['style'] ?? $existing->style,
                            'description' => $rowData['description'] ?? $existing->description,
                        ]);
                        $product = $existing;
                        $imported++;
                    }
                }
                
                // Create new product if not found
                if (!$product) {
                    // Parse price (remove $ and commas)
                    $price = 0;
                    if (!empty($rowData['price'])) {
                        $price = floatval(preg_replace('/[^0-9.]/', '', $rowData['price']));
                    }
                    
                    $salePrice = null;
                    if (!empty($rowData['sale_price'])) {
                        $salePrice = floatval(preg_replace('/[^0-9.]/', '', $rowData['sale_price']));
                    }
                    
                    // Prepare product data
                    $productData = [
                        'name' => $rowData['name'],
                        'slug' => $slug,
                        'sku' => $rowData['sku'] ?? null,
                        'description' => $rowData['description'] ?? null,
                        'short_description' => $rowData['short_description'] ?? null,
                        'price' => $price,
                        'category_id' => $categoryId,
                        'status' => $rowData['status'] ?? 'active',
                        'stock_status' => $rowData['stock_status'] ?? 'in_stock',
                        'featured' => isset($rowData['featured']) ? ($rowData['featured'] == '1' || strtolower($rowData['featured']) == 'yes') : false,
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
                    
                    $product = Product::create($productData);
                    $imported++;
                }
                
                // Download and attach image if URL provided
                if (!empty($rowData['image_url']) && $product) {
                    try {
                        $imagePath = $this->downloadAndSaveImage($rowData['image_url'], $product->sku);
                        if ($imagePath) {
                            ProductImage::create([
                                'product_id' => $product->id,
                                'path' => $imagePath,
                                'sort_order' => 0,
                                'is_primary' => true,
                            ]);
                            $imageDownloaded++;
                        }
                    } catch (\Exception $imgEx) {
                        $errors[] = "Row " . ($index + 2) . ": Product imported but image download failed - " . $imgEx->getMessage();
                    }
                }
            }
            
            DB::commit();
            
            $message = $imported . ' products imported successfully.';
            if ($imageDownloaded > 0) {
                $message .= ' (' . $imageDownloaded . ' images downloaded)';
            }
            if (!empty($errors)) {
                $message .= ' (' . count($errors) . ' warnings)';
            }
            
            return back()->with('success', $message)->with('import_errors', $errors);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Download image from URL and save to storage
     * Enhanced with logging and fallback handling
     */
    private function downloadAndSaveImage($imageUrl, $sku)
    {
        // Convert Dropbox preview URLs to direct download URLs
        $originalUrl = $imageUrl;
        $imageUrl = $this->convertToDirectDownloadUrl($imageUrl);
        
        // Log the attempt
        \Log::info('Attempting to download image', [
            'sku' => $sku,
            'original_url' => $originalUrl,
            'processed_url' => $imageUrl
        ]);
        
        // Validate URL
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            \Log::error('Invalid image URL', ['url' => $imageUrl, 'sku' => $sku]);
            throw new \Exception('Invalid image URL provided: ' . substr($imageUrl, 0, 50));
        }
        
        // Generate unique filename
        $extension = $this->getImageExtensionFromUrl($imageUrl);
        $filename = Str::slug($sku ?? 'product') . '-' . Str::random(8) . '.' . $extension;
        $storagePath = 'products/' . $filename;
        
        // Ensure products directory exists
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
            \Log::info('Created products directory');
        }
        
        // Try multiple download methods
        $imageData = null;
        $errors = [];
        
        // Method 1: file_get_contents
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'follow_location' => true,
                    'max_redirects' => 5,
                    'header' => [
                        'Accept: image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.9',
                    ],
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            
            $imageData = @file_get_contents($imageUrl, false, $context);
            if ($imageData) {
                \Log::info('Image downloaded via file_get_contents', ['sku' => $sku, 'size' => strlen($imageData)]);
            }
        } catch (\Exception $e) {
            $errors[] = 'file_get_contents: ' . $e->getMessage();
        }
        
        // Method 2: cURL fallback
        if (!$imageData) {
            try {
                $imageData = $this->downloadWithCurl($imageUrl);
                if ($imageData) {
                    \Log::info('Image downloaded via cURL', ['sku' => $sku, 'size' => strlen($imageData)]);
                }
            } catch (\Exception $e) {
                $errors[] = 'cURL: ' . $e->getMessage();
            }
        }
        
        if (!$imageData) {
            \Log::error('All download methods failed', [
                'sku' => $sku,
                'url' => $imageUrl,
                'errors' => $errors
            ]);
            throw new \Exception('Failed to download image from URL after multiple attempts: ' . substr($imageUrl, 0, 50));
        }
        
        // Verify it's actually an image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);
        
        $validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $validImageTypes)) {
            throw new \Exception('Downloaded file is not a valid image (got: ' . $mimeType . ')');
        }
        
        // Save to storage
        $saved = Storage::disk('public')->put($storagePath, $imageData);
        
        if (!$saved) {
            throw new \Exception('Failed to save image to storage');
        }
        
        return $storagePath;
    }
    
    /**
     * Convert preview URLs to direct download URLs
     */
    private function convertToDirectDownloadUrl($url)
    {
        // Convert Dropbox preview URLs to direct download
        if (strpos($url, 'dropbox.com') !== false) {
            // Replace dl=0 with dl=1 for direct download
            $url = str_replace('dl=0', 'dl=1', $url);
            // Add dl=1 if not present
            if (strpos($url, 'dl=1') === false) {
                $url .= (strpos($url, '?') !== false ? '&' : '?') . 'dl=1';
            }
        }
        
        return $url;
    }
    
    /**
     * Get image extension from URL
     */
    private function getImageExtensionFromUrl($url)
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        
        // Try to get extension from path
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Clean up extension (remove query params if any)
        if (strpos($extension, '?') !== false) {
            $extension = explode('?', $extension)[0];
        }
        
        // Default to jpg if no extension found
        if (empty($extension) || !in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'jpg';
        }
        
        return strtolower($extension);
    }
    
    /**
     * Download image using cURL as fallback
     */
    private function downloadWithCurl($url)
    {
        if (!function_exists('curl_init')) {
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($data !== false && $httpCode === 200) {
            return $data;
        }
        
        return false;
    }
    
    /**
     * Manage filter attributes
     */
    public function filterAttributes()
    {
        $attributes = ProductFilterAttribute::with('values')->get();
        return view('admin.products.filter-attributes', compact('attributes'));
    }
    
    /**
     * Store new filter attribute
     */
    public function storeFilterAttribute(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:product_filter_attributes',
            'display_name' => 'required|string|max:100',
            'type' => 'required|in:select,multiselect,text,number',
        ]);
        
        ProductFilterAttribute::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'type' => $request->type,
            'is_active' => true,
        ]);
        
        return back()->with('success', 'Filter attribute created successfully!');
    }
    
    /**
     * Store filter value
     */
    public function storeFilterValue(Request $request, ProductFilterAttribute $attribute)
    {
        $request->validate([
            'value' => 'required|string|max:100',
            'display_value' => 'required|string|max:100',
        ]);
        
        $attribute->values()->create([
            'value' => $request->value,
            'display_value' => $request->display_value,
        ]);
        
        return back()->with('success', 'Filter value added successfully!');
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $filterAttributes = ProductFilterAttribute::with('values')->where('is_active', true)->get();
        $filterOptions = \App\Http\Controllers\Admin\FilterController::getOptions();
        return view('admin.products.create', compact('categories', 'filterAttributes', 'filterOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images'      => 'nullable|array|max:4',
            'images.*'    => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['images', 'colors', '_token', 'filter_values', 'dimension_labels', 'dimension_widths', 'dimension_lengths', 'dimension_shapes', 'dimension_prices', 'dimension_sale_prices', 'dimension_stocks', 'dimension_default']);
        $data['slug']   = Str::slug($request->name) . '-' . Str::random(4);
        $data['featured']     = $request->has('featured');
        $data['is_bestseller']= $request->has('is_bestseller');
        $data['is_new_arrival']= $request->has('is_new_arrival');

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products', 'public');
                // Also write directly to public/storage/ for shared hosting compatibility
                $webPath = public_path('storage/' . $path);
                $webDir = dirname($webPath);
                if (!is_dir($webDir)) {
                    @mkdir($webDir, 0755, true);
                }
                if (is_dir($webDir) && !file_exists($webPath)) {
                    @copy($file->getRealPath(), $webPath);
                }
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }
        }

        if ($request->filled('color_names')) {
            foreach ($request->color_names as $i => $name) {
                if (!empty($name) && !empty($request->color_hexes[$i])) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $name,
                        'color_hex'  => $request->color_hexes[$i],
                    ]);
                }
            }
        }

        try {
            $this->syncFilterValues($product, $request);
            $this->syncDimensionPrices($product, $request);
        } catch (\Exception $e) {
            \Log::error('Product save sync error: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load('images', 'colors', 'sizes', 'dimensionPrices', 'filterValues.attribute');
        $categories = Category::where('is_active', true)->get();
        $filterAttributes = ProductFilterAttribute::with('values')->where('is_active', true)->get();
        $selectedFilterValues = $product->filterValues->pluck('id')->toArray();
        $filterOptions = \App\Http\Controllers\Admin\FilterController::getOptions();
        return view('admin.products.edit', compact('product', 'categories', 'filterAttributes', 'selectedFilterValues', 'filterOptions'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images'      => 'nullable|array|max:4',
            'images.*'    => 'nullable|image|max:5120',
        ]);

        $data = $request->except(['images', 'colors', '_token', '_method', 'filter_values', 'dimension_labels', 'dimension_widths', 'dimension_lengths', 'dimension_shapes', 'dimension_prices', 'dimension_sale_prices', 'dimension_stocks', 'dimension_default']);
        $data['featured']     = $request->has('featured');
        $data['is_bestseller']= $request->has('is_bestseller');
        $data['is_new_arrival']= $request->has('is_new_arrival');

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('products', 'public');
                // Also write directly to public/storage/ for shared hosting compatibility
                $webPath = public_path('storage/' . $path);
                $webDir = dirname($webPath);
                if (!is_dir($webDir)) {
                    @mkdir($webDir, 0755, true);
                }
                if (is_dir($webDir) && !file_exists($webPath)) {
                    @copy($file->getRealPath(), $webPath);
                }
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'sort_order' => $product->images->count() + $i,
                    'is_primary' => $product->images->isEmpty() && $i === 0,
                ]);
            }
        }

        if ($request->filled('color_names')) {
            $product->colors()->delete();
            foreach ($request->color_names as $i => $name) {
                if (!empty($name) && !empty($request->color_hexes[$i])) {
                    ProductColor::create([
                        'product_id' => $product->id,
                        'color_name' => $name,
                        'color_hex'  => $request->color_hexes[$i],
                    ]);
                }
            }
        }

        try {
            $this->syncFilterValues($product, $request);
            $this->syncDimensionPrices($product, $request);
        } catch (\Exception $e) {
            \Log::error('Product update sync error: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated!');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name) . '-' . Str::random(4);
        $newProduct->status = 'draft';
        $newProduct->created_at = now();
        $newProduct->updated_at = now();
        $newProduct->save();

        // Duplicate images
        foreach ($product->images as $img) {
            $newPath = $img->path;
            if (Storage::disk('public')->exists($img->path)) {
                $ext = pathinfo($img->path, PATHINFO_EXTENSION);
                $newPath = 'products/' . Str::random(40) . '.' . $ext;
                Storage::disk('public')->copy($img->path, $newPath);
            }
            ProductImage::create([
                'product_id' => $newProduct->id,
                'path' => $newPath,
                'sort_order' => $img->sort_order,
                'is_primary' => $img->is_primary,
            ]);
        }

        // Duplicate colors
        foreach ($product->colors as $color) {
            ProductColor::create([
                'product_id' => $newProduct->id,
                'color_name' => $color->color_name,
                'color_hex'  => $color->color_hex,
            ]);
        }

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', 'Product duplicated successfully!');
    }

    /**
     * Sync filter values for a product
     */
    private function syncFilterValues(Product $product, Request $request)
    {
        if (!$request->has('filter_values')) {
            $product->filterValues()->detach();
            return;
        }

        $values = $request->input('filter_values', []);
        $syncData = [];

        foreach ($values as $valueId) {
            $value = ProductFilterValue::find($valueId);
            if ($value) {
                $syncData[$valueId] = ['product_filter_attribute_id' => $value->product_filter_attribute_id];
            }
        }

        $product->filterValues()->sync($syncData);
    }

    /**
     * Sync dimension prices for a product
     */
    private function syncDimensionPrices(Product $product, Request $request)
    {
        // Delete existing dimension prices if updating
        if ($request->has('dimension_labels')) {
            $product->dimensionPrices()->delete();
        }

        if ($request->has('dimension_labels')) {
            $labels = $request->input('dimension_labels', []);
            $widths = $request->input('dimension_widths', []);
            $lengths = $request->input('dimension_lengths', []);
            $shapes = $request->input('dimension_shapes', []);
            $prices = $request->input('dimension_prices', []);
            $salePrices = $request->input('dimension_sale_prices', []);
            $stocks = $request->input('dimension_stocks', []);
            $defaultIndex = $request->input('dimension_default');

            foreach ($labels as $i => $label) {
                if (empty($label) && empty($widths[$i]) && empty($lengths[$i]) && empty($prices[$i])) {
                    continue;
                }

                $price = floatval(preg_replace('/[^0-9.]/', '', $prices[$i] ?? 0));
                $salePrice = null;
                if (!empty($salePrices[$i])) {
                    $salePrice = floatval(preg_replace('/[^0-9.]/', '', $salePrices[$i]));
                    if ($salePrice <= 0) $salePrice = null;
                }

                $product->dimensionPrices()->create([
                    'label' => $label ?: null,
                    'width' => !empty($widths[$i]) ? floatval($widths[$i]) : null,
                    'length' => !empty($lengths[$i]) ? floatval($lengths[$i]) : null,
                    'shape' => !empty($shapes[$i]) ? $shapes[$i] : null,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'stock' => !empty($stocks[$i]) ? intval($stocks[$i]) : 0,
                    'is_default' => (string)$i === (string)$defaultIndex,
                    'sort_order' => $i,
                ]);
            }
        }
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(403);
        }
        Storage::disk('public')->delete($image->path);
        $image->delete();

        // If we deleted the primary and other images exist, set the first as primary
        if ($product->images()->count() > 0 && !$product->images()->where('is_primary', true)->exists()) {
            $product->images()->first()->update(['is_primary' => true]);
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Image deleted.']);
        }

        return redirect()->route('admin.products.edit', ['product' => $product->id])->with('success', 'Image deleted.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            abort(403);
        }
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('success', 'Primary image updated.');
    }

    /**
     * Ensure the image is accessible from the web by copying it to public/storage/.
     * On shared hosting the symlink is often broken, so we create a real directory.
     */
    private function ensureImageAccessible(string $src, string $dst): void
    {
        if (!file_exists($src) || file_exists($dst)) {
            return;
        }

        $dir = dirname($dst);
        // On shared hosting the symlink is at public/storage, not the subdirectory.
        // If that parent symlink is broken, remove it so we can create a real directory.
        $parentDir = dirname($dir);
        if (is_link($parentDir) && !file_exists($parentDir)) {
            @unlink($parentDir);
        }
        if (is_link($dir) && !file_exists($dir)) {
            @unlink($dir);
        }
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (is_dir($dir)) {
            @copy($src, $dst);
        }
    }
}
