@extends('layouts.dashboard')
@section('title', 'Order ' . $order->order_number)

@section('dashboard-content')
<div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-6 lg:mb-8">
    <div class="flex items-center gap-3 sm:gap-4">
        <a href="{{ route('dashboard.orders') }}" class="text-stone-400 hover:text-stone-900 transition-colors flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="font-serif text-xl lg:text-2xl font-bold">Order {{ $order->order_number }}</h1>
    </div>
    <span class="badge {{ $order->status_badge }} capitalize self-start sm:ml-auto">{{ $order->status }}</span>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
    {{-- Items --}}
    <div class="border border-stone-200">
        <div class="px-5 py-3 bg-stone-50 border-b border-stone-200 text-xs font-semibold uppercase tracking-wide text-stone-500">Items</div>
        <div class="divide-y divide-stone-100">
            @foreach($order->items as $item)
            <div class="flex gap-4 px-5 py-4">
                <div class="w-14 h-14 bg-stone-100 flex-shrink-0 overflow-hidden">
                    @if($item->product)
                    <img src="{{ $item->product->primary_image_url }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-stone-900">{{ $item->product_name }}</p>
                    @if($item->size)<p class="text-xs text-stone-400">Size: {{ $item->size }}</p>@endif
                    @if($item->color)<p class="text-xs text-stone-400">Color: {{ $item->color }}</p>@endif
                    <p class="text-xs text-stone-500">Qty: {{ $item->quantity }} × ${{ number_format($item->price, 0) }}</p>
                </div>
                <p class="text-sm font-semibold flex-shrink-0">${{ number_format($item->line_total, 0) }}</p>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-4 border-t border-stone-200 space-y-1.5 text-sm">
            <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>${{ number_format($order->subtotal,0) }}</span></div>
            @if($order->discount > 0)<div class="flex justify-between text-green-700"><span>Discount</span><span>−${{ number_format($order->discount,0) }}</span></div>@endif
            <div class="flex justify-between text-stone-600"><span>Shipping</span><span>{{ $order->shipping > 0 ? '$'.number_format($order->shipping,0) : 'Free' }}</span></div>
            <div class="flex justify-between text-stone-600"><span>Tax</span><span>${{ number_format($order->tax,0) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t border-stone-200 pt-2 mt-2"><span>Total</span><span>${{ number_format($order->total,0) }}</span></div>
        </div>
    </div>

    {{-- Shipping address --}}
    <div class="border border-stone-200">
        <div class="px-5 py-3 bg-stone-50 border-b border-stone-200 text-xs font-semibold uppercase tracking-wide text-stone-500">Shipping Address</div>
        <div class="px-5 py-4 text-sm text-stone-700 space-y-1">
            @php $addr = $order->shipping_address; @endphp
            <p class="font-medium text-stone-900">{{ $addr['full_name'] ?? '' }}</p>
            <p>{{ $addr['line1'] ?? '' }}</p>
            @if(!empty($addr['line2']))<p>{{ $addr['line2'] }}</p>@endif
            <p>{{ $addr['city'] ?? '' }}@if(!empty($addr['state'])), {{ $addr['state'] }}@endif {{ $addr['zip'] ?? '' }}</p>
            <p>{{ $addr['country'] ?? '' }}</p>
        </div>
        <div class="px-5 py-3 border-t border-stone-200">
            <p class="text-xs text-stone-500">Placed on {{ $order->created_at->format('F d, Y \a\t g:i a') }}</p>
        </div>
    </div>
</div>
@endsection
