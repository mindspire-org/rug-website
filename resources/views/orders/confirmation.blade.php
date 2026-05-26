@extends('layouts.site')
@section('title', 'Order Confirmed — ' . $order->order_number)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h1 class="font-serif text-3xl font-bold mb-3">Thank You for Your Order!</h1>
    <p class="text-stone-500 mb-2">Order <strong>{{ $order->order_number }}</strong></p>
    <p class="text-stone-500 text-sm mb-10">A confirmation email has been sent. Our team will reach out within 1–2 business days.</p>

    <div class="border border-stone-200 text-left mb-10">
        <div class="bg-stone-50 px-6 py-4 border-b border-stone-200">
            <h2 class="font-serif text-lg font-bold">Order Details</h2>
        </div>
        <div class="p-6 space-y-4">
            @foreach($order->items as $item)
            <div class="flex gap-4">
                <div class="w-16 h-16 bg-stone-100 flex-shrink-0 overflow-hidden">
                    @if($item->product)
                    <img src="{{ $item->product->primary_image_url }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-stone-900">{{ $item->product_name }}</p>
                    @if($item->size)<p class="text-xs text-stone-400">Size: {{ $item->size }}</p>@endif
                    @if($item->color)<p class="text-xs text-stone-400">Color: {{ $item->color }}</p>@endif
                    <p class="text-xs text-stone-500">Qty: {{ $item->quantity }}</p>
                </div>
                <p class="text-sm font-semibold">${{ number_format($item->line_total, 0) }}</p>
            </div>
            @endforeach
        </div>
        <div class="border-t border-stone-200 px-6 py-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-stone-600">Subtotal</span><span>${{ number_format($order->subtotal, 0) }}</span></div>
            @if($order->discount > 0)<div class="flex justify-between text-green-700"><span>Discount</span><span>−${{ number_format($order->discount, 0) }}</span></div>@endif
            <div class="flex justify-between"><span class="text-stone-600">Shipping</span><span>{{ $order->shipping > 0 ? '$'.number_format($order->shipping,0) : 'Free' }}</span></div>
            <div class="flex justify-between"><span class="text-stone-600">Tax</span><span>${{ number_format($order->tax, 0) }}</span></div>
            <div class="flex justify-between font-bold text-base border-t border-stone-200 pt-3"><span>Total</span><span>${{ number_format($order->total, 0) }}</span></div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('dashboard.orders') }}" class="btn-dark px-8">View My Orders</a>
        <a href="{{ route('shop.index') }}" class="btn-outline-dark px-8">Continue Shopping</a>
    </div>
</div>
@endsection
