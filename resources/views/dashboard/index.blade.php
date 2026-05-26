@extends('layouts.dashboard')
@section('title', 'My Account')

@section('dashboard-content')
<h1 class="font-serif text-2xl font-bold mb-8">Welcome back, {{ Auth::user()->name }}</h1>

@php
    $recentOrders = Auth::user()->orders()->with('items')->latest()->take(3)->get();
    $wishlistCount = Auth::user()->wishlist()->count();
    $ordersCount = Auth::user()->orders()->count();
@endphp

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-10">
    <div class="border border-stone-200 p-5">
        <p class="text-2xl font-bold text-stone-900">{{ $ordersCount }}</p>
        <p class="text-xs text-stone-500 mt-1">Total Orders</p>
    </div>
    <div class="border border-stone-200 p-5">
        <p class="text-2xl font-bold text-stone-900">{{ $wishlistCount }}</p>
        <p class="text-xs text-stone-500 mt-1">Wishlist Items</p>
    </div>
    <div class="border border-stone-200 p-5">
        <p class="text-2xl font-bold text-stone-900">${{ number_format(Auth::user()->orders()->where('payment_status','paid')->sum('total'), 0) }}</p>
        <p class="text-xs text-stone-500 mt-1">Total Spent</p>
    </div>
</div>

{{-- Recent orders --}}
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-serif text-xl font-bold">Recent Orders</h2>
        <a href="{{ route('dashboard.orders') }}" class="text-xs text-stone-500 hover:text-stone-900 underline">View all</a>
    </div>
    @if($recentOrders->isEmpty())
    <div class="border border-stone-200 p-8 text-center text-stone-400 text-sm">
        No orders yet. <a href="{{ route('shop.index') }}" class="text-stone-600 underline">Start shopping</a>
    </div>
    @else
    <div class="divide-y divide-stone-100 border border-stone-200">
        @foreach($recentOrders as $order)
        <div class="flex items-center justify-between px-5 py-4 hover:bg-stone-50 transition-colors">
            <div>
                <p class="text-sm font-medium text-stone-900">{{ $order->order_number }}</p>
                <p class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('M d, Y') }} · {{ $order->items->count() }} item(s)</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="badge {{ $order->status_badge }} capitalize text-xs">{{ $order->status }}</span>
                <span class="text-sm font-semibold">${{ number_format($order->total, 0) }}</span>
                <a href="{{ route('dashboard.orders.show', $order) }}" class="text-xs text-stone-500 hover:text-stone-900 underline">Details</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
