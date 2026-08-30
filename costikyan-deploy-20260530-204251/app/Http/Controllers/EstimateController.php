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
            'add_ons' => 'nullable|array',
            'delivery_method' => 'nullable|string',
            'email' => 'required|email',
            'notes' => 'nullable|string',
        ]);

        $basePrice = $product->sale_price ?? $product->price;
        $addOnPrice = 0;
        $addOns = $data['add_ons'] ?? [];
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
        ];

        try {
            Mail::send('emails.estimate', $estimateData, function ($message) use ($data) {
                $message->to($data['email'])
                    ->subject('Your Costikyan Custom Carpet Estimate');
            });

            return back()->with('success', 'Estimate emailed to ' . $data['email']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not send email. Please try again later.');
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
