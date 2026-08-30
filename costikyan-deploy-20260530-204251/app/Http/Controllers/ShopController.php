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
        $query = Product::active()->with(['primaryImage', 'colors', 'category']);

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

        // ── Material (multi-select array) ────────────────────────
        if ($request->filled('material')) {
            $query->whereIn('material', (array) $request->material);
        }

        // ── Style / Pattern ──────────────────────────────────────
        if ($request->filled('pattern')) {
            $query->whereIn('style', (array) $request->pattern);
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
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('material', 'like', '%' . $request->search . '%');
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

        // ── Filter options (admin-editable via Settings) ──────────
        $filterOptions = json_decode(Setting::get('filter_options', '{}'), true) ?: [];

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
