@extends('layouts.site')
@section('title', $product->name)
@section('meta_description', Str::limit(strip_tags($product->description ?? 'Handcrafted ' . $product->name . ' rug by Costikyan Custom Carpet. Available in multiple sizes and finishes.'), 155))
@section('og_type', 'product')
@section('og_image', $product->primary_image_url)

@php
    // Determine product type for conditional sections — driven by the product's
    // category first (in-stock / custom-designs / made-to-order), with heuristic fallback.
    $catSlug = $product->category->slug ?? '';
    $isCustomSize  = $catSlug === 'custom-designs';
    $isMadeToOrder = $catSlug === 'made-to-order';
    $isInStock     = $catSlug === 'in-stock';

    if (!$isInStock && !$isCustomSize && !$isMadeToOrder) {
        // Fallback heuristic for products without one of the three core categories
        $isCustomSize  = $product->featured && !$product->is_bestseller && !$product->is_new_arrival;
        $isMadeToOrder = $product->is_new_arrival || ($product->stock == 0 && !$product->featured);
        $isInStock     = !$isCustomSize && !$isMadeToOrder;
    }

    // Badge label
    if ($product->is_bestseller)    $badgeLabel = 'BEST SELLER';
    elseif ($product->is_new_arrival) $badgeLabel = 'NEW ARRIVAL';
    elseif ($product->featured)     $badgeLabel = 'SIGNATURE';
    else                            $badgeLabel = 'BEST SELLER';

    // Images array
    $images = $product->images->count() ? $product->images : collect();
    $primaryImg = $product->primary_image_url;

    // Rug finish options (Made to Order only)
    $rugFinishes = [
        ['name' => 'Machine Narrow Binding', 'desc' => 'A machine-applied fabric binding for a consistent and clean finishing edge.', 'img' => 'images/Frame 50.png'],
        ['name' => 'Machine Serge',          'desc' => 'A straight stitch with a continuous series of interlocked stitches for a durable and consistent finish.', 'img' => 'images/Frame 51.png'],
        ['name' => 'Custom Wide Bind',       'desc' => 'A wide fabric binding customized to your needs for a bold, decorative edge.', 'img' => 'images/Frame 53.png'],
        ['name' => 'Hand Serge',             'desc' => 'The rug edge is finished by hand with a fabric binding for a tailored look.', 'img' => 'images/Frame 54.png'],
    ];
@endphp

@section('content')
<div class="bg-white" x-data="{
    imgIdx: 0,
    images: {{ json_encode($images->map(fn($i) => route('media.show', ['path' => $i->path]))->toArray() ?: [$primaryImg]) }},
    @php
        $dimPrices = $product->dimensionPrices;
        $hasDimPrices = $dimPrices->count() > 0;
        $firstDim = $dimPrices->firstWhere('is_default') ?? $dimPrices->first();
    @endphp
    @if($hasDimPrices)
    selectedSize: '{{ $firstDim?->id ?? 'custom' }}',
    lastStandardSize: '{{ $firstDim?->id ?? 'custom' }}',
    dimPrices: {{ json_encode($dimPrices->mapWithKeys(fn($d) => [$d->id => ['price'=>$d->price,'sale_price'=>$d->sale_price,'label'=>$d->label,'width'=>$d->width,'length'=>$d->length,'stock'=>$d->stock]])->toArray()) }},
    sizeModifiers: {},
    @else
    selectedSize: '{{ $product->sizes->first()?->label ?? '6x9' }}',
    lastStandardSize: '{{ $product->sizes->first()?->label ?? '6x9' }}',
    dimPrices: {},
    sizeModifiers: {{ json_encode($product->sizes->pluck('price_modifier', 'label')->toArray() ?: ['6x9'=>1,'8x10'=>1.33,'9x12'=>1.5,'10x14'=>1.94,'12x15'=>2.5]) }},
    @endif
    selectedColor: '{{ $product->colors->first()?->color_name ?? '' }}',
    selectedFinish: 'Machine Narrow Binding',
    qty: 1,
    customOpen: false, wFt: '', wIn: '', hFt: '', hIn: '',
    addOns: { protector: false, padding: false, spot: false },
    paddingType: 'Dry Grip',
    delivery: 'whiteglove',
    showZip: false,
    zip: '',
    upsQuote: null,
    upsQuoteMsg: '',
    checkZip() {
        const z = (this.zip || '').replace(/[^0-9]/g, '').slice(0, 5);
        if (z.length < 5) { this.upsQuote = null; this.upsQuoteMsg = ''; return; }
        this.upsQuoteMsg = 'Checking…';
        fetch('{{ route('shipping.quote') }}?zip=' + z)
            .then(r => r.json())
            .then(d => {
                if (d.found) {
                    this.upsQuote = Number(d.price);
                    this.upsQuoteMsg = (d.estimated ? 'Est. UPS to ' : 'UPS to ') + z + ': $' + this.upsQuote.toFixed(2)
                        + (d.estimated ? ' (confirmed at checkout)' : '');
                } else {
                    this.upsQuote = null;
                    this.upsQuoteMsg = d.reason === 'invalid'
                        ? 'Enter a valid 5-digit ZIP.'
                        : 'We\'ll confirm shipping to ' + z + ' at checkout.';
                }
            })
            .catch(() => { this.upsQuoteMsg = 'Could not check that ZIP. We\'ll confirm at checkout.'; });
    },
    showEmailModal: false,
    showSaveModal: false,
    showRoomModal: false,
    emailNotes: '',
    saveNotes: '',
    emailAddress: '{{ Auth::check() ? Auth::user()->email : '' }}',
    zoomSrc: null,
    zoomLabel: '',
    // Only fire the hover preview on real pointer devices — on touch screens a
    // 'hover' is emulated by a tap and would cover the page.
    showZoom(src, label) {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
        this.zoomSrc = src; this.zoomLabel = label || '';
    },
    hideZoom() { this.zoomSrc = null; this.zoomLabel = ''; },
    get currentImg() { return this.images[this.imgIdx] ?? '{{ $primaryImg }}'; },
    prev() { this.imgIdx = (this.imgIdx - 1 + this.images.length) % this.images.length; },
    next() { this.imgIdx = (this.imgIdx + 1) % this.images.length; },
    get customSqFt() {
        let w = (parseFloat(this.wFt) || 0) + (parseFloat(this.wIn) || 0) / 12;
        let h = (parseFloat(this.hFt) || 0) + (parseFloat(this.hIn) || 0) / 12;
        return (w > 0 && h > 0) ? w * h : 0;
    },
    get sizePrice() {
        if (Object.keys(this.dimPrices).length > 0) {
            let dim = this.dimPrices[this.selectedSize];
            if (dim) return Number(dim.sale_price ?? dim.price);
            if (this.selectedSize === 'custom') {
                let sqft = this.customSqFt;
                let ref = this.dimPrices[this.lastStandardSize];
                let refPrice = Number(ref ? (ref.sale_price ?? ref.price) : {{ $product->sale_price ?? $product->price }});
                let refSqft = (ref && ref.width && ref.length) ? (ref.width * ref.length) : 54;
                return sqft > 0 ? Math.round(refPrice * (sqft / refSqft)) : refPrice;
            }
            return {{ $product->sale_price ?? $product->price }};
        }
        let base = {{ $product->sale_price ?? $product->price }};
        if (this.selectedSize === 'custom') {
            let sqft = this.customSqFt;
            return sqft > 0 ? Math.round(base * (sqft / 54)) : base;
        }
        return Math.round(base * (this.sizeModifiers[this.selectedSize] ?? 1));
    },
    get addOnsTotal() {
        let add = 0;
        if (this.addOns.protector) add += 120;
        if (this.addOns.padding) add += 190;
        if (this.addOns.spot) add += 19.99;
        return add;
    },
    get deliveryPrice() {
        if (this.delivery === 'whiteglove') return 250;
        if (this.delivery === 'ups') return this.upsQuote ?? 500;
        if (this.delivery === 'pickup') return 50;
        return 0;
    },
    get deliveryLabel() {
        if (this.delivery === 'whiteglove') return 'White-Glove Delivery';
        if (this.delivery === 'ups') return 'Standard UPS Shipping';
        if (this.delivery === 'pickup') return 'Warehouse Pick-Up';
        return 'Delivery';
    },
    get qtyNum() { return Math.max(1, parseInt(this.qty) || 1); },
    // Rug + add-ons scale with quantity; delivery is charged once.
    get estimateTotal() {
        return (this.sizePrice + this.addOnsTotal) * this.qtyNum + this.deliveryPrice;
    },
    fmt(n) { return '$' + Number(n).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); },
    roomGenerating: false,
    roomResultUrl: '',
    roomError: '',
    roomStatus: '',
    roomCredits: {{ Auth::check() ? (int) Auth::user()->ai_credits : 0 }},
    resetRoom() { this.roomResultUrl = ''; this.roomError = ''; this.roomStatus = ''; },
    async generateRoom(e) {
        const form = e.target;
        if (!form.room_photo.files.length) return;
        this.roomError = '';
        this.roomResultUrl = '';
        this.roomGenerating = true;
        const msgs = ['Analyzing your room…', 'Placing your rug…', 'Matching lighting & shadows…', 'Adding finishing touches…'];
        let i = 0; this.roomStatus = msgs[0];
        const ticker = setInterval(() => { i = (i + 1) % msgs.length; this.roomStatus = msgs[i]; }, 2800);
        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });
            let json = {};
            try { json = await res.json(); } catch (_) {}
            if (res.ok && json.success) {
                this.roomResultUrl = json.url;
                if (typeof json.credits !== 'undefined') this.roomCredits = json.credits;
                form.reset();
                document.getElementById('roomPreview')?.classList.add('hidden');
                document.getElementById('roomFilename').textContent = 'Click to upload room photo';
            } else {
                this.roomError = (json && json.error) ? json.error : 'Generation failed. Please try again.';
                if (typeof json.credits !== 'undefined') this.roomCredits = json.credits;
            }
        } catch (err) {
            this.roomError = 'Something went wrong. Please check your connection and try again.';
        } finally {
            clearInterval(ticker);
            this.roomGenerating = false;
        }
    }
}">

    {{-- ── TOP BAR ── --}}
    <div class="max-w-[1200px] mx-auto px-6 pt-6 pb-2">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-[12px] text-[#121212] hover:opacity-70">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            BACK
        </a>
    </div>

    {{-- ── MAIN 2-COL LAYOUT ── --}}
    <div class="max-w-[1200px] mx-auto px-6 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-10 lg:gap-14 items-start">

            {{-- ════════════════════════════════
                 LEFT COLUMN
            ════════════════════════════════ --}}
            <div>
                {{-- Badge + Title + Meta --}}
                <p style="font-family:'Lusitana',serif; font-size:12px; font-weight:400; letter-spacing:0.1em; color:#121212; text-transform:uppercase;" class="mb-1">
                    {{ $badgeLabel }}
                </p>
                @php
                    $isFav = auth()->check() && auth()->user()->wishlist->contains('product_id', $product->id);
                @endphp
                <div class="flex items-start justify-between gap-3 mb-1">
                    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; line-height:1.3; color:#121212;">
                        {{ $product->display_name }}
                    </h1>
                    <button class="wishlist-toggle flex-shrink-0 transition-colors {{ $isFav ? 'text-red-500' : 'text-stone-400 hover:text-stone-700' }}"
                            data-product-id="{{ $product->id }}"
                            data-in-wishlist="{{ $isFav ? 'true' : 'false' }}"
                            data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                            style="background:none; border:none; cursor:pointer; padding:4px; margin-top:2px;"
                            title="{{ $isFav ? 'Remove from favorites' : 'Add to favorites' }}">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" {!! $isFav ? 'fill="currentColor" stroke="none"' : 'fill="none" stroke="currentColor"' !!} stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>
                <p style="font-size:12px; color:rgba(18,18,18,0.6);" class="mb-0.5">
                    Construction: {{ $product->construction ?? 'Hand-knotted' }}
                </p>
                <p style="font-size:12px; color:rgba(18,18,18,0.6);" class="mb-4">
                    Material: {{ $product->material ?? '100% New Zealand Wool' }}
                </p>

                {{-- Price --}}
                <p style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212;" class="mb-3">
                    @if($hasDimPrices)
                    <span x-text="(selectedSize === 'custom' ? '' : 'From ') + '$' + sizePrice.toLocaleString('en-US', {maximumFractionDigits:0})">From ${{ number_format($firstDim?->effective_price ?? $product->effective_price, 0) }}</span>
                    @else
                    <span x-text="(selectedSize === 'custom' ? '' : 'From ') + '$' + sizePrice.toLocaleString('en-US', {maximumFractionDigits:0})">From ${{ number_format($product->sale_price ?? $product->price, 0) }}</span>
                    @endif
                </p>

                {{-- Short description --}}
                @if($product->description)
                <div x-data="{ descOpen: false }" class="mb-6" style="max-width:420px;">
                    <p style="font-size:13px; color:rgba(18,18,18,0.75); line-height:1.6;">
                        <span x-show="!descOpen">{{ Str::limit($product->description, 180) }}</span>
                        <span x-show="descOpen" x-cloak>{{ $product->description }}</span>
                    </p>
                    @if(Str::length($product->description) > 180)
                    <button @click="descOpen = !descOpen" type="button"
                            class="mt-1.5" style="font-size:13px; font-weight:600; color:#E8651A; background:none; border:none; cursor:pointer; padding:0;"
                            x-text="descOpen ? 'Read less' : 'Read more'"></button>
                    @endif
                </div>
                @endif

                {{-- ── IMAGE CAROUSEL ── --}}
                <div class="relative overflow-hidden mb-6 group cursor-zoom-in" style="aspect-ratio:4/3; background:#f5f3f0; border-radius:2px;"
                     @mouseenter="showZoom(currentImg, '{{ addslashes($product->name) }}')" @mouseleave="hideZoom()">
                    <img :src="currentImg" alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-all duration-500 ease-out group-hover:scale-150">

                    {{-- Arrows --}}
                    <button @click="prev()"
                            class="absolute left-3 top-1/2 -translate-y-1/2 flex items-center justify-center bg-white/80 hover:bg-white transition-colors"
                            style="width:36px; height:36px; border-radius:2px; box-shadow:0 1px 4px rgba(0,0,0,0.15);">
                        <svg width="16" height="16" fill="none" stroke="#121212" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="next()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center bg-white/80 hover:bg-white transition-colors"
                            style="width:36px; height:36px; border-radius:2px; box-shadow:0 1px 4px rgba(0,0,0,0.15);">
                        <svg width="16" height="16" fill="none" stroke="#121212" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Dot indicators --}}
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                        <template x-for="(img, i) in images" :key="i">
                            <button @click="imgIdx = i"
                                    :class="i === imgIdx ? 'bg-white' : 'bg-white/40'"
                                    class="w-1.5 h-1.5 rounded-full transition-colors"></button>
                        </template>
                    </div>
                </div>

                {{-- Thumbnail strip --}}
                @if($product->images->count() > 1)
                <div class="flex gap-2 mb-8">
                    @foreach($product->images as $i => $img)
                    <button @click="imgIdx = {{ $i }}"
                            @mouseenter="showZoom('{{ route('media.show', ['path' => $img->path]) }}', '{{ addslashes($product->name) }}')"
                            @mouseleave="hideZoom()"
                            :class="{{ $i }} === imgIdx ? 'ring-2 ring-[#121212]' : 'ring-1 ring-stone-200'"
                            class="flex-shrink-0 overflow-hidden cursor-zoom-in"
                            style="width:60px; height:60px; border-radius:2px;">
                        <img src="{{ route('media.show', ['path' => $img->path]) }}" class="w-full h-full object-cover" alt="" loading="lazy">
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- ── RUG DETAILS TABLE ── --}}
                <div class="mb-6">
                    <p style="font-family:'Lusitana',serif; font-size:14px; font-weight:700; color:#121212;" class="mb-3">Rug Details:</p>
                    <table class="w-full text-sm" style="border:1px solid rgba(18,18,18,0.1); border-radius:2px;">
                        <tbody>
                            @foreach([
                                ['Material',       $product->material     ?? 'New Zealand Wool & Silk'],
                                ['Construction',   $product->construction ?? 'Hand-Knotted'],
                                ['Origin',         $product->origin     ?? 'Nepal'],
                                ['Style / Pattern',$product->style      ?? 'Abstract'],
                                ['Color',          $product->colors->first()?->color_name ?? 'Beige'],
                            ] as [$label, $val])
                            <tr style="border-bottom:1px solid rgba(18,18,18,0.08);">
                                <td style="padding:10px 16px; color:rgba(18,18,18,0.6); width:40%; font-size:13px;">{{ $label }}</td>
                                <td style="padding:10px 16px; color:#121212; font-size:13px;">{{ $val }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ── SEE IT IN YOUR ROOM ── --}}
                <div class="mb-2">
                    <div class="flex items-center justify-between mb-2">
                        <p style="font-family:'Lusitana',serif; font-size:14px; font-weight:400; color:#121212;">See it in your room:</p>
                        @auth
                        <span style="font-size:11px; color:#E8651A; font-weight:600;">{{ Auth::user()->ai_credits }} credits left</span>
                        @endauth
                    </div>
                    <button @click="showRoomModal = true"
                            class="w-full flex flex-col items-center justify-center transition-colors hover:bg-stone-50"
                            style="border:1px dashed rgba(18,18,18,0.2); border-radius:2px; height:140px; background:#fafafa; cursor:pointer;">
                        <svg width="28" height="28" fill="none" stroke="rgba(18,18,18,0.35)" stroke-width="1.5" viewBox="0 0 24 24" class="mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <span style="font-size:11px; color:rgba(18,18,18,0.45); letter-spacing:0.06em; text-transform:uppercase;">Upload Room Photo</span>
                    </button>
                </div>

            </div>{{-- /left --}}

            {{-- ════════════════════════════════
                 RIGHT COLUMN — SUMMARY PANEL
            ════════════════════════════════ --}}
            <div class="lg:sticky lg:top-[72px]">
                {{-- Panel header --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;">Summary</h2>
                    <button class="wishlist-toggle transition-colors {{ $isFav ? 'text-red-500' : 'text-stone-400 hover:text-red-400' }}"
                            data-product-id="{{ $product->id }}"
                            data-in-wishlist="{{ $isFav ? 'true' : 'false' }}"
                            data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                            style="background:none; border:none; cursor:pointer; padding:4px;"
                            title="{{ $isFav ? 'Remove from favorites' : 'Add to favorites' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" {!! $isFav ? 'fill="currentColor" stroke="none"' : 'fill="none" stroke="currentColor"' !!} stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>

                {{-- ── DIMENSIONS ── --}}
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212; letter-spacing:0.02em;" class="mb-3">Dimensions</p>

                    {{-- Standard size pills --}}
                    <div style="background:#f5f5f5; border-radius:4px; padding:10px 12px;" class="mb-2">
                        <div class="flex flex-wrap gap-2">
                            @if($hasDimPrices)
                                @foreach($dimPrices as $dim)
                                <button @click="selectedSize = '{{ $dim->id }}'; lastStandardSize = '{{ $dim->id }}'; customOpen = false"
                                        :class="selectedSize === '{{ $dim->id }}' ? 'border-[#121212] bg-white font-semibold' : 'border-transparent bg-transparent text-[rgba(18,18,18,0.6)]'"
                                        class="border transition-all"
                                        style="padding:5px 14px; font-size:13px; border-radius:4px; color:#121212;">
                                    {{ $dim->label ?: $dim->dimension_display }}
                                </button>
                                @endforeach
                            @else
                                @foreach($product->sizes as $sz)
                                <button @click="selectedSize = '{{ $sz->label }}'; lastStandardSize = '{{ $sz->label }}'; customOpen = false"
                                        :class="selectedSize === '{{ $sz->label }}' ? 'border-[#121212] bg-white font-semibold' : 'border-transparent bg-transparent text-[rgba(18,18,18,0.6)]'"
                                        class="border transition-all"
                                        style="padding:5px 14px; font-size:13px; border-radius:4px; color:#121212;">
                                    {{ $sz->label }}
                                </button>
                                @endforeach
                                @if($product->sizes->isEmpty())
                                @foreach(['6x9','8x10','9x12','10x14','12x15'] as $sz)
                                <button @click="selectedSize = '{{ $sz }}'; lastStandardSize = '{{ $sz }}'; customOpen = false"
                                        :class="selectedSize === '{{ $sz }}' ? 'border-[#121212] bg-white font-semibold' : 'border-transparent bg-transparent text-[rgba(18,18,18,0.6)]'"
                                        class="border transition-all"
                                        style="padding:5px 14px; font-size:13px; border-radius:4px; color:#121212;">
                                    {{ $sz }}
                                </button>
                                @endforeach
                                @endif
                            @endif
                        </div>

                        {{-- CUSTOM SIZE accordion row --}}
                        <button @click="customOpen = !customOpen; if(customOpen) { selectedSize = 'custom'; } else { selectedSize = lastStandardSize; }"
                                class="w-full flex items-center justify-between mt-3 pt-3"
                                style="border-top:1px solid rgba(18,18,18,0.1); font-size:11px; font-weight:600; letter-spacing:0.1em; color:rgba(18,18,18,0.7);">
                            <span>CUSTOM SIZE</span>
                            <svg :class="customOpen ? 'rotate-180' : ''" class="transition-transform"
                                 width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Custom inputs --}}
                        <div x-show="customOpen" x-cloak class="mt-4 space-y-3">
                            <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                                {{-- Width --}}
                                <div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.5);" class="mb-1.5">Width (ft, in)</p>
                                    <div class="flex gap-1.5">
                                        <div class="relative flex-1">
                                            <input x-model="wFt" type="number" min="0" placeholder=""
                                                   class="w-full focus:outline-none text-right pr-8"
                                                   style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:8px 10px; font-size:13px;">
                                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2" style="font-size:12px; color:rgba(18,18,18,0.4);">ft</span>
                                        </div>
                                        <div class="relative flex-1">
                                            <input x-model="wIn" type="number" min="0" max="11" placeholder=""
                                                   class="w-full focus:outline-none text-right pr-6"
                                                   style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:8px 10px; font-size:13px;">
                                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2" style="font-size:12px; color:rgba(18,18,18,0.4);">in</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- × --}}
                                <span style="font-size:14px; color:rgba(18,18,18,0.4); padding-top:20px;">×</span>
                                {{-- Height --}}
                                <div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.5);" class="mb-1.5">Height (ft, in)</p>
                                    <div class="flex gap-1.5">
                                        <div class="relative flex-1">
                                            <input x-model="hFt" type="number" min="0" placeholder=""
                                                   class="w-full focus:outline-none text-right pr-8"
                                                   style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:8px 10px; font-size:13px;">
                                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2" style="font-size:12px; color:rgba(18,18,18,0.4);">ft</span>
                                        </div>
                                        <div class="relative flex-1">
                                            <input x-model="hIn" type="number" min="0" max="11" placeholder=""
                                                   class="w-full focus:outline-none text-right pr-6"
                                                   style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:8px 10px; font-size:13px;">
                                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2" style="font-size:12px; color:rgba(18,18,18,0.4);">in</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Live calculated price for custom size --}}
                            <div class="flex items-center justify-between gap-3" style="background:#f8f8f8; border-radius:3px; padding:10px 12px;">
                                <span style="font-size:12px; color:rgba(18,18,18,0.6);">
                                    Custom dimensions:
                                    <span x-text="((parseFloat(wFt)||0) + (parseFloat(wIn)||0)/12).toFixed(2) + ' ft'" style="font-weight:600; color:#121212;"></span>
                                    ×
                                    <span x-text="((parseFloat(hFt)||0) + (parseFloat(hIn)||0)/12).toFixed(2) + ' ft'" style="font-weight:600; color:#121212;"></span>
                                </span>
                                <span style="font-size:14px; font-weight:700; color:#121212; font-family:'Lusitana',serif;"
                                      x-text="'$' + sizePrice.toLocaleString('en-US', {maximumFractionDigits:0})">
                                    ${{ number_format($product->sale_price ?? $product->price, 0) }}
                                </span>
                            </div>

                            {{-- Info note --}}
                            <div class="flex items-start gap-2" style="background:#f8f8f8; border-radius:3px; padding:10px 12px;">
                                <svg width="14" height="14" fill="none" stroke="rgba(18,18,18,0.45)" stroke-width="1.5" viewBox="0 0 24 24" class="flex-shrink-0 mt-0.5">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <p style="font-size:12px; color:rgba(18,18,18,0.6); line-height:1.6;">
                                    Custom sized rugs are made to order and may take an additional 2-3 weeks for delivery. All sales are final for custom dimensions.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── RUG FINISH (Made to Order only) ── --}}
                @if($isMadeToOrder)
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-3">Rug Finish</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($rugFinishes as $finish)
                        <button @click="selectedFinish = '{{ $finish['name'] }}'"
                                :class="selectedFinish === '{{ $finish['name'] }}' ? 'ring-2 ring-[#121212]' : 'ring-1 ring-[rgba(18,18,18,0.15)]'"
                                class="text-left p-2.5 bg-white transition-all flex flex-col"
                                style="border-radius:3px;">
                            <p style="font-size:11px; font-weight:600; color:#121212; line-height:1.3; text-transform:uppercase; letter-spacing:0.04em;" class="mb-1">{{ $finish['name'] }}</p>
                            <p style="font-size:10px; color:rgba(18,18,18,0.55); line-height:1.4;" class="mb-2 flex-1">{{ $finish['desc'] }}</p>
                            @if(!empty($finish['img']))
                            <img src="{{ asset($finish['img']) }}" alt="{{ $finish['name'] }}" loading="lazy"
                                 @mouseenter="showZoom('{{ asset($finish['img']) }}', '{{ addslashes($finish['name']) }}')"
                                 @mouseleave="hideZoom()"
                                 class="w-full object-cover cursor-zoom-in" style="height:70px; border-radius:2px;">
                            @else
                            <div class="w-full bg-stone-100" style="height:70px; border-radius:2px;"></div>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── COLOR (Made to Order only) ── --}}
                @if($isMadeToOrder && $product->colors->count())
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-3">Color</p>
                    <div class="flex gap-2 flex-wrap">
                        @foreach($product->colors as $color)
                        <button @click="selectedColor = '{{ $color->color_name }}'"
                                class="flex flex-col items-center gap-1">
                            <span :class="selectedColor === '{{ $color->color_name }}' ? 'ring-2 ring-[#121212] ring-offset-1' : ''"
                                  class="block rounded-sm transition-all"
                                  style="width:44px; height:44px; background-color:{{ $color->color_hex }};
                                         border:1px solid rgba(18,18,18,0.12);"></span>
                            <span style="font-size:10px; color:rgba(18,18,18,0.65);">{{ $color->color_name }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── ADD-ON SERVICES ── --}}
                <style>
                    .cc-addon-zoom { overflow:hidden; }
                    .cc-addon-zoom img { transition: transform .45s ease; will-change: transform; }
                    .cc-addon-zoom:hover img { transform: scale(1.4); }
                </style>
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-3">Add-on Services</p>
                    <div class="space-y-2">

                        {{-- Rug Protector --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" x-model="addOns.protector" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Rug Protector</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;">+$120.00</span>
                                    </div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.55);">Fiber-shield® Stain Protectant</p>
                                </div>
                            </div>
                        </div>

                        {{-- Premium Padding --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" x-model="addOns.padding" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Premium Padding</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;">+$190.00</span>
                                    </div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.55);">Extra comfort, grip, and longevity</p>
                                    {{-- Sub-options: selectable when padding is chosen --}}
                                    <p x-show="addOns.padding" x-cloak style="font-size:10px; color:#E8651A; font-weight:600; margin-top:6px;">Choose your pad:</p>
                                    <div class="grid grid-cols-2 gap-2 mt-2" :style="addOns.padding ? '' : 'opacity:.55'">
                                        <button type="button" @click="addOns.padding = true; paddingType = 'Dry Grip'"
                                                class="border p-2 flex items-start gap-2 text-left transition-all"
                                                :style="paddingType === 'Dry Grip' && addOns.padding ? 'border-color:#121212; box-shadow:0 0 0 1px #121212; border-radius:2px;' : 'border-color:rgba(18,18,18,0.12); border-radius:2px;'">
                                            <span class="cc-addon-zoom flex-shrink-0 block" style="width:49px; height:49px; border:1px solid rgba(18,18,18,0.12); border-radius:2px;">
                                                <img src="{{ asset('images/Frame 427319814.png') }}" alt="Dry Grip" loading="lazy"
                                                     class="object-cover" style="width:100%; height:100%; display:block;">
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p style="font-size:10px; font-weight:600; color:#121212;">Dry Grip <span x-show="paddingType==='Dry Grip' && addOns.padding" style="color:#E8651A;">&#10003;</span></p>
                                                <p style="font-size:9px; color:rgba(18,18,18,0.5); line-height:1.3;">A thinner rug pad for a lower profile, non-slip finish.</p>
                                            </div>
                                        </button>
                                        <button type="button" @click="addOns.padding = true; paddingType = 'Durahold'"
                                                class="border p-2 flex items-start gap-2 text-left transition-all"
                                                :style="paddingType === 'Durahold' && addOns.padding ? 'border-color:#121212; box-shadow:0 0 0 1px #121212; border-radius:2px;' : 'border-color:rgba(18,18,18,0.12); border-radius:2px;'">
                                            <span class="cc-addon-zoom flex-shrink-0 block" style="width:49px; height:49px; border:1px solid rgba(18,18,18,0.12); border-radius:2px;">
                                                <img src="{{ asset('images/Frame 427319815.png') }}" alt="Durahold" loading="lazy"
                                                     class="object-cover" style="width:100%; height:100%; display:block;">
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p style="font-size:10px; font-weight:600; color:#121212;">Durahold <span x-show="paddingType==='Durahold' && addOns.padding" style="color:#E8651A;">&#10003;</span></p>
                                                <p style="font-size:9px; color:rgba(18,18,18,0.5); line-height:1.3;">A thicker, more cushioned rug pad—ideal for added comfort and support.</p>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Spot Kit Cleaner --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" x-model="addOns.spot" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <span class="cc-addon-zoom flex-shrink-0 block" style="width:49px; height:49px; border-radius:2px;">
                                    <img src="{{ asset('images/Frame 500.png') }}" alt="Spot Kit Cleaner" loading="lazy"
                                         class="object-cover" style="width:100%; height:100%; display:block;">
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Spot Kit Cleaner</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;">+$19.99</span>
                                    </div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.55);">Remove stains with our 8oz. bottled, all-purpose cleaning product for your furnishings.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── DELIVERY METHOD ── --}}
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-3">Delivery method</p>
                    <div class="space-y-2">

                        {{-- White Glove --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="delivery_method" value="whiteglove" x-model="delivery"
                                       class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">White-Glove Delivery & Spread</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;">+$250</span>
                                    </div>
                                    <p style="font-size:11px; color:rgba(18,18,18,0.55);">
                                        @if($isMadeToOrder) Timeline: 8-12 weeks @else Timeline: 2 weeks @endif
                                    </p>
                                    <div class="mt-2 flex items-start gap-2 px-3 py-2" style="background:#f9f9f9; border-radius:2px;">
                                        <svg width="13" height="13" fill="none" stroke="rgba(18,18,18,0.5)" stroke-width="1.5" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                                        </svg>
                                        <p style="font-size:10px; color:rgba(18,18,18,0.55); line-height:1.5;">This delivery option is currently available only in New York, New Jersey, Connecticut, Boston, and Philadelphia.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Standard UPS --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="delivery_method" value="ups" x-model="delivery" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Standard UPS Shipping</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;" x-text="upsQuote != null ? '+$' + upsQuote.toFixed(2) : '+$500'">+$500</span>
                                    </div>
                                    <button @click="showZip = !showZip" x-show="!showZip"
                                            class="border px-3 py-1.5 text-xs"
                                            style="border-color:rgba(18,18,18,0.25); border-radius:3px; color:#121212;">
                                        Enter Zip Code
                                    </button>
                                    <div x-show="showZip" class="flex items-center gap-2">
                                        <input x-model="zip" @input.debounce.500ms="checkZip()" placeholder="Enter ZIP" type="text" inputmode="numeric" maxlength="5"
                                               class="border px-3 py-1.5 text-xs flex-1 focus:outline-none"
                                               style="border-color:rgba(18,18,18,0.25); border-radius:3px;">
                                        <button type="button" @click="checkZip()" class="text-white text-xs px-3 py-1.5" style="background:#121212; border-radius:3px;">Check</button>
                                    </div>
                                    <p x-show="showZip && upsQuoteMsg" x-text="upsQuoteMsg" style="font-size:10px; color:#E8651A; margin-top:4px;"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Warehouse Pick-up --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="delivery_method" value="pickup" x-model="delivery" class="w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex items-center justify-between flex-1">
                                    <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Warehouse Pick-up</p>
                                    <span style="font-size:12px; font-weight:600; color:#121212;">+$50</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── QUANTITY ── --}}
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-2">Quantity</p>
                    <div class="flex items-center border" style="border-color:rgba(18,18,18,0.2); border-radius:3px; height:42px; max-width:200px;">
                        <button @click="qty = Math.max(1, qty - 1)"
                                class="flex items-center justify-center hover:bg-stone-50 flex-shrink-0"
                                style="width:42px; height:42px; border-right:1px solid rgba(18,18,18,0.15);">
                            <svg width="14" height="14" fill="none" stroke="#121212" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14"/></svg>
                        </button>
                        <span x-text="qty" class="flex-1 text-center" style="font-size:14px; color:#121212;"></span>
                        <button @click="qty = Math.min(99, qty + 1)"
                                class="flex items-center justify-center hover:bg-stone-50 flex-shrink-0"
                                style="width:42px; height:42px; border-left:1px solid rgba(18,18,18,0.15);">
                            <svg width="14" height="14" fill="none" stroke="#121212" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                    </div>
                </div>

                {{-- ── CTA BUTTONS ── --}}
                <form action="{{ route('cart.add') }}" method="POST" class="space-y-2 mb-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="qty">
                    <input type="hidden" name="size" :value="selectedSize">
                    <input type="hidden" name="color" :value="selectedColor">
                    <input type="hidden" name="custom_width"  :value="selectedSize==='custom' ? ((parseFloat(wFt)||0)+(parseFloat(wIn)||0)/12).toFixed(2) : ''">
                    <input type="hidden" name="custom_length" :value="selectedSize==='custom' ? ((parseFloat(hFt)||0)+(parseFloat(hIn)||0)/12).toFixed(2) : ''">
                    <input type="hidden" name="delivery_method" :value="delivery">
                    <template x-if="addOns.protector"><input type="hidden" name="addons[]" value="protector"></template>
                    <template x-if="addOns.padding"><input type="hidden" name="addons[]" value="padding"></template>
                    <template x-if="addOns.padding"><input type="hidden" name="padding_type" :value="paddingType"></template>
                    <template x-if="addOns.spot"><input type="hidden" name="addons[]" value="spot"></template>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 flex items-center justify-center text-white transition-colors"
                                style="background:#121212; height:48px; font-family:'Lusitana',serif; font-size:15px; border-radius:3px;">
                            Add to Cart
                        </button>
                        @auth
                        <button type="button" class="flex items-center justify-center border hover:bg-stone-50"
                                style="width:48px; height:48px; border-color:rgba(18,18,18,0.2); border-radius:3px;">
                            <svg width="18" height="18" fill="none" stroke="#121212" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                        @endauth
                    </div>
                </form>

                {{-- Order Sample: adds a free swatch to the cart (no login needed; #8) --}}
                <form action="{{ route('cart.add') }}" method="POST" class="mb-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="is_sample" value="1">
                    <input type="hidden" name="color" :value="selectedColor">
                    <button type="submit"
                            class="w-full flex items-center justify-center border transition-colors hover:bg-stone-50"
                            style="height:44px; border-color:rgba(18,18,18,0.25); border-radius:3px;
                                   font-family:'Lusitana',serif; font-size:14px; color:#121212;">
                        Order Sample
                    </button>
                </form>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button type="button" @click="showEmailModal = true"
                            class="flex items-center justify-center border transition-colors hover:bg-stone-50"
                            style="height:40px; border-color:rgba(18,18,18,0.25); border-radius:3px;
                                   font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                        Email My Estimate
                    </button>
                    <button type="button" @click="showSaveModal = true"
                            class="flex items-center justify-center border transition-colors hover:bg-stone-50"
                            style="height:40px; border-color:rgba(18,18,18,0.25); border-radius:3px;
                                   font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                        Save My Estimate
                    </button>
                </div>

                {{-- Trade pricing --}}
                <div class="flex items-center justify-between pt-2" style="border-top:1px solid rgba(18,18,18,0.08);">
                    <p style="font-size:12px; color:rgba(18,18,18,0.6);">Log in to see our trade pricing.</p>
                    <a href="{{ route('login') }}"
                       class="flex items-center justify-center text-white"
                       style="background:#121212; padding:6px 16px; border-radius:3px; font-size:12px; font-family:'Lusitana',serif;">
                        Log in
                    </a>
                </div>

            </div>{{-- /right --}}

        </div>{{-- /grid --}}
    </div>{{-- /container --}}

    {{-- ── RELATED PRODUCTS ── --}}
    @if($related->count())
    <div class="max-w-[1200px] mx-auto px-6 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;">You May Also Like</h2>
            <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" style="font-size:13px; color:#121212; font-weight:500;" class="hover:opacity-70 transition-opacity">View All Similar Rugs →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($related as $relProduct)
                @include('partials.product-card', ['product' => $relProduct])
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════
         MODALS
      ══════════════════════════════════════════ --}}

    {{-- Email Estimate Modal --}}
    <div x-show="showEmailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.5);" @click.self="showEmailModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden" style="max-height:90vh; overflow-y:auto;">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(18,18,18,0.1);">
                <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#121212;">Email My Estimate</h3>
                <button @click="showEmailModal = false" class="text-stone-400 hover:text-stone-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('estimate.email', $product) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="size" :value="selectedSize">
                <input type="hidden" name="color" :value="selectedColor">
                <input type="hidden" name="finish" :value="selectedFinish">
                <input type="hidden" name="add_ons" :value="JSON.stringify(addOns)">
                <input type="hidden" name="delivery_method" :value="delivery">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#121212; display:block; margin-bottom:6px;">Your Email</label>
                    <input type="email" name="email" x-model="emailAddress" required
                           class="w-full px-3 py-2 text-sm border focus:outline-none"
                           style="border-color:rgba(18,18,18,0.2); border-radius:3px; color:#121212;"
                           placeholder="you@example.com">
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#121212; display:block; margin-bottom:6px;">Notes (optional)</label>
                    <textarea name="notes" x-model="emailNotes" rows="3"
                              class="w-full px-3 py-2 text-sm border focus:outline-none resize-none"
                              style="border-color:rgba(18,18,18,0.2); border-radius:3px; color:#121212;"
                              placeholder="Any special requests or questions..."></textarea>
                </div>
                <div style="background:#f8fafc; border-radius:3px; padding:12px;">
                    <p style="font-size:12px; color:#64748b;">Estimated Total: <span style="font-weight:700; color:#121212;" x-text="'$' + estimateTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})"></span></p>
                </div>
                <button type="submit"
                        class="w-full flex items-center justify-center text-white transition-colors hover:opacity-90"
                        style="background:#E8651A; height:44px; border-radius:3px; font-size:14px; font-weight:500;">
                    Send Estimate
                </button>
            </form>
        </div>
    </div>

    {{-- Save Estimate Modal --}}
    <div x-show="showSaveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.5);" @click.self="showSaveModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden" style="max-height:90vh; overflow-y:auto;">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(18,18,18,0.1);">
                <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#121212;">Save My Estimate</h3>
                <button @click="showSaveModal = false" class="text-stone-400 hover:text-stone-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @auth
            <form action="{{ route('estimate.save', $product) }}" method="POST" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="size" :value="selectedSize">
                <input type="hidden" name="color" :value="selectedColor">
                <input type="hidden" name="finish" :value="selectedFinish">
                <input type="hidden" name="add_ons" :value="JSON.stringify(addOns)">
                <input type="hidden" name="delivery_method" :value="delivery">
                <input type="hidden" name="estimated_price" :value="estimateTotal">
                <div>
                    <label style="font-size:12px; font-weight:600; color:#121212; display:block; margin-bottom:6px;">Notes (optional)</label>
                    <textarea name="notes" x-model="saveNotes" rows="3"
                              class="w-full px-3 py-2 text-sm border focus:outline-none resize-none"
                              style="border-color:rgba(18,18,18,0.2); border-radius:3px; color:#121212;"
                              placeholder="Add a personal note..."></textarea>
                </div>
                <div style="background:#f8fafc; border-radius:3px; padding:12px;">
                    <p style="font-size:12px; color:#64748b;">This will be saved to your account dashboard.</p>
                    <p style="font-size:12px; color:#64748b; margin-top:4px;">Estimated Total: <span style="font-weight:700; color:#121212;" x-text="'$' + estimateTotal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})"></span></p>
                </div>
                <button type="submit"
                        class="w-full flex items-center justify-center text-white transition-colors hover:opacity-90"
                        style="background:#121212; height:44px; border-radius:3px; font-size:14px; font-weight:500;">
                    Save to My Account
                </button>
            </form>
            @else
            <div class="p-8 text-center">
                <p style="font-size:14px; color:#64748b; margin-bottom:16px;">Please log in to save your estimate.</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center text-white transition-colors hover:opacity-90 px-6"
                   style="background:#E8651A; height:44px; border-radius:3px; font-size:14px; font-weight:500;">Log In</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- Room Visualization Modal --}}
    <style>
        @keyframes rugCubeSpin { from { transform: rotateX(-22deg) rotateY(0); } to { transform: rotateX(-22deg) rotateY(360deg); } }
        @keyframes rugGlow { 0%,100% { opacity:.35; } 50% { opacity:.9; } }
        .rug-loader-stage { perspective: 700px; }
        .rug-cube { width:76px; height:76px; margin:0 auto; position:relative; transform-style:preserve-3d; animation: rugCubeSpin 2.6s linear infinite; }
        .rug-cube > span { position:absolute; width:76px; height:76px; border:1px solid rgba(255,255,255,.3);
            background:linear-gradient(135deg,#E8651A 0%,#b8430a 100%); box-shadow:inset 0 0 22px rgba(0,0,0,.25); }
        .rc-front  { transform: translateZ(38px); }
        .rc-back   { transform: rotateY(180deg) translateZ(38px); }
        .rc-right  { transform: rotateY(90deg) translateZ(38px); }
        .rc-left   { transform: rotateY(-90deg) translateZ(38px); }
        .rc-top    { transform: rotateX(90deg) translateZ(38px); }
        .rc-bottom { transform: rotateX(-90deg) translateZ(38px); }
        .rug-shadow { width:90px; height:14px; margin:26px auto 0; border-radius:50%;
            background:radial-gradient(ellipse,rgba(0,0,0,.28),transparent 70%); animation: rugGlow 2.6s ease-in-out infinite; }
    </style>
    <div x-show="showRoomModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background:rgba(0,0,0,0.5);" @click.self="showRoomModal = false; resetRoom()">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden" style="max-height:90vh; overflow-y:auto;">
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(18,18,18,0.1);">
                <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#121212;">See It In Your Room</h3>
                <button @click="showRoomModal = false; resetRoom()" class="text-stone-400 hover:text-stone-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- 3D animated preloader --}}
            <div x-show="roomGenerating" x-cloak class="px-6 py-12 text-center rug-loader-stage">
                <div class="rug-cube">
                    <span class="rc-front"></span><span class="rc-back"></span>
                    <span class="rc-right"></span><span class="rc-left"></span>
                    <span class="rc-top"></span><span class="rc-bottom"></span>
                </div>
                <div class="rug-shadow"></div>
                <p class="mt-7" style="font-family:'Lusitana',serif; font-size:15px; font-weight:700; color:#121212;" x-text="roomStatus"></p>
                <p style="font-size:12px; color:#64748b; margin-top:6px;">Creating your visualization — this can take up to a minute.</p>
            </div>

            {{-- Result --}}
            <div x-show="roomResultUrl && !roomGenerating" x-cloak class="p-5 space-y-4">
                <img :src="roomResultUrl" alt="Your room with the rug" class="w-full rounded-lg border" style="border-color:rgba(18,18,18,0.1);">
                <div class="flex gap-2">
                    <a :href="roomResultUrl" download="room-visualization.png"
                       class="flex-1 flex items-center justify-center text-white hover:opacity-90"
                       style="background:#121212; height:42px; border-radius:3px; font-size:13px; font-weight:500;">Download</a>
                    <button @click="resetRoom()" type="button"
                       class="flex-1 flex items-center justify-center hover:opacity-80"
                       style="border:1px solid rgba(18,18,18,0.2); height:42px; border-radius:3px; font-size:13px; font-weight:500; color:#121212;">Try another photo</button>
                </div>
                <p style="font-size:12px; color:#64748b; text-align:center;">Credits remaining: <span x-text="roomCredits"></span></p>
            </div>

            {{-- Upload form (hidden while generating or showing a result) --}}
            <div x-show="!roomGenerating && !roomResultUrl">
            @auth
            @if(Auth::user()->ai_credits > 0)
            <form @submit.prevent="generateRoom" action="{{ route('room.visualize', $product) }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div>
                    <p style="font-size:13px; color:#64748b; line-height:1.6; margin-bottom:12px;">
                        Upload a photo of your room and our AI will place the <strong>{{ $product->name }}</strong> rug into it.
                    </p>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-1 rounded text-[10px] font-semibold" style="background:#dcfce7; color:#15803d;"><span x-text="roomCredits"></span> credits remaining</span>
                    </div>
                </div>
                <div x-show="roomError" x-cloak class="px-3 py-2 rounded" style="background:#fef2f2; border:1px solid #fecaca;">
                    <p style="font-size:12px; color:#b91c1c;" x-text="roomError"></p>
                </div>
                <div>
                    <label style="font-size:12px; font-weight:600; color:#121212; display:block; margin-bottom:6px;">Room Photo</label>
                    <div class="relative">
                        <input type="file" name="room_photo" accept="image/*" required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               onchange="document.getElementById('roomFilename').textContent = this.files[0]?.name || 'Choose file...';
                                         if (this.files[0]) { document.getElementById('roomPreview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('roomPreview').classList.remove('hidden'); }">
                        <div class="border border-dashed border-stone-300 rounded-lg p-4 text-center hover:border-amber-400 transition-colors">
                            <img id="roomPreview" class="hidden w-full max-h-48 object-cover rounded mb-2 mx-auto">
                            <p id="roomFilename" style="font-size:13px; color:#64748b;">Click to upload room photo</p>
                            <p style="font-size:11px; color:#9ca3af; margin-top:2px;">JPG, PNG up to 10MB</p>
                        </div>
                    </div>
                </div>
                <button type="submit" :disabled="roomGenerating"
                        class="w-full flex items-center justify-center text-white transition-colors hover:opacity-90"
                        style="background:#E8651A; height:44px; border-radius:3px; font-size:14px; font-weight:500;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Generate Visualization
                </button>
            </form>
            @else
            <div class="p-8 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center" style="background:#fef2f2;">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p style="font-size:14px; color:#121212; font-weight:600; margin-bottom:4px;">No credits left</p>
                <p style="font-size:13px; color:#64748b; margin-bottom:16px;">You have used all 3 of your AI room visualization credits.</p>
                <a href="{{ route('contact') }}" class="text-sm font-medium" style="color:#E8651A;">Contact us for more →</a>
            </div>
            @endif
            @else
            <div class="p-8 text-center">
                <p style="font-size:14px; color:#64748b; margin-bottom:16px;">Please log in to use See It In Your Room. You will receive 3 free AI credits.</p>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center text-white transition-colors hover:opacity-90 px-6"
                   style="background:#E8651A; height:44px; border-radius:3px; font-size:14px; font-weight:500;">Log In</a>
            </div>
            @endauth
            </div>{{-- /upload form wrapper --}}
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FLOATING PRICE CALCULATOR (Add-ons + Delivery + Quantity → Total)
      ══════════════════════════════════════════ --}}
    <style>
        .cc-fc-row { display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:12.5px; color:rgba(18,18,18,0.7); padding:4px 0; }
        .cc-fc-row span:last-child { color:#121212; font-weight:500; white-space:nowrap; }
        .cc-fc-total { border-top:1px solid rgba(18,18,18,0.12); margin-top:6px; padding-top:8px; font-size:14px; }
        .cc-fc-total span { color:#121212 !important; font-weight:700 !important; }
        @media (max-width: 640px) {
            .cc-floatcalc { left:12px !important; right:12px !important; width:auto !important; bottom:12px !important; }
        }
    </style>
    <div class="cc-floatcalc" x-data="{ calcOpen: true }"
         style="position:fixed; z-index:60; right:24px; bottom:24px; width:310px; max-width:calc(100vw - 32px);
                background:#fff; border:1px solid rgba(18,18,18,0.12); border-radius:10px;
                box-shadow:0 12px 34px rgba(10,13,18,0.18); font-family:'Inter',sans-serif; overflow:hidden;">
        <button type="button" @click="calcOpen = !calcOpen"
                style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:12px;
                       padding:13px 16px; background:#121212; color:#fff; border:none; cursor:pointer;">
            <span style="display:flex; flex-direction:column; align-items:flex-start; line-height:1.2;">
                <span style="font-size:10px; text-transform:uppercase; letter-spacing:0.08em; opacity:0.7;">Estimated Total</span>
                <span style="font-size:19px; font-weight:700;" x-text="fmt(estimateTotal)"></span>
            </span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                 :style="calcOpen ? '' : 'transform:rotate(180deg)'" style="transition:transform .2s; flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="calcOpen" style="padding:12px 16px 14px;">
            <div class="cc-fc-row">
                <span>Rug <span x-text="selectedSize==='custom' ? '(Custom)' : (dimPrices[selectedSize] ? '('+dimPrices[selectedSize].label+')' : '')"></span></span>
                <span x-text="fmt(sizePrice)"></span>
            </div>
            <div class="cc-fc-row" x-show="addOnsTotal > 0">
                <span>Add-on services</span>
                <span x-text="fmt(addOnsTotal)"></span>
            </div>
            <div class="cc-fc-row" x-show="qtyNum > 1">
                <span>Quantity</span>
                <span x-text="'× ' + qtyNum"></span>
            </div>
            <div class="cc-fc-row">
                <span x-text="deliveryLabel"></span>
                <span x-text="fmt(deliveryPrice)"></span>
            </div>
            <div class="cc-fc-row cc-fc-total">
                <span>Total</span>
                <span x-text="fmt(estimateTotal)"></span>
            </div>
            <p style="font-size:10px; color:rgba(18,18,18,0.45); margin-top:8px; line-height:1.4;">
                Rug &amp; add-ons shown per piece × quantity. Delivery charged once. Final price confirmed at checkout.
            </p>
        </div>
    </div>

    {{-- ── HOVER PREVIEW ──
         Enlarged view of whichever image is hovered (main photo, thumbnail or
         rug finish). Sized to ~1/3 of the viewport and pinned to the right so it
         never covers the option controls. Desktop pointers only. --}}
    <div x-show="zoomSrc" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="hidden md:block fixed z-[80] pointer-events-none"
         style="top:50%; right:24px; transform:translateY(-50%); width:33vw; max-width:520px;">
        <div style="background:#fff; border-radius:4px; box-shadow:0 24px 64px rgba(0,0,0,0.28); overflow:hidden;">
            <img :src="zoomSrc" :alt="zoomLabel"
                 style="display:block; width:100%; height:auto; max-height:70vh; object-fit:contain; background:#f5f3f0;">
            <p x-show="zoomLabel" x-text="zoomLabel"
               style="margin:0; padding:10px 14px; font-family:'Lusitana',serif; font-size:13px; color:#121212;
                      border-top:1px solid rgba(18,18,18,0.08);"></p>
        </div>
    </div>

</div>{{-- /x-data --}}

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Product",
    "name": "{{ $product->name }}",
    "image": "{{ $product->primary_image_url }}",
    "description": "{{ Str::limit(strip_tags($product->description ?? ''), 500) }}",
    "brand": {
        "@@type": "Brand",
        "name": "Costikyan Custom Carpet"
    },
    "offers": {
        "@@type": "Offer",
        "url": "{{ route('shop.show', $product->slug) }}",
        "priceCurrency": "USD",
        "price": "{{ $product->sale_price ?? $product->price }}",
        "availability": "{{ $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder' }}",
        "itemCondition": "https://schema.org/NewCondition"
    },
    "material": "{{ $product->material ?? 'Wool' }}",
    "color": "{{ $product->colors->first()?->color_name ?? '' }}"
}
</script>
@endpush
@endsection
