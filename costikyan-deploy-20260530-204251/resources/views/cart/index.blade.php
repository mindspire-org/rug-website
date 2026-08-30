@extends('layouts.site')
@section('title', 'Your Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="font-serif text-3xl font-bold mb-10">Your Cart</h1>

    @if($cart->items->isEmpty())
    <div class="text-center py-24 border border-stone-200">
        <svg class="w-16 h-16 text-stone-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
        <p class="text-stone-500 mb-6">Your cart is empty.</p>
        <a href="{{ route('shop.index') }}" class="btn-dark">Explore Our Collection</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Items --}}
        <div class="lg:col-span-2 space-y-6">
            @foreach($cart->items as $item)
            <div class="flex gap-5 border-b border-stone-100 pb-6">
                <a href="{{ route('shop.show', $item->product->slug) }}" class="flex-shrink-0 w-24 h-24 bg-stone-100 overflow-hidden">
                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                </a>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <a href="{{ route('shop.show', $item->product->slug) }}" class="text-sm font-medium text-stone-900 hover:text-stone-600 block">{{ $item->product->name }}</a>
                            @if($item->color)<p class="text-xs text-stone-400 mt-0.5">Color: {{ $item->color }}</p>@endif
                            @if($item->size)<p class="text-xs text-stone-400">Size: {{ $item->size }}</p>@endif
                        </div>
                        <p class="text-sm font-semibold text-stone-900 flex-shrink-0">${{ number_format($item->line_total, 0) }}</p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center border border-stone-300">
                            @csrf @method('PATCH')
                            <button type="button" onclick="var i=this.parentNode.querySelector('input');i.value=Math.max(1,parseInt(i.value)-1);this.form.submit()" class="px-3 py-1.5 text-stone-600 hover:bg-stone-50">–</button>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="w-10 text-center text-sm border-0 focus:ring-0 p-0 py-1.5" onchange="this.form.submit()">
                            <button type="button" onclick="var i=this.parentNode.querySelector('input');i.value=Math.min(99,parseInt(i.value)+1);this.form.submit()" class="px-3 py-1.5 text-stone-600 hover:bg-stone-50">+</button>
                        </form>
                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-stone-400 hover:text-red-600 transition-colors">Remove</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Coupon --}}
            <div class="pt-2">
                @if($coupon)
                <div class="flex items-center justify-between bg-green-50 border border-green-200 px-4 py-3 text-sm">
                    <span class="text-green-700">Coupon <strong>{{ $coupon }}</strong> applied! −${{ number_format($discount, 0) }}</span>
                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-green-600 hover:text-red-600 ml-4">✕</button>
                    </form>
                </div>
                @else
                <form action="{{ route('cart.coupon') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="coupon" placeholder="Coupon code" class="form-input flex-1">
                    <button type="submit" class="btn-outline-dark text-sm px-4 py-2.5">Apply</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-1">
            <div class="border border-stone-200 p-6 sticky top-24">
                <h2 class="font-serif text-xl font-bold mb-6">Order Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-600">Subtotal</span>
                        <span>${{ number_format($cart->subtotal, 0) }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="flex justify-between text-green-700">
                        <span>Discount</span>
                        <span>−${{ number_format($discount, 0) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-stone-600">Shipping</span>
                        <span class="text-stone-500">Calculated at checkout</span>
                    </div>
                    <div class="border-t border-stone-200 pt-3 flex justify-between font-semibold text-base">
                        <span>Estimated Total</span>
                        <span>${{ number_format($cart->subtotal - $discount, 0) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn-dark w-full justify-center mt-6 py-4">
                    Proceed to Checkout
                </a>
                <a href="{{ route('shop.index') }}" class="block text-center mt-4 text-xs text-stone-500 hover:text-stone-900 underline">Continue Shopping</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
