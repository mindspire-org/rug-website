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
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Thank you! Your message has been received. We\'ll be in touch shortly.');
    }

    public function weave()
    {
        return view('pages.weave');
    }

    public function weaveSubmit(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email',
            'phone'       => 'nullable|string|max:20',
            'style'       => 'nullable|string|max:200',
            'dimensions'  => 'nullable|string|max:200',
            'description' => 'required|string|max:3000',
        ]);

        return back()->with('success', 'Your custom rug request has been submitted! Our team will contact you within 2 business days.');
    }

    public function trade()
    {
        return view('pages.trade');
    }

    public function services()
    {
        return view('pages.services');
    }
}
