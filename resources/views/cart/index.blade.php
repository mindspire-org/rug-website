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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12"
         x-data="{
            subtotal: {{ (float) $cart->subtotal }},
            discount: {{ (float) $discount }},
            delivery: '{{ $delivery }}',
            sampleOnly: {{ $cart->items->where('is_sample', false)->count() === 0 ? 'true' : 'false' }},
            deliveryPrices: { whiteglove: 250, ups: 500, pickup: 50 },
            addons: {
                protector: {{ in_array('protector', $addons) ? 'true' : 'false' }},
                padding:   {{ in_array('padding', $addons) ? 'true' : 'false' }},
                spot:      {{ in_array('spot', $addons) ? 'true' : 'false' }}
            },
            addonPrices: { protector: 120, padding: 190, spot: 19.99 },
            get deliveryCost() { return this.sampleOnly ? 0 : (this.deliveryPrices[this.delivery] || 0); },
            get addonsCost() { if (this.sampleOnly) return 0; let s = 0; for (const k in this.addons) { if (this.addons[k]) s += this.addonPrices[k]; } return s; },
            get total() { return Math.max(0, this.subtotal - this.discount) + this.deliveryCost + this.addonsCost; },
            money(n) { return '$' + Number(n).toLocaleString('en-US', { maximumFractionDigits: 2 }); },
            persist() {
                fetch('{{ route('cart.options') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ delivery: this.delivery, addons: Object.keys(this.addons).filter(k => this.addons[k]) })
                });
            }
         }">
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
                            <a href="{{ route('shop.show', $item->product->slug) }}" class="text-sm font-medium text-stone-900 hover:text-stone-600 block">
                                {{ $item->product->name }}
                                @if($item->is_sample)
                                <span class="ml-1 inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold align-middle" style="background:#fff7ed; color:#E8651A;">SAMPLE</span>
                                @endif
                            </a>
                            @if($item->color)<p class="text-xs text-stone-400 mt-0.5">Color: {{ $item->color }}</p>@endif
                            @if($item->is_sample)
                                <p class="text-xs text-stone-400">Swatch sample</p>
                            @elseif($item->size === 'custom' && $item->custom_width)
                                <p class="text-xs text-stone-400">Custom size: {{ rtrim(rtrim(number_format($item->custom_width,2),'0'),'.') }}ft × {{ rtrim(rtrim(number_format($item->custom_length,2),'0'),'.') }}ft</p>
                            @elseif($item->size)
                                <p class="text-xs text-stone-400">Size: {{ $item->size }}</p>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-stone-900 flex-shrink-0">
                            @if($item->is_sample) Free @else ${{ number_format($item->line_total, 0) }} @endif
                        </p>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        @if($item->is_sample)
                        <span class="text-xs text-stone-400">Qty: 1</span>
                        @else
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center border border-stone-300">
                            @csrf @method('PATCH')
                            <button type="button" onclick="var i=this.parentNode.querySelector('input');i.value=Math.max(1,parseInt(i.value)-1);this.form.submit()" class="px-3 py-1.5 text-stone-600 hover:bg-stone-50">–</button>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="w-10 text-center text-sm border-0 focus:ring-0 p-0 py-1.5" onchange="this.form.submit()">
                            <button type="button" onclick="var i=this.parentNode.querySelector('input');i.value=Math.min(99,parseInt(i.value)+1);this.form.submit()" class="px-3 py-1.5 text-stone-600 hover:bg-stone-50">+</button>
                        </form>
                        @endif
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
                <h2 class="font-serif text-xl font-bold mb-5">Order Summary</h2>

                {{-- Delivery method (one required) --}}
                <p class="text-xs font-semibold text-stone-700 uppercase tracking-wide mb-2">Delivery method</p>
                <div class="space-y-2 mb-5">
                    @foreach(['whiteglove' => ['White-Glove Delivery', 250], 'ups' => ['Standard UPS', 500], 'pickup' => ['Warehouse Pick-up', 50]] as $key => $d)
                    <label class="flex items-center justify-between gap-2 text-sm cursor-pointer">
                        <span class="flex items-center gap-2">
                            <input type="radio" name="cart_delivery" value="{{ $key }}" x-model="delivery" @change="persist()" style="accent-color:#121212;">
                            <span class="text-stone-700">{{ $d[0] }}</span>
                        </span>
                        <span class="text-stone-500">+${{ $d[1] }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Add-on services --}}
                <p class="text-xs font-semibold text-stone-700 uppercase tracking-wide mb-2">Add-on services</p>
                <div class="space-y-2 mb-5">
                    @foreach(['protector' => ['Rug Protector', 120], 'padding' => ['Premium Padding', 190], 'spot' => ['Spot Kit Cleaner', 19.99]] as $key => $a)
                    <label class="flex items-center justify-between gap-2 text-sm cursor-pointer">
                        <span class="flex items-center gap-2">
                            <input type="checkbox" x-model="addons.{{ $key }}" @change="persist()" style="accent-color:#121212;">
                            <span class="text-stone-700">{{ $a[0] }}</span>
                        </span>
                        <span class="text-stone-500">+${{ number_format($a[1], 2) }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="space-y-3 text-sm border-t border-stone-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-stone-600">Subtotal</span>
                        <span x-text="money(subtotal)"></span>
                    </div>
                    @if($discount > 0)
                    <div class="flex justify-between text-green-700">
                        <span>Discount</span>
                        <span x-text="'−' + money(discount)"></span>
                    </div>
                    @endif
                    <div class="flex justify-between" x-show="addonsCost > 0" x-cloak>
                        <span class="text-stone-600">Add-ons</span>
                        <span x-text="money(addonsCost)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-600">Delivery</span>
                        <span x-text="money(deliveryCost)"></span>
                    </div>
                    <div class="border-t border-stone-200 pt-3 flex justify-between font-semibold text-base">
                        <span>Estimated Total</span>
                        <span x-text="money(total)"></span>
                    </div>
                    <p class="text-[11px] text-stone-400">Taxes calculated at checkout.</p>
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
