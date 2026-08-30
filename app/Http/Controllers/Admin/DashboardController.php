<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\SavedEstimate;
use App\Models\RoomVisualization;
use App\Models\SampleRequest;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->date_start
            ? Carbon::parse($request->date_start)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $end = $request->date_end
            ? Carbon::parse($request->date_end)->endOfDay()
            : Carbon::now()->endOfDay();

        $dateRange = [$start, $end];

        $stats = [
            'total_revenue'     => Order::where('payment_status', 'paid')->whereBetween('created_at', $dateRange)->sum('total'),
            'total_orders'      => Order::whereBetween('created_at', $dateRange)->count(),
            'total_products'    => Product::count(),
            'total_customers'   => User::whereIn('role', [User::ROLE_CLIENT, User::ROLE_TRADE])->count(),
            'pending_orders'    => Order::whereIn('status', ['pending', 'processing'])->whereBetween('created_at', $dateRange)->count(),
            'delivered_orders'  => Order::where('status', 'delivered')->whereBetween('created_at', $dateRange)->count(),
            'low_stock'         => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'saved_estimates'   => SavedEstimate::whereBetween('created_at', $dateRange)->count(),
            'visualizations'    => RoomVisualization::whereBetween('created_at', $dateRange)->count(),
            'sample_requests'   => SampleRequest::whereBetween('created_at', $dateRange)->count(),
            'wishlist_items'    => Wishlist::count(),
            'repeat_customers'  => Order::whereBetween('created_at', $dateRange)
                                    ->select('user_id', DB::raw('COUNT(*) as cnt'))
                                    ->groupBy('user_id')
                                    ->having('cnt', '>', 1)
                                    ->count(),
            'avg_order_value'   => Order::where('payment_status', 'paid')->whereBetween('created_at', $dateRange)->avg('total') ?? 0,
        ];

        $recentOrders = Order::with('user')->whereBetween('created_at', $dateRange)->latest()->take(10)->get();
        $topProducts = Product::withCount('wishlists')
            ->orderByDesc('wishlists_count')->take(5)->get();

        $revenueChart = $this->revenueChartData($start, $end);
        $ordersStatusChart = $this->ordersStatusData($start, $end);
        $topSellingChart = $this->topSellingData($start, $end);

        return view('admin.dashboard', compact(
            'stats', 'recentOrders', 'topProducts',
            'revenueChart', 'ordersStatusChart', 'topSellingChart',
            'start', 'end'
        ));
    }

    private function revenueChartData(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end) + 1;
        $format = $days <= 31 ? 'Y-m-d' : 'Y-m';
        $selectRaw = $days <= 31
            ? "DATE(created_at) as period, SUM(total) as revenue"
            : "DATE_FORMAT(created_at, '%Y-%m') as period, SUM(total) as revenue";

        $rows = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw($selectRaw))
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $labels = [];
        $data = [];
        $current = $start->copy();

        if ($days <= 31) {
            while ($current <= $end) {
                $key = $current->format('Y-m-d');
                $labels[] = $current->format('M j');
                $data[] = round($rows[$key]?->revenue ?? 0, 2);
                $current->addDay();
            }
        } else {
            while ($current <= $end) {
                $key = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $data[] = round($rows[$key]?->revenue ?? 0, 2);
                $current->addMonth();
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function ordersStatusData(Carbon $start, Carbon $end): array
    {
        $rows = Order::whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
        $keys = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $data = [];
        foreach ($keys as $k) {
            $data[] = $rows[$k] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function topSellingData(Carbon $start, Carbon $end): array
    {
        $rows = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->select('products.name', DB::raw('SUM(order_items.quantity) as qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        return [
            'labels' => $rows->pluck('name')->toArray(),
            'data' => $rows->pluck('qty')->map(fn($v) => (int)$v)->toArray(),
        ];
    }

    public function exportCsv(Request $request)
    {
        $start = $request->date_start ? Carbon::parse($request->date_start)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $end = $request->date_end ? Carbon::parse($request->date_end)->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::with('user')->whereBetween('created_at', [$start, $end])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dashboard-export-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($orders) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Order #', 'Customer', 'Email', 'Date', 'Status', 'Total', 'Payment']);
            foreach ($orders as $o) {
                fputcsv($fh, [
                    $o->order_number,
                    $o->user?->name ?? 'Guest',
                    $o->user?->email ?? '',
                    $o->created_at->format('Y-m-d H:i'),
                    $o->status,
                    $o->total,
                    $o->payment_status,
                ]);
            }
            fclose($fh);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        return redirect()->back()->with('info', 'Use your browser print dialog (Ctrl+P) and choose "Save as PDF" for best results.');
    }
}
