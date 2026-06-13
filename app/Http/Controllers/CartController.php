<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product.primaryImage');
        $coupon = session('coupon');
        $discount = 0;
        if ($coupon) {
            $couponModel = Coupon::where('code', $coupon)->first();
            if ($couponModel && $couponModel->isValid()) {
                $discount = $couponModel->calculateDiscount($cart->subtotal);
            }
        }
        $delivery     = session('cart_delivery', 'whiteglove');
        $deliveryCost = Cart::deliveryCost($delivery);
        $addons       = (array) session('cart_addons', []);
        $addonsCost   = Cart::addonsCost($addons);

        return view('cart.index', compact('cart', 'discount', 'coupon', 'delivery', 'deliveryCost', 'addons', 'addonsCost'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'quantity'        => 'nullable|integer|min:1|max:99',
            'size'            => 'nullable|string',
            'color'           => 'nullable|string',
            'is_sample'       => 'nullable|boolean',
            'custom_width'    => 'nullable|numeric|min:0',
            'custom_length'   => 'nullable|numeric|min:0',
            'delivery_method' => 'nullable|string',
            'addons'          => 'nullable|array',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $cart     = $this->getCart();
        $isSample = $request->boolean('is_sample');

        // Carry the product-page delivery / add-on choices to the cart (#22b/#24)
        if ($request->filled('delivery_method') && array_key_exists($request->delivery_method, Cart::DELIVERY_PRICES)) {
            session(['cart_delivery' => $request->delivery_method]);
        }
        if ($request->has('addons')) {
            $valid = array_values(array_intersect((array) $request->input('addons', []), array_keys(Cart::ADDON_PRICES)));
            session(['cart_addons' => $valid]);
        }

        if ($isSample) {
            // Free sample line, deduped per product (#8)
            if (! $cart->items()->where('product_id', $product->id)->where('is_sample', true)->exists()) {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => 1,
                    'size'       => 'Sample',
                    'color'      => $request->color,
                    'price'      => 0,
                    'is_sample'  => true,
                ]);
            }
            $message = 'Sample added to your cart!';
        } else {
            $size      = $request->size;
            $width     = (float) $request->custom_width;
            $length    = (float) $request->custom_length;
            $unitPrice = (float) $product->effective_price;

            if ($size === 'custom' && $width > 0 && $length > 0) {
                // Square-foot pricing relative to a 6x9 (54 sq ft) base (#2)
                $unitPrice = round($product->effective_price * max(1, ($width * $length) / 54), 2);
            } elseif (is_numeric($size) && ($dim = $product->dimensionPrices()->find($size))) {
                $unitPrice = (float) $dim->effective_price;
            }

            $quantity = (int) ($request->quantity ?? 1);

            $item = $cart->items()
                ->where('product_id', $product->id)
                ->where('size', $size)
                ->where('color', $request->color)
                ->where('is_sample', false)
                ->when($size === 'custom', fn ($q) => $q->where('custom_width', $width)->where('custom_length', $length))
                ->first();

            if ($item) {
                $item->increment('quantity', $quantity);
            } else {
                $cart->items()->create([
                    'product_id'    => $product->id,
                    'quantity'      => $quantity,
                    'size'          => $size,
                    'color'         => $request->color,
                    'price'         => $unitPrice,
                    'is_sample'     => false,
                    'custom_width'  => $size === 'custom' ? $width : null,
                    'custom_length' => $size === 'custom' ? $length : null,
                ]);
            }
            $message = 'Item added to cart!';
        }

        if ($request->expectsJson()) {
            return response()->json(['count' => $cart->load('items')->count, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    /** Persist cart-level delivery method + add-on selections (live-priced on the cart page). */
    public function options(Request $request)
    {
        $request->validate([
            'delivery' => 'nullable|string',
            'addons'   => 'nullable|array',
        ]);

        if ($request->filled('delivery') && array_key_exists($request->delivery, Cart::DELIVERY_PRICES)) {
            session(['cart_delivery' => $request->delivery]);
        }
        $valid = array_values(array_intersect((array) $request->input('addons', []), array_keys(Cart::ADDON_PRICES)));
        session(['cart_addons' => $valid]);

        $cart = $this->getCart();
        return response()->json([
            'subtotal'      => (float) $cart->subtotal,
            'delivery_cost' => Cart::deliveryCost(session('cart_delivery', 'whiteglove')),
            'addons_cost'   => Cart::addonsCost(session('cart_addons', [])),
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1|max:99']);
        $cartItem->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon' => 'required|string']);
        $coupon = Coupon::where('code', strtoupper($request->coupon))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        session(['coupon' => $coupon->code]);
        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }

    public function count()
    {
        $cart = $this->getCart();
        return response()->json(['count' => $cart->load('items')->count]);
    }
}
