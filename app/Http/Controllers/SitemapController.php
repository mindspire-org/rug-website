<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::active()->select('slug', 'updated_at')->latest()->get();
        $categories = Category::where('is_active', true)->select('slug', 'updated_at')->get();

        $content = view('sitemap', compact('products', 'categories'))->render();

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $content = "User-agent: *\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /dashboard/\n";
        $content .= "Disallow: /trade-portal/\n";
        $content .= "Disallow: /cart/\n";
        $content .= "Disallow: /checkout/\n";
        $content .= "Allow: /shop/\n";
        $content .= "Allow: /\n";
        $content .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
