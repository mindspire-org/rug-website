@extends('layouts.site')
@section('title', 'Our Story — Est. 1886')

@section('content')
{{-- Hero --}}
<section class="relative h-[50vh] min-h-[380px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/about-hero.jpg') }}" alt="Costikyan workshop" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
    </div>
    <div class="relative z-10 text-center px-4">
        <p class="text-stone-300 text-xs uppercase tracking-widest mb-3">Est. 1886</p>
        <h1 class="font-serif text-4xl md:text-5xl font-bold text-white">Our Story</h1>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="prose prose-stone max-w-none">
        <p class="text-lg text-stone-700 leading-relaxed mb-6">For over 135 years, Costikyan Custom Carpet has been at the forefront of handcrafted rug-making. Founded in 1886, we have served generations of discerning clients — from private residences to landmark hotels and cultural institutions.</p>
        <p class="text-stone-600 leading-relaxed mb-6">Our legacy is rooted in a belief that every space deserves a rug made precisely for it. We work directly with master weavers across multiple continents, selecting only those whose craft meets our exacting standards. Each rug — whether chosen from our signature collection or created entirely from scratch — is a collaboration between our design team and the client's vision.</p>
        <p class="text-stone-600 leading-relaxed mb-10">From hand-knotted Persian wool to modern machine-loomed synthetics, our range of materials, weave structures, and finishing techniques means no brief is too ambitious.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 border-t border-stone-200 pt-12">
        <div class="text-center">
            <p class="font-serif text-5xl font-bold text-stone-900 mb-2">135+</p>
            <p class="text-sm text-stone-500">Years in business</p>
        </div>
        <div class="text-center">
            <p class="font-serif text-5xl font-bold text-stone-900 mb-2">10K+</p>
            <p class="text-sm text-stone-500">Rugs crafted</p>
        </div>
        <div class="text-center">
            <p class="font-serif text-5xl font-bold text-stone-900 mb-2">40+</p>
            <p class="text-sm text-stone-500">Countries served</p>
        </div>
    </div>
</section>
@endsection
