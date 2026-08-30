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
        return view('cart.index', compact('cart', 'discount', 'coupon'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
            'size'       => 'nullable|string',
            'color'      => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart    = $this->getCart();

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'size'       => $request->size,
                'color'      => $request->color,
                'price'      => $product->effective_price,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['count' => $cart->load('items')->count, 'message' => 'Added to cart']);
        }

        return back()->with('success', 'Item added to cart!');
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
