<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->isTeam()) {
            return redirect('/admin');
        }

        if ($user->isTrade()) {
            return redirect('/trade-portal');
        }

        // Core stats
        $ordersCount      = $user->orders()->count();
        $wishlistCount    = $user->wishlist()->count();
        $totalSpent       = $user->orders()->where('payment_status', 'paid')->sum('total');
        $pendingOrders    = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        $completedOrders  = $user->orders()->where('status', 'delivered')->count();
        $activeCartItems  = $user->cart ? $user->cart->items()->count() : 0;

        // Daily interaction data (orders per day for last 7 days)
        $dailyData = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'label' => $date->format('D'),
                'value' => $user->orders()->whereDate('created_at', $date)->count(),
            ];
        });

        // Monthly interaction data (orders per month for last 6 months)
        $monthlyData = collect(range(5, 0))->map(function ($monthsAgo) use ($user) {
            $date = Carbon::today()->startOfMonth()->subMonths($monthsAgo);
            return [
                'label' => $date->format('M'),
                'value' => $user->orders()->whereYear('created_at', $date->year)
                                          ->whereMonth('created_at', $date->month)->count(),
            ];
        });

        // Top viewed collections (most ordered products)
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'ordersCount', 'wishlistCount', 'totalSpent',
            'pendingOrders', 'completedOrders', 'activeCartItems',
            'dailyData', 'monthlyData', 'topProducts'
        ));
    }
}
