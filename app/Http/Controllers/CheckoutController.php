<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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
        $isFree  = $subtotal <= 0;
        $shipping = $isFree || $subtotal > 5000 ? 0 : 150;
        $tax      = round(($subtotal - $discount) * 0.08, 2);
        $total    = $subtotal - $discount + $shipping + $tax;

        $addresses = Auth::user()->addresses()->get();

        // Skip Stripe PaymentIntent for free orders ($0 total) — Stripe
        // rejects amounts below its minimum charge (typically $0.50).
        $paymentIntent = null;
        if (!$isFree) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::create([
                'amount'   => (int)($total * 100),
                'currency' => 'usd',
                'metadata' => ['user_id' => Auth::id()],
            ]);
        }

        return view('checkout.index', compact(
            'cart', 'subtotal', 'discount', 'shipping', 'tax', 'total',
            'coupon', 'addresses', 'paymentIntent', 'isFree'
        ));
    }

    public function store(Request $request)
    {
        $cart    = $this->getCart();
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
        $isFree  = $subtotal <= 0;
        $shipping = $isFree || $subtotal > 5000 ? 0 : 150;
        $tax      = round(($subtotal - $discount) * 0.08, 2);
        $total    = $subtotal - $discount + $shipping + $tax;

        // For free orders, payment_intent_id is not required (no Stripe charge).
        $rules = [
            'full_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'required|string|max:20',
            'line1'            => 'required|string|max:200',
            'city'             => 'required|string|max:100',
            'state'            => 'nullable|string|max:100',
            'zip'              => 'required|string|max:20',
            'country'          => 'required|string|max:100',
        ];
        if (!$isFree) {
            $rules['payment_intent_id'] = 'required|string';
        }
        $request->validate($rules);

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
            'payment_intent_id'  => $isFree ? 'free_order' : $request->payment_intent_id,
            'payment_status'     => $isFree ? 'free' : 'paid',
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
        session()->forget('coupon');

        // ── Send order confirmation email ──
        $order->load('items');
        $emailData = [
            'order'           => $order,
            'shippingAddress' => $shippingAddress,
            'siteUrl'         => config('app.url'),
            'address'         => SiteSetting::get('address', '37-11 48th Avenue, Long Island City, NY 11101'),
            'phone'           => SiteSetting::get('phone', '800-247-7847'),
        ];

        $businessEmail = SiteSetting::get('business_email') ?: config('mail.from.address');
        $fromName      = SiteSetting::get('site_name', config('app.name', 'Costikyan Custom Carpet'));
        $customerEmail = $request->email;

        try {
            Mail::send(['html' => 'emails.order_confirmation', 'text' => 'emails.order_confirmation_text'],
                $emailData,
                function ($message) use ($customerEmail, $businessEmail, $fromName, $order) {
                    $message->to($customerEmail)
                        ->from(config('mail.from.address'), $fromName)
                        ->subject('Order Confirmation — ' . $order->order_number)
                        ->replyTo($businessEmail, $fromName);

                    $headers = $message->getHeaders();
                    $headers->addTextHeader('X-Mailer', 'Costikyan Custom Carpet');
                    $headers->addTextHeader('X-Priority', '3');
                    $headers->addTextHeader('List-Unsubscribe', '<mailto:' . config('mail.from.address') . '?subject=Unsubscribe>');
                    $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                    $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, OoO');

                    if ($businessEmail && $businessEmail !== config('mail.from.address')) {
                        $message->bcc($businessEmail);
                    }
                }
            );
        } catch (\Throwable $e) {
            Log::error('Order confirmation email failed for order ' . $order->order_number . ': ' . $e->getMessage());
        }

        return redirect()->route('orders.confirmation', $order->id)->with('success', 'Order placed successfully!');
    }
}
