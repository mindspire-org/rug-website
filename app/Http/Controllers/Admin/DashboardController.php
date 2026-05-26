<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue'  => Order::where('payment_status', 'paid')->sum('total'),
            'total_orders'   => Order::count(),
            'total_products' => Product::count(),
            'total_customers'=> User::where('role', 'client')->count(),
        ];
        $recentOrders = Order::with('user')->latest()->take(10)->get();
        $topProducts = Product::withCount('wishlists')
            ->orderByDesc('wishlists_count')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts'));
    }
}
