@extends('layouts.dashboard')
@section('title', 'My Orders')

@section('dashboard-content')
<h1 class="font-serif text-xl lg:text-2xl font-bold mb-6 lg:mb-8">My Orders</h1>

@if($orders->isEmpty())
<div class="border border-stone-200 p-8 lg:p-10 text-center text-stone-400 text-sm rounded-lg">
    No orders yet. <a href="{{ route('shop.index') }}" class="text-stone-600 underline">Start shopping</a>
</div>
@else
<div class="border border-stone-200 divide-y divide-stone-100 rounded-lg overflow-hidden">
    @foreach($orders as $order)
    <div class="px-4 lg:px-5 py-4 lg:py-5 hover:bg-stone-50 transition-colors">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-stone-900">{{ $order->order_number }}</p>
                <p class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('F d, Y') }} · {{ $order->items->count() }} item(s)</p>
            </div>
            <div class="flex items-center gap-3 sm:gap-4">
                <span class="badge {{ $order->status_badge }} capitalize text-xs">{{ $order->status }}</span>
                <span class="text-sm font-bold">${{ number_format($order->total, 0) }}</span>
                <a href="{{ route('dashboard.orders.show', $order) }}" class="text-xs border border-stone-300 px-3 py-1.5 hover:bg-stone-900 hover:text-white hover:border-stone-900 transition-colors rounded">View</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-6 lg:mt-8">{{ $orders->links() }}</div>
@endif
@endsection
