@extends('layouts.site')
@section('title', 'Checkout')

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: { base: { fontSize: '15px', color: '#1a1a1a', fontFamily: 'Inter, sans-serif' } }
    });
    cardElement.mount('#card-element');

    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-btn').textContent = 'Processing…';

        const { error, paymentIntent } = await stripe.confirmCardPayment(
            '{{ $paymentIntent->client_secret }}',
            { payment_method: { card: cardElement } }
        );

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            document.getElementById('submit-btn').disabled = false;
            document.getElementById('submit-btn').textContent = 'Place Order';
        } else {
            document.getElementById('payment_intent_id').value = paymentIntent.id;
            form.submit();
        }
    });
});
</script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="font-serif text-3xl font-bold mb-10">Checkout</h1>

    <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <input type="hidden" name="payment_intent_id" id="payment_intent_id">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Left: Address + Payment --}}
            <div class="lg:col-span-2 space-y-10">

                {{-- Shipping address --}}
                <div>
                    <h2 class="font-serif text-xl font-semibold mb-6">Shipping Address</h2>

                    @if($addresses->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        @foreach($addresses as $addr)
                        <label class="border p-4 cursor-pointer hover:border-stone-900 transition-colors has-[:checked]:border-stone-900 has-[:checked]:bg-stone-50">
                            <input type="radio" name="saved_address" value="{{ $addr->id }}" class="sr-only">
                            <p class="text-sm font-medium text-stone-900">{{ $addr->full_name }}</p>
                            <p class="text-xs text-stone-500 mt-1">{{ $addr->formatted }}</p>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-stone-500 mb-4">Or enter a new address:</p>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name) }}" class="form-input" required>
                            @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="form-input" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" name="line1" value="{{ old('line1') }}" class="form-input" required>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" name="line2" value="{{ old('line2') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">State / Province</label>
                            <input type="text" name="state" value="{{ old('state') }}" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">ZIP / Postal Code</label>
                            <input type="text" name="zip" value="{{ old('zip') }}" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Country</label>
                            <input type="text" name="country" value="{{ old('country', 'United States') }}" class="form-input" required>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div>
                    <h2 class="font-serif text-xl font-semibold mb-6">Payment</h2>
                    <div id="card-element" class="border border-stone-300 px-4 py-4 focus-within:border-stone-900 transition-colors"></div>
                    <p id="card-errors" class="text-red-600 text-sm mt-2"></p>
                </div>
            </div>

            {{-- Order summary --}}
            <div>
                <div class="border border-stone-200 p-6 sticky top-24">
                    <h2 class="font-serif text-xl font-bold mb-6">Order Summary</h2>
                    <div class="space-y-4 mb-6">
                        @foreach($cart->items as $item)
                        <div class="flex gap-3">
                            <div class="w-14 h-14 bg-stone-100 flex-shrink-0 overflow-hidden">
                                <img src="{{ $item->product->primary_image_url }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-stone-900 truncate">{{ $item->product->name }}</p>
                                @if($item->size)<p class="text-xs text-stone-400">{{ $item->size }}</p>@endif
                                <p class="text-xs text-stone-500">Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="text-xs font-semibold flex-shrink-0">${{ number_format($item->line_total, 0) }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t border-stone-200 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-stone-600">Subtotal</span><span>${{ number_format($subtotal, 0) }}</span></div>
                        @if($discount > 0)
                        <div class="flex justify-between text-green-700"><span>Discount</span><span>−${{ number_format($discount, 0) }}</span></div>
                        @endif
                        <div class="flex justify-between"><span class="text-stone-600">Shipping</span><span>{{ $shipping > 0 ? '$'.number_format($shipping,0) : 'Free' }}</span></div>
                        <div class="flex justify-between"><span class="text-stone-600">Tax (8%)</span><span>${{ number_format($tax, 0) }}</span></div>
                        <div class="border-t border-stone-200 pt-3 flex justify-between font-bold text-base">
                            <span>Total</span><span>${{ number_format($total, 0) }}</span>
                        </div>
                    </div>
                    <button id="submit-btn" type="submit" class="btn-dark w-full justify-center mt-6 py-4">
                        Place Order — ${{ number_format($total, 0) }}
                    </button>
                    <p class="text-center text-xs text-stone-400 mt-3">Secured by Stripe</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
