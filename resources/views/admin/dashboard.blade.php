@extends('layouts.admin')
@section('title', 'Dashboard')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Dashboard</h1>
    <p class="text-xs text-stone-400">{{ now()->format('l, F j, Y') }}</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
    @foreach([
        ['Revenue', '$'.number_format($stats['total_revenue'],0), 'text-green-600'],
        ['Orders', $stats['total_orders'], 'text-blue-600'],
        ['Products', $stats['total_products'], 'text-purple-600'],
        ['Customers', $stats['total_customers'], 'text-orange-600'],
    ] as [$label, $value, $color])
    <div class="bg-white border border-stone-200 p-5 rounded">
        <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">{{ $label }}</p>
        <p class="text-3xl font-bold {{ $color }}">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent orders --}}
    <div class="bg-white border border-stone-200 rounded">
        <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 class="font-semibold text-stone-900">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-xs text-stone-400 hover:text-stone-900">View all →</a>
        </div>
        <div class="divide-y divide-stone-50">
            @forelse($recentOrders as $order)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-stone-900">{{ $order->order_number }}</p>
                    <p class="text-xs text-stone-400">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="badge {{ $order->status_badge }} capitalize text-xs">{{ $order->status }}</span>
                    <span class="text-sm font-semibold">${{ number_format($order->total,0) }}</span>
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-stone-400 hover:text-stone-900">→</a>
                </div>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-stone-400 text-center">No orders yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Top wishlisted products --}}
    <div class="bg-white border border-stone-200 rounded">
        <div class="px-5 py-4 border-b border-stone-100">
            <h2 class="font-semibold text-stone-900">Top Wishlisted Products</h2>
        </div>
        <div class="divide-y divide-stone-50">
            @forelse($topProducts as $product)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <p class="text-sm text-stone-900 truncate">{{ $product->name }}</p>
                <span class="text-xs text-stone-400 flex-shrink-0">{{ $product->wishlists_count }} saves</span>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-stone-400 text-center">No data yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
