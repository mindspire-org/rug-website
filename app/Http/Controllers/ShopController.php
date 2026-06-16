<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /** Colour filter groups → keywords matched against product_colors.color_name */
    private const COLOR_GROUPS = [
        'Neutrals'    => ['neutral', 'beige', 'cream', 'ivory', 'sand', 'stone', 'taupe', 'oat', 'greige', 'linen', 'natural', 'oyster', 'champagne', 'fawn', 'pewter', 'white', 'café', 'toffee', 'caramel'],
        'Blues'       => ['blue', 'navy', 'teal', 'lagoon', 'indigo', 'denim', 'aqua', 'azure', 'royal'],
        'Reds'        => ['red', 'rust', 'terracotta', 'crimson', 'burgundy', 'wine', 'apple', 'brick', 'merlot'],
        'Greens'      => ['green', 'sage', 'olive', 'moss', 'emerald', 'forest', 'fern'],
        'Warm Tones'  => ['gold', 'amber', 'toffee', 'caramel', 'brown', 'tan', 'copper', 'bronze', 'mustard', 'ochre', 'blush', 'peach', 'rose', 'terracotta'],
        'Cool Tones'  => ['grey', 'gray', 'charcoal', 'silver', 'slate', 'pewter', 'steel'],
        'Yellow'      => ['yellow', 'gold', 'mustard', 'citron', 'lemon'],
    ];

    /** Material filter groups → keywords matched against products.material */
    private const MATERIAL_GROUPS = [
        'Wool'               => ['wool'],
        'Wool & Silk'        => ['wool & silk', 'wool and silk', 'silk and wool', 'silkette'],
        'Silk'               => ['silk'],
        'Natural Fibers'     => ['cotton', 'jute', 'sisal', 'seagrass', 'hemp', 'bamboo', 'viscose', 'linen'],
        'Performance Fibers' => ['polypropylene', 'poly', 'nylon', 'acrylic', 'polyester', 'solution dyed', 'olefin'],
    ];

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

        // ── Color (groups matched by keyword against product_colors.color_name) ──
        if ($request->filled('color')) {
            $colors = (array) $request->color;
            $keywords = [];
            foreach ($colors as $group) {
                foreach (self::COLOR_GROUPS[$group] ?? [$group] as $kw) {
                    $keywords[] = $kw;
                }
            }
            $query->whereHas('colors', function ($q) use ($keywords) {
                $q->where(function ($sub) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $sub->orWhere('color_name', 'like', '%' . $kw . '%');
                    }
                });
            });
        }

        // ── Material (design groups matched by keyword against the material column) ──
        if ($request->filled('material')) {
            $materials = (array) $request->material;
            $keywords = [];
            foreach ($materials as $group) {
                foreach (self::MATERIAL_GROUPS[$group] ?? [$group] as $kw) {
                    $keywords[] = $kw;
                }
            }
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('material', 'like', '%' . $kw . '%');
                }
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

        // ── Filter options ───────────────────────────────────────────────
        // Colour + Material stay as the design's curated GROUPS (rendered from the
        // blade defaults, matched by keyword in the queries above) — showing 193 raw
        // colours / junk material rows would be unusable. Pattern/Size/Construction
        // are derived from REAL product data so the values match exactly.
        $realOptions = array_filter([
            'pattern'      => Product::active()->whereNotNull('style')->where('style', '!=', '')->distinct()->orderBy('style')->pluck('style')->all(),
            'size'         => \App\Models\ProductDimensionPrice::whereNotNull('label')->where('label', '!=', '')->distinct()->orderBy('label')->pluck('label')->all(),
            'construction' => Category::whereIn('slug', ['hand-knotted', 'hand-tufted', 'machine-loomed', 'flat-weave', 'hand-loomed'])->pluck('name')->all(),
        ]);

        $filterOptions = $realOptions;

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
