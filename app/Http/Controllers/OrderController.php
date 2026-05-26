<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->paginate(10);
        return view('dashboard.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items.product.primaryImage');
        return view('dashboard.order-detail', compact('order'));
    }

    public function confirmation(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items.product.primaryImage');
        return view('orders.confirmation', compact('order'));
    }
}
