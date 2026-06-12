@extends('layouts.site')
@section('title', 'Where Your Dream Rug Comes to Life')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. HERO — full-bleed, centered text, "Explore the Collection"
══════════════════════════════════════════════════════════════ --}}
<section class="relative h-[85vh] min-h-[560px] flex items-center justify-center overflow-hidden">
    {{-- Background: COVER photo --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" alt="Costikyan Custom Carpet"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-b from-stone-900/50 via-stone-900/30 to-stone-900/65"></div>
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
     3. CRAFTED AROUND YOU — white bg, heading + 3 cards (swapped up)
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

            {{-- Card 1: Truly Yours — CARD 11 --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <img src="{{ asset('images/card 11.png') }}" alt="Truly Yours"
                     class="absolute inset-0 w-full h-full object-cover object-center transition-all duration-700 ease-out group-hover:scale-110 group-hover:opacity-0">
                <img src="{{ asset('images/Frame 13.png') }}" alt="Truly Yours"
                     class="absolute inset-0 w-full h-full object-cover object-center opacity-0 scale-110 transition-all duration-700 ease-out group-hover:opacity-100 group-hover:scale-100">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent transition-all duration-500 group-hover:from-black/85"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white transition-transform duration-500 group-hover:-translate-y-1">
                    <h3 class="font-serif text-xl font-bold mb-2">Truly Yours</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Begin with a design from our collection or reimagine it for your space. Custom dimensions and nuanced color variations allow each rug to feel entirely personal — executed with precision and delivered with ease.</p>
                </div>
            </div>

            {{-- Card 2: Generational Craftsmanship — CARD 10 --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <img src="{{ asset('images/card 10.png') }}" alt="Generational Craftsmanship"
                     class="absolute inset-0 w-full h-full object-cover object-center transition-all duration-700 ease-out group-hover:scale-110 group-hover:opacity-0">
                <img src="{{ asset('images/Frame 14.png') }}" alt="Generational Craftsmanship"
                     class="absolute inset-0 w-full h-full object-cover object-center opacity-0 scale-110 transition-all duration-700 ease-out group-hover:opacity-100 group-hover:scale-100">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent transition-all duration-500 group-hover:from-black/85"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white transition-transform duration-500 group-hover:-translate-y-1">
                    <h3 class="font-serif text-xl font-bold mb-2">Generational Craftsmanship</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Hand-knotted, hand-tufted, and machine-loomed by carefully curated weavers, our rugs reflect generations of craftsmanship and trusted artisan relationships built over 140 years.</p>
                </div>
            </div>

            {{-- Card 3: Finishing Care — CARD 9 --}}
            <div class="group relative overflow-hidden aspect-[4/5]">
                <img src="{{ asset('images/card 9.png') }}" alt="Finishing Care"
                     class="absolute inset-0 w-full h-full object-cover object-center transition-all duration-700 ease-out group-hover:scale-110 group-hover:opacity-0">
                <img src="{{ asset('images/Frame 15.png') }}" alt="Finishing Care"
                     class="absolute inset-0 w-full h-full object-cover object-center opacity-0 scale-110 transition-all duration-700 ease-out group-hover:opacity-100 group-hover:scale-100">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent transition-all duration-500 group-hover:from-black/85"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white transition-transform duration-500 group-hover:-translate-y-1">
                    <h3 class="font-serif text-xl font-bold mb-2">Finishing Care</h3>
                    <p class="text-sm text-stone-300 leading-relaxed">Handled with exceptional care through white-glove delivery and in-home placement, our experienced teams carefully position each rug to ensure effortless installation and refined presentation.</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     4. WEAVE YOUR DREAM RUG — full-bleed dark photo section (swapped down)
══════════════════════════════════════════════════════════════ --}}
<section class="relative h-[55vh] min-h-[420px] flex items-center justify-center overflow-hidden">
    {{-- Background: Frame 427319720 photo --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/frame 427319720.png') }}" alt="Weave Your Dream Rug"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:rgba(18,18,18,0.52);"></div>
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
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Costikyan Custom Carpet",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/cover.jpg') }}",
    "description": "Handcrafted custom rugs made to your specifications since 1886.",
    "sameAs": [],
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "customer service",
        "availableLanguage": "English"
    }
}
</script>
@endpush
