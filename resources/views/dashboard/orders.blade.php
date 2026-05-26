@extends('layouts.dashboard')
@section('title', 'My Orders')

@section('dashboard-content')
<h1 class="font-serif text-2xl font-bold mb-8">My Orders</h1>

@if($orders->isEmpty())
<div class="border border-stone-200 p-10 text-center text-stone-400 text-sm">
    No orders yet. <a href="{{ route('shop.index') }}" class="text-stone-600 underline">Start shopping</a>
</div>
@else
<div class="border border-stone-200 divide-y divide-stone-100">
    @foreach($orders as $order)
    <div class="px-5 py-5 hover:bg-stone-50 transition-colors">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-sm font-semibold text-stone-900">{{ $order->order_number }}</p>
                <p class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('F d, Y') }} · {{ $order->items->count() }} item(s)</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="badge {{ $order->status_badge }} capitalize">{{ $order->status }}</span>
                <span class="text-sm font-bold">${{ number_format($order->total, 0) }}</span>
                <a href="{{ route('dashboard.orders.show', $order) }}" class="text-xs border border-stone-300 px-3 py-1.5 hover:bg-stone-900 hover:text-white hover:border-stone-900 transition-colors">View</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-8">{{ $orders->links() }}</div>
@endif
@endsection
