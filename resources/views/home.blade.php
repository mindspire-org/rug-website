@extends('layouts.site')
@section('title', 'Where Your Dream Rug Comes to Life')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. HERO — full-bleed, centered text, "Explore the Collection"
══════════════════════════════════════════════════════════════ --}}
<section class="relative h-[85vh] min-h-[560px] flex items-center justify-center overflow-hidden">
    {{-- Background: decorative rug-pattern gradient (no photo needed) --}}
    <div class="absolute inset-0 bg-stone-800">
        <svg class="absolute inset-0 w-full h-full opacity-[0.12]" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice">
            <defs>
                <pattern id="hero-rug" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <rect width="80" height="80" fill="none"/>
                    <rect x="4"  y="4"  width="72" height="72" fill="none" stroke="#d4b060" stroke-width="1"/>
                    <rect x="14" y="14" width="52" height="52" fill="none" stroke="#c9903a" stroke-width="0.7"/>
                    <rect x="24" y="24" width="32" height="32" fill="none" stroke="#d4b060" stroke-width="0.5"/>
                    <circle cx="40" cy="40" r="7"  fill="none" stroke="#c9903a" stroke-width="0.6"/>
                    <line x1="4"  y1="40" x2="14" y2="40" stroke="#d4b060" stroke-width="0.8"/>
                    <line x1="66" y1="40" x2="76" y2="40" stroke="#d4b060" stroke-width="0.8"/>
                    <line x1="40" y1="4"  x2="40" y2="14" stroke="#d4b060" stroke-width="0.8"/>
                    <line x1="40" y1="66" x2="40" y2="76" stroke="#d4b060" stroke-width="0.8"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-rug)"/>
        </svg>
        <div class="absolute inset-0 bg-gradient-to-b from-stone-900/60 via-stone-900/40 to-stone-900/70"></div>
    </div>

    {{-- Centered content --}}
    <div class="relative z-10 text-center px-6 max-w-2xl">
        <h1 class="font-serif text-5xl md:text-6xl font-bold text-white leading-[1.15] mb-8 drop-shadow-lg">
            Where Your Dream Rug<br>Comes to Life
        </h1>
        <a href="{{ route('shop.index') }}"
           class="inline-flex items-center gap-2 border border-white text-white hover:bg-white hover:text-stone-900 text-sm font-medium px-8 py-3 transition-colors duration-200 tracking-wide">
            Explore the Collection
        </a>
    </div>

    {{-- Bottom amber accent bar (matches Figma) --}}
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-400/60"></div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     2. PRODUCT TABS — Signature Designs | Best Sellers | New Arrivals
══════════════════════════════════════════════════════════════ --}}
<section class="py-14 bg-white" x-data="{ tab: 'signature' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Tab header row --}}
        <div class="flex items-center justify-between border-b border-stone-200 pb-3 mb-10 gap-4 overflow-x-auto">
            <div class="flex items-center gap-8 flex-shrink-0">
                <button @click="tab = 'signature'"
                        :class="tab === 'signature' ? 'text-stone-900 border-b-2 border-stone-900 font-semibold pb-3 -mb-3' : 'text-stone-500 hover:text-stone-700 font-medium pb-3 -mb-3 transition-colors'"
                        class="text-base whitespace-nowrap">
                    Signature Designs
                </button>
                <button @click="tab = 'bestsellers'"
                        :class="tab === 'bestsellers' ? 'text-stone-900 border-b-2 border-stone-900 font-semibold pb-3 -mb-3' : 'text-stone-500 hover:text-stone-700 font-medium pb-3 -mb-3 transition-colors'"
                        class="text-base whitespace-nowrap">
                    Best Sellers
                </button>
                <button @click="tab = 'new'"
                        :class="tab === 'new' ? 'text-stone-900 border-b-2 border-stone-900 font-semibold pb-3 -mb-3' : 'text-stone-500 hover:text-stone-700 font-medium pb-3 -mb-3 transition-colors'"
                        class="text-base whitespace-nowrap">
                    New Arrivals
                </button>
            </div>
            <a href="{{ route('shop.index') }}"
               class="flex-shrink-0 inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-stone-900 font-medium text-sm px-5 py-2 transition-colors">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Signature Designs --}}
        <div x-show="tab === 'signature'"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                @forelse($signatureProducts as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <p class="col-span-4 text-center text-stone-400 py-10 text-sm">No featured products yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Best Sellers --}}
        <div x-show="tab === 'bestsellers'" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                @forelse($bestsellers as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <p class="col-span-4 text-center text-stone-400 py-10 text-sm">No bestsellers yet.</p>
                @endforelse
            </div>
        </div>

        {{-- New Arrivals --}}
        <div x-show="tab === 'new'" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                @forelse($newArrivals as $product)
                    @include('partials.product-card', ['product' => $product])
                @empty
                    <p class="col-span-4 text-center text-stone-400 py-10 text-sm">No new arrivals yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     3. WEAVE YOUR DREAM RUG — full-bleed dark photo section
══════════════════════════════════════════════════════════════ --}}
<section class="relative h-[55vh] min-h-[420px] flex items-center justify-center overflow-hidden">
    {{-- Dark gradient background with woven texture --}}
    <div class="absolute inset-0 bg-stone-700">
        <svg class="absolute inset-0 w-full h-full opacity-[0.15]" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid slice">
            <defs>
                <pattern id="weave-bg" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <rect x="2" y="2" width="36" height="36" fill="none" stroke="#d4c090" stroke-width="0.8"/>
                    <circle cx="20" cy="20" r="5" fill="none" stroke="#d4c090" stroke-width="0.6"/>
                    <line x1="2"  y1="20" x2="8"  y2="20" stroke="#d4c090" stroke-width="0.5"/>
                    <line x1="32" y1="20" x2="38" y2="20" stroke="#d4c090" stroke-width="0.5"/>
                    <line x1="20" y1="2"  x2="20" y2="8"  stroke="#d4c090" stroke-width="0.5"/>
                    <line x1="20" y1="32" x2="20" y2="38" stroke="#d4c090" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#weave-bg)"/>
        </svg>
        <div class="absolute inset-0 bg-gradient-to-b from-stone-900/50 to-stone-900/70"></div>
    </div>

    <div class="relative z-10 text-center px-6 max-w-xl">
        <h2 class="font-serif text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
            Weave Your Dream Rug<br>From Scratch
        </h2>
        <p class="text-stone-300 text-sm md:text-base mb-8 max-w-md mx-auto">
            Start with a blank canvas and collaborate with our team to design a rug made entirely for your space.
        </p>
        <a href="{{ route('weave') }}"
           class="inline-flex items-center gap-2 border border-white text-white hover:bg-white hover:text-stone-900 text-sm font-medium px-8 py-3 transition-colors duration-200">
            Design Rug from Scratch
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     4. CRAFTED AROUND YOU — white bg, heading + 3 cards
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Heading --}}
        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl font-bold text-stone-900">Crafted Around You</h2>
            <p class="text-stone-500 text-sm mt-2">Start with our collection. Make it your own.</p>
            <div class="mt-7">
                <a href="{{ route('shop.index') }}"
                   class="inline-flex items-center gap-2 bg-amber-400 hover:bg-amber-500 text-stone-900 font-medium text-sm px-7 py-3 transition-colors">
                    Explore the Collection
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- 3-column image cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Card 1: Truly Yours --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-800 via-stone-700 to-stone-900">
                    <svg class="absolute inset-0 w-full h-full opacity-[0.12]" viewBox="0 0 300 375" preserveAspectRatio="xMidYMid slice">
                        <defs><pattern id="c1" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="15" fill="none" stroke="#d4b060" stroke-width="0.8"/><rect x="5" y="5" width="40" height="40" fill="none" stroke="#d4b060" stroke-width="0.5"/></pattern></defs>
                        <rect width="100%" height="100%" fill="url(#c1)"/>
                    </svg>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="font-serif text-xl font-bold mb-2">Truly Yours</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Begin with a design from our collection or reimagine it for your space. Custom dimensions and nuanced color variations allow each rug to feel entirely personal — executed with precision and delivered with ease.</p>
                </div>
            </div>

            {{-- Card 2: Generational Craftsmanship --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <div class="absolute inset-0 bg-gradient-to-br from-stone-600 via-amber-900 to-stone-800">
                    <svg class="absolute inset-0 w-full h-full opacity-[0.12]" viewBox="0 0 300 375" preserveAspectRatio="xMidYMid slice">
                        <defs><pattern id="c2" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><rect x="5" y="5" width="30" height="30" fill="none" stroke="#d4b060" stroke-width="0.8"/><line x1="5" y1="20" x2="35" y2="20" stroke="#d4b060" stroke-width="0.4"/><line x1="20" y1="5" x2="20" y2="35" stroke="#d4b060" stroke-width="0.4"/></pattern></defs>
                        <rect width="100%" height="100%" fill="url(#c2)"/>
                    </svg>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="font-serif text-xl font-bold mb-2">Generational Craftsmanship</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Hand-knotted, hand-tufted, and machine-loomed by carefully curated weavers, our rugs reflect generations of craftsmanship and trusted artisan relationships built over 140 years.</p>
                </div>
            </div>

            {{-- Card 3: Finishing Care --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <div class="absolute inset-0 bg-gradient-to-br from-stone-500 via-stone-700 to-stone-900">
                    <svg class="absolute inset-0 w-full h-full opacity-[0.12]" viewBox="0 0 300 375" preserveAspectRatio="xMidYMid slice">
                        <defs><pattern id="c3" x="0" y="0" width="60" height="60" patternUnits="userSpaceOnUse"><rect x="8" y="8" width="44" height="44" fill="none" stroke="#d4b060" stroke-width="0.8"/><rect x="18" y="18" width="24" height="24" fill="none" stroke="#c9903a" stroke-width="0.5"/></pattern></defs>
                        <rect width="100%" height="100%" fill="url(#c3)"/>
                    </svg>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="font-serif text-xl font-bold mb-2">Finishing Care</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Handled with exceptional care through white-glove delivery and in-home placement, our experienced teams carefully position each rug to ensure effortless installation and refined presentation.</p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
