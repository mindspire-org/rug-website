<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $signatureProducts = Product::active()->featured()->with(['primaryImage', 'colors'])->take(8)->get();
        $bestsellers = Product::active()->bestsellers()->with(['primaryImage', 'colors'])->take(8)->get();
        $newArrivals = Product::active()->newArrivals()->with(['primaryImage', 'colors'])->take(8)->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->take(6)->get();

        return view('home', compact('signatureProducts', 'bestsellers', 'newArrivals', 'categories'));
    }
}
