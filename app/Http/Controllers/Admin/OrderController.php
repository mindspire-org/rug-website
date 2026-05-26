<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('export')) {
            $allOrders = (clone $query)->get();
            return new StreamedResponse(function () use ($allOrders) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Order Number', 'Customer', 'Email', 'Date', 'Status', 'Total']);
                foreach ($allOrders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->user?->name ?? 'Guest',
                        $order->user?->email ?? '',
                        $order->created_at->format('Y-m-d'),
                        $order->status,
                        number_format($order->total, 2),
                    ]);
                }
                fclose($handle);
            }, 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders-'.now()->format('Y-m-d').'.csv"',
            ]);
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product.primaryImage', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated.');
    }
}
