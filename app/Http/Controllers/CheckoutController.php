<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    private function getCart(): Cart
    {
        return Cart::where('user_id', Auth::id())->with('items.product')->firstOrFail();
    }

    public function index()
    {
        $cart = $this->getCart();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cart->subtotal;
        $coupon   = session('coupon');
        $discount = 0;
        if ($coupon) {
            $couponModel = Coupon::where('code', $coupon)->first();
            if ($couponModel && $couponModel->isValid()) {
                $discount = $couponModel->calculateDiscount($subtotal);
            }
        }
        $deliveryCost = Cart::deliveryCost(session('cart_delivery', 'whiteglove'));
        $addonsCost   = Cart::addonsCost((array) session('cart_addons', []));
        $sampleOnly   = $cart->items->count() > 0 && $cart->items->where("is_sample", false)->count() === 0;
        if ($sampleOnly) { $deliveryCost = 0; $addonsCost = 0; }
        $shipping     = $deliveryCost + $addonsCost;
        $tax          = round(($subtotal - $discount) * 0.08, 2);
        $total        = $subtotal - $discount + $shipping + $tax;

        $addresses = Auth::user()->addresses()->get();

        // Free orders (e.g. sample-only carts) skip Stripe entirely — Stripe rejects
        // any PaymentIntent under $0.50, which was throwing a 500 at checkout.
        $isFree = $total < 0.50;
        $paymentIntent = null;

        if (! $isFree) {
            $stripeKey = config('services.stripe.secret');
            if (! $stripeKey || str_contains($stripeKey, '_KEY') || ! str_starts_with($stripeKey, 'sk_')) {
                return redirect()->route('cart.index')
                    ->with('error', 'Payment processing is temporarily unavailable. Please contact us to complete your order.');
            }

            Stripe::setApiKey($stripeKey);
            $paymentIntent = PaymentIntent::create([
                'amount'   => (int)($total * 100),
                'currency' => 'usd',
                'metadata' => ['user_id' => Auth::id()],
            ]);
        }

        return view('checkout.index', compact(
            'cart', 'subtotal', 'discount', 'shipping', 'tax', 'total',
            'coupon', 'addresses', 'paymentIntent', 'deliveryCost', 'addonsCost', 'isFree'
        ));
    }

    public function store(Request $request)
    {
        $cart    = $this->getCart();
        $cartSubtotalCheck = $cart->subtotal;
        $isFreeOrder = ($cartSubtotalCheck - 0) <= 0
            && $cart->items->where('is_sample', false)->count() === 0;

        $request->validate([
            'full_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'required|string|max:20',
            'line1'            => 'required|string|max:200',
            'city'             => 'required|string|max:100',
            'state'            => 'nullable|string|max:100',
            'zip'              => 'required|string|max:20',
            'country'          => 'required|string|max:100',
            'payment_intent_id'=> $isFreeOrder ? 'nullable|string' : 'required|string',
        ]);

        $subtotal = $cart->subtotal;
        $coupon   = session('coupon');
        $discount = 0;
        if ($coupon) {
            $couponModel = Coupon::where('code', $coupon)->first();
            if ($couponModel && $couponModel->isValid()) {
                $discount = $couponModel->calculateDiscount($subtotal);
                if ($couponModel->uses_left !== null) {
                    $couponModel->decrement('uses_left');
                }
            }
        }
        $deliveryCost = Cart::deliveryCost(session('cart_delivery', 'whiteglove'));
        $addonsCost   = Cart::addonsCost((array) session('cart_addons', []));
        $sampleOnly   = $cart->items->count() > 0 && $cart->items->where("is_sample", false)->count() === 0;
        if ($sampleOnly) { $deliveryCost = 0; $addonsCost = 0; }
        $shipping     = $deliveryCost + $addonsCost;
        $tax          = round(($subtotal - $discount) * 0.08, 2);
        $total        = $subtotal - $discount + $shipping + $tax;

        $shippingAddress = $request->only('full_name', 'line1', 'line2', 'city', 'state', 'zip', 'country', 'phone');

        $order = Order::create([
            'order_number'       => Order::generateOrderNumber(),
            'user_id'            => Auth::id(),
            'status'             => 'pending',
            'subtotal'           => $subtotal,
            'shipping'           => $shipping,
            'tax'                => $tax,
            'discount'           => $discount,
            'total'              => $total,
            'shipping_address'   => $shippingAddress,
            'payment_intent_id'  => $request->payment_intent_id ?: 'free',
            'payment_status'     => 'paid',
            'coupon_code'        => $coupon,
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'size'         => $item->size,
                'color'        => $item->color,
                'quantity'     => $item->quantity,
                'price'        => $item->price,
            ]);
        }

        $cart->items()->delete();
        session()->forget(['coupon', 'cart_delivery', 'cart_addons']);

        return redirect()->route('orders.confirmation', $order->id)->with('success', 'Order placed successfully!');
    }
}
