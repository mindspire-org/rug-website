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

    <div class="bg-stone-950 text-white p-10 text-center">
        <h2 class="font-serif text-2xl font-bold mb-3">Apply for Trade Access</h2>
        <p class="text-stone-400 text-sm mb-6">Contact our trade team to get started with your application.</p>
        <a href="{{ route('contact') }}" class="btn-gold text-sm">Contact Trade Team</a>
    </div>
</div>
@endsection
