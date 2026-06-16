<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['primaryImage', 'images', 'dimensionPrices', 'colors', 'category', 'filterValues.attribute']);

        // ── Tab filter ──────────────────────────────────────────
        $tab = $request->get('tab', 'all');
        match ($tab) {
            'signature'  => $query->where('featured', true),
            'bestseller' => $query->where('is_bestseller', true),
            'new'        => $query->where('is_new_arrival', true),
            default      => null,
        };

        // ── Category ────────────────────────────────────────────
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) $query->where('category_id', $category->id);
        }

        // ── Budget ──────────────────────────────────────────────
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // ── Color (via product_colors.color_name) ────────────────
        if ($request->filled('color')) {
            $colors = (array) $request->color;
            $query->whereHas('colors', function ($q) use ($colors) {
                $q->whereIn('color_name', $colors);
            });
        }

        // ── Material (via product_filter_values OR direct column) ──
        if ($request->filled('material')) {
            $materials = (array) $request->material;
            $query->where(function ($q) use ($materials) {
                // Try direct column first
                $q->whereIn('material', $materials);
                // Also try filter values
                $q->orWhereHas('filterValues', function ($sq) use ($materials) {
                    $sq->whereIn('value', $materials)
                        ->orWhereIn('display_value', $materials);
                });
            });
        }

        // ── Pattern / Style (via product_filter_values OR direct column) ──
        if ($request->filled('pattern')) {
            $patterns = (array) $request->pattern;
            $query->where(function ($q) use ($patterns) {
                $q->whereIn('style', $patterns)
                  ->orWhereHas('filterValues', function ($sq) use ($patterns) {
                      $sq->whereIn('value', $patterns)
                          ->orWhereIn('display_value', $patterns);
                  });
            });
        }

        // ── Construction (matches construction-type category or filter values) ──
        if ($request->filled('construction')) {
            $constructions = (array) $request->construction;
            $query->where(function ($q) use ($constructions) {
                $q->whereHas('category', function ($c) use ($constructions) {
                        $c->whereIn('name', $constructions);
                    })
                  ->orWhereHas('filterValues', function ($sq) use ($constructions) {
                        $sq->whereIn('value', $constructions)
                            ->orWhereIn('display_value', $constructions);
                    });
            });
        }

        // ── Room (via product_filter_values) ───────────────────
        if ($request->filled('room')) {
            $rooms = (array) $request->room;
            $query->whereHas('filterValues', function ($q) use ($rooms) {
                $q->whereIn('value', $rooms)
                  ->orWhereIn('display_value', $rooms);
            });
        }

        // ── Size (via product_dimension_prices) ─────────────────
        if ($request->filled('size')) {
            $sizes = (array) $request->size;
            $query->whereHas('dimensionPrices', function ($q) use ($sizes) {
                $q->whereIn('label', $sizes);
            });
        }

        // ── Availability (maps to product flags) ─────────────────
        if ($request->filled('availability')) {
            $avail = (array) $request->availability;
            $query->where(function ($q) use ($avail) {
                if (in_array('In Stock', $avail))      $q->orWhere('stock', '>', 0);
                if (in_array('Custom Size', $avail))   $q->orWhere('featured', true);
                if (in_array('Made to Order', $avail)) $q->orWhere('stock', 0);
            });
        }

        // ── Search ───────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('material', 'like', '%' . $search . '%');
            });
        }

        // ── Sort ─────────────────────────────────────────────────
        $sort = $request->get('sort', 'featured');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->orderByDesc('featured')->orderBy('name'),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $materials  = Product::active()->whereNotNull('material')->distinct()->pluck('material');

        // ── Filter options derived from REAL product data so selections actually match ──
        $colorRows = \App\Models\ProductColor::query()
            ->whereNotNull('color_name')->where('color_name', '!=', '')
            ->get(['color_name', 'color_hex'])
            ->unique('color_name')->sortBy('color_name')->values();

        $realOptions = [
            'color'        => $colorRows->map(fn ($c) => ['name' => $c->color_name, 'hex' => $c->color_hex ?: '#cccccc'])->all(),
            'pattern'      => Product::active()->whereNotNull('style')->where('style', '!=', '')->distinct()->orderBy('style')->pluck('style')->all(),
            'material'     => Product::active()->whereNotNull('material')->where('material', '!=', '')->distinct()->orderBy('material')->pluck('material')->all(),
            'size'         => \App\Models\ProductDimensionPrice::whereNotNull('label')->where('label', '!=', '')->distinct()->orderBy('label')->pluck('label')->all(),
            'construction' => Category::whereIn('slug', ['hand-knotted', 'hand-tufted', 'machine-loomed', 'flat-weave', 'hand-loomed'])->pluck('name')->all(),
        ];

        // Admin-configured overrides (Settings) take precedence where present
        $adminOptions = json_decode(Setting::get('filter_options', '{}'), true) ?: [];
        $filterOptions = array_merge(array_filter($realOptions), $adminOptions);

        return view('shop.index', compact('products', 'categories', 'materials', 'filterOptions'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->active()
            ->with(['images', 'colors', 'sizes', 'category', 'reviews.user'])
            ->firstOrFail();

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['primaryImage', 'colors'])
            ->take(4)->get();

        return view('shop.show', compact('product', 'related'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::active()
            ->with(['primaryImage', 'colors'])
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
                      ->orWhere('material', 'like', "%{$q}%");
            })
            ->paginate(12)->withQueryString();

        return view('shop.search', compact('products', 'q'));
    }
}
