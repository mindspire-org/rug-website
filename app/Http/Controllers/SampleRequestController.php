<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SampleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SampleRequestController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to request a sample.');
        }

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'rug_name' => 'required_without:product_id|string|max:255',
            'color' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $sample = SampleRequest::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'] ?? null,
            'rug_name' => $validated['rug_name'] ?? ($request->product_id ? Product::find($request->product_id)->name : 'Custom Request'),
            'color' => $validated['color'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Sample request submitted successfully. Our team will contact you shortly.');
    }

    public function index()
    {
        $samples = Auth::user()->sampleRequests()->with('product')->latest()->get();
        return view('trade.samples', compact('samples'));
    }

    public function createFromProduct(Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to request a sample.');
        }

        SampleRequest::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rug_name' => $product->name,
            'color' => $product->colors->first()?->color_name,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Sample request for "' . $product->name . '" submitted successfully.');
    }
}
