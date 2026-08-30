@extends('layouts.site')
@section('title', 'Trade & Design Program')

@section('content')
<section class="relative h-[45vh] min-h-[320px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/trade-hero.jpg') }}" alt="Trade and design" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>
    <div class="relative z-10 text-center px-4">
        <h1 class="font-serif text-4xl md:text-5xl font-bold text-white mb-3">Trade & Design</h1>
        <p class="text-stone-200 text-sm md:text-base">Exclusive benefits for interior designers and architects.</p>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-14">
        <h2 class="section-title mb-4">The Costikyan Trade Program</h2>
        <p class="text-stone-600 leading-relaxed max-w-2xl mx-auto">We partner with interior designers, architects, and design professionals to create extraordinary spaces. Our trade program offers exclusive pricing, priority service, and dedicated support.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-16">
        @foreach([['Trade Pricing', 'Exclusive net pricing on our full collection and custom rug programs.'], ['Dedicated Rep', 'A personal account manager to guide you through every project.'], ['Priority Lead Times', 'Expedited production for time-sensitive installations.']] as [$title, $desc])
        <div class="border border-stone-200 p-6 text-center">
            <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <div class="w-4 h-4 bg-amber-400 rounded-full"></div>
            </div>
            <h3 class="font-serif text-lg font-bold mb-2">{{ $title }}</h3>
            <p class="text-sm text-stone-600">{{ $desc }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-stone-950 text-white p-8 md:p-10">
        <div class="max-w-xl mx-auto">
            <h2 class="font-serif text-2xl font-bold mb-2 text-center">Apply for Trade Access</h2>
            <p class="text-stone-400 text-sm mb-6 text-center">Submit your details and our trade team will review your application and set up your account.</p>
            @if(session('success'))
            <div class="bg-green-500/15 border border-green-500/40 text-green-300 text-sm rounded p-3 mb-5 text-center">{{ session('success') }}</div>
            @endif
            <form action="{{ route('trade.apply') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="first_name" placeholder="First name" value="{{ old('first_name') }}" class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">
                    <input type="text" name="last_name" placeholder="Last name" value="{{ old('last_name') }}" class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">
                </div>
                <input type="text" name="company" placeholder="Company / Studio" value="{{ old('company') }}" class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="email" name="email" placeholder="Business email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">
                    <input type="tel" name="phone" placeholder="Phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">
                </div>
                <textarea name="message" rows="3" placeholder="Tell us about your business (optional)" class="w-full px-4 py-2.5 text-sm rounded bg-stone-900 border border-stone-700 text-white placeholder-stone-500 focus:outline-none focus:border-amber-400">{{ old('message') }}</textarea>
                @error('email')<p class="text-red-400 text-xs">{{ $message }}</p>@enderror
                <button type="submit" class="w-full bg-amber-400 hover:bg-amber-500 text-stone-900 font-medium text-sm py-3 rounded transition-colors">Submit Trade Application</button>
            </form>
        </div>
    </div>
</div>
@endsection
