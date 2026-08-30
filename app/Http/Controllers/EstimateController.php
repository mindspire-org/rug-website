<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SavedEstimate;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EstimateController extends Controller
{
    public function email(Request $request, Product $product)
    {
        $data = $request->validate([
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'finish' => 'nullable|string',
            'add_ons' => 'nullable',
            'delivery_method' => 'nullable|string',
            'email' => 'required|email',
            'notes' => 'nullable|string',
        ]);

        // Decode JSON add_ons if string
        $addOns = $data['add_ons'] ?? [];
        if (is_string($addOns)) {
            $addOns = json_decode($addOns, true) ?? [];
        }
        $data['add_ons'] = $addOns;

        $basePrice = $product->sale_price ?? $product->price;
        $addOnPrice = 0;
        if (!empty($addOns['protector'])) $addOnPrice += 120;
        if (!empty($addOns['padding'])) $addOnPrice += 190;
        if (!empty($addOns['spot'])) $addOnPrice += 19.99;

        $deliveryPrice = 0;
        if (($data['delivery_method'] ?? '') === 'whiteglove') $deliveryPrice = 250;
        elseif (($data['delivery_method'] ?? '') === 'ups') $deliveryPrice = 500;
        elseif (($data['delivery_method'] ?? '') === 'pickup') $deliveryPrice = 50;

        $total = $basePrice + $addOnPrice + $deliveryPrice;

        $estimateData = [
            'product' => $product,
            'size' => $data['size'] ?? 'Standard',
            'color' => $data['color'] ?? 'Default',
            'finish' => $data['finish'] ?? 'N/A',
            'add_ons' => $addOns,
            'delivery_method' => $data['delivery_method'] ?? 'whiteglove',
            'base_price' => $basePrice,
            'add_on_price' => $addOnPrice,
            'delivery_price' => $deliveryPrice,
            'total' => $total,
            'notes' => $data['notes'] ?? '',
            'customer_email' => $data['email'],
            'phone' => SiteSetting::get('phone', '800-247-7847'),
            'address' => SiteSetting::get('address', '37-11 48th Avenue, Long Island City, NY 11101'),
        ];

        // Business inbox that should receive every estimate request as a lead
        $businessEmail = SiteSetting::get('business_email') ?: config('mail.from.address');
        $fromName = SiteSetting::get('site_name', config('app.name', 'Costikyan Custom Carpet'));

        try {
            Mail::send(['html' => 'emails.estimate', 'text' => 'emails.estimate_text'], $estimateData, function ($message) use ($data, $businessEmail, $fromName) {
                $message->to($data['email'])
                    ->from(config('mail.from.address'), $fromName)
                    ->subject('Your Costikyan Custom Carpet Estimate — ' . $data['size'] ?? 'Standard')
                    ->replyTo($businessEmail, $fromName);

                // Proper email headers for deliverability
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Mailer', 'Costikyan Custom Carpet');
                $headers->addTextHeader('X-Priority', '3');
                $headers->addTextHeader('List-Unsubscribe', '<mailto:' . config('mail.from.address') . '?subject=Unsubscribe>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, OoO');

                if ($businessEmail && $businessEmail !== config('mail.from.address')) {
                    $message->bcc($businessEmail);
                }
            });

            return back()->with('success', 'Your estimate has been emailed to ' . $data['email'] . '. Please check your inbox.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Estimate email failed: ' . $e->getMessage());
            return back()->with('error', 'Could not send email right now. Please try again or contact us directly.');
        }
    }

    public function save(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to save your estimate.');
        }

        $data = $request->validate([
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'finish' => 'nullable|string',
            'add_ons' => 'nullable',
            'delivery_method' => 'nullable|string',
            'estimated_price' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $addOns = $data['add_ons'] ?? [];
        if (is_string($addOns)) {
            $addOns = json_decode($addOns, true) ?? [];
        }

        SavedEstimate::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'size' => $data['size'] ?? null,
            'color' => $data['color'] ?? null,
            'finish' => $data['finish'] ?? null,
            'add_ons' => $addOns,
            'delivery_method' => $data['delivery_method'] ?? null,
            'estimated_price' => $data['estimated_price'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Estimate saved to your account.');
    }
}
