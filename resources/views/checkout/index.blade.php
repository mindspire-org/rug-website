@extends('layouts.site')
@section('title', 'Checkout')

@push('scripts')
@if($isFree ?? false)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', function () {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Placing order...</span>';
    });
});
</script>
@else
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '15px',
                color: '#1a1a1a',
                fontFamily: "'Inter', sans-serif",
                '::placeholder': { color: '#a8a29e' }
            },
            invalid: { color: '#dc2626' }
        }
    });
    cardElement.mount('#card-element');

    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Processing...</span>';

        const { error, paymentIntent } = await stripe.confirmCardPayment(
            '{{ $paymentIntent->client_secret }}',
            { payment_method: { card: cardElement } }
        );

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            btn.disabled = false;
            btn.innerHTML = originalText;
        } else {
            document.getElementById('payment_intent_id').value = paymentIntent.id;
            form.submit();
        }
    });
});
</script>
@endif
@endpush

@section('content')
<div class="min-h-screen" style="background: #FAFAF8;">

    {{-- HEADER BAR --}}
    <div class="border-b" style="border-color: rgba(18,18,18,0.08); background: #fff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span style="font-family:'Lusitana',serif; font-size:18px; font-weight:700; letter-spacing:0.12em; color:#121212;">COSTI<span style="color:#E8651A;">K</span>YAN</span>
                </a>
                <p class="text-sm" style="color:rgba(18,18,18,0.45);">Secure Checkout</p>
            </div>
        </div>
    </div>

    {{-- STEPPER --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-2">
        <div class="flex items-center justify-center gap-2">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold" style="background: linear-gradient(135deg, #E8651A, #EDB84A);">1</div>
                <span class="text-sm font-medium" style="color:#121212;">Shipping</span>
            </div>
            <div class="w-12 h-px" style="background: rgba(18,18,18,0.12);"></div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold" style="background: rgba(18,18,18,0.06); color: rgba(18,18,18,0.35);">2</div>
                <span class="text-sm font-medium" style="color:rgba(18,18,18,0.35);">Payment</span>
            </div>
            <div class="w-12 h-px" style="background: rgba(18,18,18,0.12);"></div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold" style="background: rgba(18,18,18,0.06); color: rgba(18,18,18,0.35);">3</div>
                <span class="text-sm font-medium" style="color:rgba(18,18,18,0.35);">Review</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-24">
        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <input type="hidden" name="payment_intent_id" id="payment_intent_id">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- LEFT COLUMN -- Forms --}}
                <div class="lg:col-span-7 space-y-8">

                    {{-- SHIPPING ADDRESS --}}
                    <div class="rounded-lg p-6 sm:p-8" style="background:#fff; border:1px solid rgba(18,18,18,0.08); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #E8651A, #EDB84A);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h2 style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212; line-height:1.2;">Shipping Address</h2>
                                <p class="text-xs mt-0.5" style="color:rgba(18,18,18,0.45);">Where should we deliver your rug?</p>
                            </div>
                        </div>

                        @if($addresses->count())
                        <p class="text-xs font-semibold uppercase tracking-wider mb-3" style="color:rgba(18,18,18,0.4);">Saved Addresses</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                            @foreach($addresses as $addr)
                            <label class="relative cursor-pointer rounded-md border p-4 transition-all hover:border-stone-400" style="border-color: rgba(18,18,18,0.12);">
                                <input type="radio" name="saved_address" value="{{ $addr->id }}" class="absolute top-4 right-4 w-4 h-4 accent-orange-600">
                                <p class="text-sm font-semibold pr-6" style="color:#121212;">{{ $addr->full_name }}</p>
                                <p class="text-xs mt-1 leading-relaxed" style="color:rgba(18,18,18,0.55);">{{ $addr->formatted }}</p>
                            </label>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="flex-1 h-px" style="background: rgba(18,18,18,0.08);"></div>
                            <span class="text-xs font-medium" style="color:rgba(18,18,18,0.35);">Or use a new address</span>
                            <div class="flex-1 h-px" style="background: rgba(18,18,18,0.08);"></div>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">Full Name</label>
                                <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name) }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                                @error('full_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">Email</label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">Phone</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">Address</label>
                                <input type="text" name="line1" value="{{ old('line1') }}" required placeholder="Street address"
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div class="sm:col-span-2">
                                <input type="text" name="line2" value="{{ old('line2') }}" placeholder="Apartment, suite, unit, etc. (optional)"
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">State / Province</label>
                                <input type="text" name="state" value="{{ old('state') }}"
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">ZIP / Postal Code</label>
                                <input type="text" name="zip" value="{{ old('zip') }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" style="color:rgba(18,18,18,0.55);">Country</label>
                                <input type="text" name="country" value="{{ old('country', 'United States') }}" required
                                       class="w-full rounded-md border px-4 py-3 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-400"
                                       style="border-color: rgba(18,18,18,0.12); color:#121212; background:#fff;">
                            </div>
                        </div>
                    </div>

                    {{-- PAYMENT METHOD --}}
                    <div class="rounded-lg p-6 sm:p-8" style="background:#fff; border:1px solid rgba(18,18,18,0.08); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #E8651A, #EDB84A);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <h2 style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212; line-height:1.2;">Payment Method</h2>
                                <p class="text-xs mt-0.5" style="color:rgba(18,18,18,0.45);">All transactions are secure and encrypted</p>
                            </div>
                        </div>

                        @if($isFree ?? false)
                        <div class="rounded-md p-5" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 flex-shrink-0" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-semibold" style="color:#14532d;">No payment required</p>
                                    <p class="text-xs mt-0.5" style="color:#166534;">Your order total is $0 — just confirm your shipping details and place your order.</p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="rounded-md p-4 mb-3" style="background:#FAFAF8; border:1px solid rgba(18,18,18,0.08);">
                            <div class="flex items-center gap-3 mb-4">
                                <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span class="text-sm font-medium" style="color:#121212;">Credit / Debit Card</span>
                                <div class="ml-auto flex items-center gap-1.5">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:#1a1f71; color:#fff;">VISA</span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:#eb001b; color:#fff;">MC</span>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:#ff5f00; color:#fff;">Amex</span>
                                </div>
                            </div>
                            <div id="card-element" class="rounded-md px-4 py-4 transition-all" style="background:#fff; border:1px solid rgba(18,18,18,0.12);"></div>
                            <p id="card-errors" class="text-red-600 text-sm mt-2 min-h-[20px]"></p>
                        </div>

                        <div class="flex items-center gap-4 mt-4 pt-4" style="border-top:1px solid rgba(18,18,18,0.06);">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" style="color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span class="text-xs" style="color:rgba(18,18,18,0.45);">SSL Secure</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" style="color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-xs" style="color:rgba(18,18,18,0.45);">PCI Compliant</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT COLUMN -- Order Summary --}}
                <div class="lg:col-span-5">
                    <div class="lg:sticky lg:top-6">
                        <div class="rounded-lg p-6 sm:p-8" style="background:#fff; border:1px solid rgba(18,18,18,0.08); box-shadow: 0 1px 3px rgba(0,0,0,0.04);">

                            <h2 style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212; margin-bottom:24px;">Order Summary</h2>

                            <div class="space-y-5 mb-6" style="border-bottom:1px solid rgba(18,18,18,0.06); padding-bottom:24px;">
                                @foreach($cart->items as $item)
                                <div class="flex gap-4">
                                    <div class="w-[72px] h-[72px] rounded-md flex-shrink-0 overflow-hidden" style="background:#f5f5f5;">
                                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover"
                                             onerror="this.style.display='none'">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold truncate" style="color:#121212; font-family:'Lusitana',serif;">{{ $item->product->name }}</p>
                                        <div class="flex flex-wrap gap-x-3 mt-1">
                                            @if($item->size)<span class="text-xs" style="color:rgba(18,18,18,0.45);">{{ $item->size }}</span>@endif
                                            @if($item->color)<span class="text-xs" style="color:rgba(18,18,18,0.45);">{{ $item->color }}</span>@endif
                                        </div>
                                        <p class="text-xs mt-1" style="color:rgba(18,18,18,0.4);">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-semibold flex-shrink-0" style="color:#121212; font-family:'Lusitana',serif;">${{ number_format($item->line_total, 0) }}</p>
                                </div>
                                @endforeach
                            </div>

                            <div class="space-y-3 text-sm mb-6">
                                <div class="flex justify-between">
                                    <span style="color:rgba(18,18,18,0.55);">Subtotal</span>
                                    <span style="color:#121212; font-weight:500;">${{ number_format($subtotal, 0) }}</span>
                                </div>
                                @if($discount > 0)
                                <div class="flex justify-between" style="color:#059669;">
                                    <span>Discount</span>
                                    <span style="font-weight:500;">&minus;${{ number_format($discount, 0) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span style="color:rgba(18,18,18,0.55);">Shipping</span>
                                    <span style="color:#121212; font-weight:500;">{{ $shipping > 0 ? '$'.number_format($shipping,0) : 'Free' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color:rgba(18,18,18,0.55);">Tax (8%)</span>
                                    <span style="color:#121212; font-weight:500;">${{ number_format($tax, 0) }}</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center py-4 mb-6" style="border-top:2px solid rgba(18,18,18,0.08); border-bottom:2px solid rgba(18,18,18,0.08);">
                                <span style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#121212;">Total</span>
                                <span style="font-family:'Lusitana',serif; font-size:24px; font-weight:700; color:#121212;">${{ number_format($total, 0) }}</span>
                            </div>

                            <button id="submit-btn" type="submit"
                                    class="w-full flex items-center justify-center gap-2 text-white font-semibold text-sm rounded-md py-4 transition-all hover:opacity-90 active:scale-[0.98]"
                                    style="background: linear-gradient(135deg, #E8651A, #EDB84A);">
                                Place Order &mdash; ${{ number_format($total, 0) }}
                            </button>

                            <p class="text-center mt-4 flex items-center justify-center gap-1.5" style="color:rgba(18,18,18,0.35);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-xs">Secured by Stripe. Your card details are encrypted.</span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
