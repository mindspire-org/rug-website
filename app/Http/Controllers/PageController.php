<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'required|email|max:150',
            'phone'      => 'nullable|string|max:40',
            'address'    => 'nullable|string|max:200',
            'city'       => 'nullable|string|max:100',
            'state'      => 'nullable|string|max:100',
            'zip'        => 'nullable|string|max:20',
            'message'    => 'required|string|max:3000',
        ]);

        \App\Models\ContactSubmission::create($data + ['type' => 'contact']);

        return redirect()->route('thank-you');
    }

    public function weave()
    {
        return view('pages.weave');
    }

    public function weaveSubmit(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email',
            'phone'       => 'nullable|string|max:40',
            'style'       => 'nullable|string|max:200',
            'dimensions'  => 'nullable|string|max:200',
            'description' => 'required|string|max:3000',
        ]);

        \App\Models\ContactSubmission::create([
            'type'       => 'weave',
            'first_name' => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['description'],
            'meta'       => ['style' => $data['style'] ?? null, 'dimensions' => $data['dimensions'] ?? null],
        ]);

        return redirect()->route('thank-you');
    }

    public function trade()
    {
        return view('pages.trade');
    }

    public function shippingQuote(Request $request)
    {
        $zip = preg_replace('/[^0-9]/', '', (string) $request->query('zip', ''));

        // A US ZIP is exactly 5 digits (ZIP+4 is trimmed to the leading 5).
        if (strlen($zip) < 5) {
            return response()->json([
                'zip'       => $zip,
                'price'     => null,
                'found'     => false,
                'estimated' => false,
                'reason'    => 'invalid',
            ]);
        }
        $zip = substr($zip, 0, 5);

        $price = \App\Models\ZipPrice::lookup($zip);
        $estimated = false;

        // No exact zone match — fall back to the nearest zone so the shopper always
        // sees a real number (flagged "estimated") rather than a dead-end message.
        if ($price === null) {
            $price = \App\Models\ZipPrice::nearest($zip);
            $estimated = $price !== null;
        }

        // Still nothing (no zones configured at all) — fall back to the standard
        // UPS flat rate so a valid ZIP never dead-ends with "invalid ZIP". The rate
        // is confirmed at checkout.
        if ($price === null) {
            $price = (float) \App\Models\SiteSetting::get('default_ups_rate', 500);
            $estimated = true;
        }

        return response()->json([
            'zip'       => $zip,
            'price'     => $price,
            'found'     => true,
            'estimated' => $estimated,
        ]);
    }

    public function tradeApply(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'company'    => 'nullable|string|max:150',
            'email'      => 'required|email|max:150',
            'phone'      => 'nullable|string|max:40',
            'message'    => 'nullable|string|max:3000',
        ]);

        \App\Models\ContactSubmission::create([
            'type'       => 'trade',
            'first_name' => $data['first_name'] ?? null,
            'last_name'  => $data['last_name'] ?? null,
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'message'    => $data['message'] ?? null,
            'meta'       => ['company' => $data['company'] ?? null],
        ]);

        return redirect()->route('thank-you');
    }

    public function services()
    {
        return view('pages.services');
    }
}
