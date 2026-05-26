@extends('layouts.site')
@section('title', $product->name)

@php
    // Determine product type for conditional sections
    $isInStock     = $product->stock > 0 && !$product->is_new_arrival;
    $isCustomSize  = $product->featured && !$product->is_bestseller && !$product->is_new_arrival;
    $isMadeToOrder = $product->is_new_arrival || ($product->stock == 0 && !$product->featured);

    // Normalize: if none matched, fall back to is_bestseller = in stock
    if (!$isInStock && !$isCustomSize && !$isMadeToOrder) {
        $isInStock = true;
    }

    // Badge label
    if ($product->is_bestseller)    $badgeLabel = 'BEST SELLER';
    elseif ($product->is_new_arrival) $badgeLabel = 'NEW ARRIVAL';
    elseif ($product->featured)     $badgeLabel = 'SIGNATURE';
    else                            $badgeLabel = 'BEST SELLER';

    // Images array
    $images = $product->images->count() ? $product->images : collect();
    $primaryImg = $product->primary_image_url;

    // Rug finish options (Custom Size only)
    $rugFinishes = [
        ['name' => 'Machine Narrow Binding', 'desc' => 'A machine-applied fabric binding for a consistent and clean finishing edge.', 'img' => null],
        ['name' => 'Machine Surge',          'desc' => 'A straight stitch with a continuous series of interlocked stitches for a durable and consistent finish.', 'img' => null],
        ['name' => 'Custom Wide Bind',       'desc' => 'A wide fabric binding customized to your needs for a bold, decorative edge.', 'img' => null],
        ['name' => 'Hand Surge',             'desc' => 'The rug edge is finished by hand with a fabric binding for a tailored look.', 'img' => null],
    ];
@endphp

@section('content')
<div class="bg-white" x-data="{
    imgIdx: 0,
    images: {{ json_encode($images->map(fn($i) => asset('storage/'.$i->path))->toArray() ?: [$primaryImg]) }},
    selectedSize: '6x9',
    selectedColor: '{{ $product->colors->first()?->color_name ?? '' }}',
    selectedFinish: 'Machine Narrow Binding',
    qty: 1,
    addOns: { protector: true, padding: true, spot: true },
    delivery: 'whiteglove',
    showZip: false,
    zip: '',
    get currentImg() { return this.images[this.imgIdx] ?? '{{ $primaryImg }}'; },
    prev() { this.imgIdx = (this.imgIdx - 1 + this.images.length) % this.images.length; },
    next() { this.imgIdx = (this.imgIdx + 1) % this.images.length; },
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
                <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; line-height:1.3; color:#121212;" class="mb-1">
                    {{ $product->name }}
                </h1>
                <p style="font-size:12px; color:rgba(18,18,18,0.6);" class="mb-0.5">
                    Construction: {{ $product->material ?? 'Hand-knotted' }}
                </p>
                <p style="font-size:12px; color:rgba(18,18,18,0.6);" class="mb-4">
                    Material: {{ $product->material ?? '100% New Zealand Wool' }}
                </p>

                {{-- Price --}}
                <p style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212;" class="mb-3">
                    From ${{ number_format($product->sale_price ?? $product->price, 0) }}
                </p>

                {{-- Short description --}}
                @if($product->description)
                <p style="font-size:13px; color:rgba(18,18,18,0.75); line-height:1.6; max-width:420px;" class="mb-6">
                    {{ Str::limit($product->description, 180) }}
                </p>
                @endif

                {{-- ── IMAGE CAROUSEL ── --}}
                <div class="relative overflow-hidden mb-6" style="aspect-ratio:4/3; background:#f5f3f0; border-radius:2px;">
                    <img :src="currentImg" alt="{{ $product->name }}"
                         class="w-full h-full object-cover transition-opacity duration-300">

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
                            :class="{{ $i }} === imgIdx ? 'ring-2 ring-[#121212]' : 'ring-1 ring-stone-200'"
                            class="flex-shrink-0 overflow-hidden"
                            style="width:60px; height:60px; border-radius:2px;">
                        <img src="{{ asset('storage/'.$img->path) }}" class="w-full h-full object-cover" alt="">
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
                                ['Material',       $product->material   ?? 'New Zealand Wool & Silk'],
                                ['Construction',   $product->material   ?? 'Hand-Knotted'],
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
                        <button style="font-size:12px; color:rgba(18,18,18,0.5);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="inline">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col items-center justify-center"
                         style="border:1px dashed rgba(18,18,18,0.2); border-radius:2px; height:140px; background:#fafafa; cursor:pointer;">
                        <svg width="28" height="28" fill="none" stroke="rgba(18,18,18,0.35)" stroke-width="1.5" viewBox="0 0 24 24" class="mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <span style="font-size:11px; color:rgba(18,18,18,0.45); letter-spacing:0.06em; text-transform:uppercase;">Upload Room Photo</span>
                    </div>
                </div>

            </div>{{-- /left --}}

            {{-- ════════════════════════════════
                 RIGHT COLUMN — SUMMARY PANEL
            ════════════════════════════════ --}}
            <div class="lg:sticky lg:top-[72px]">
                {{-- Panel header --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;">Summary</h2>
                    <button class="text-stone-400 hover:text-red-400">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </button>
                </div>

                {{-- ── DIMENSIONS ── --}}
                <div class="mb-5" x-data="{ customOpen: false, wFt:'', wIn:'', hFt:'', hIn:'' }">
                    <p style="font-size:13px; font-weight:600; color:#121212; letter-spacing:0.02em;" class="mb-3">Dimensions</p>

                    {{-- Standard size pills --}}
                    <div style="background:#f5f5f5; border-radius:4px; padding:10px 12px;" class="mb-2">
                        <div class="flex flex-wrap gap-2">
                            @foreach(['6x9','8x10','9x12','10x14','12x15'] as $sz)
                            <button @click="selectedSize = '{{ $sz }}'; customOpen = false"
                                    :class="selectedSize === '{{ $sz }}' ? 'border-[#121212] bg-white font-semibold' : 'border-transparent bg-transparent text-[rgba(18,18,18,0.6)]'"
                                    class="border transition-all"
                                    style="padding:5px 14px; font-size:13px; border-radius:4px; color:#121212;">
                                {{ $sz }}
                            </button>
                            @endforeach
                        </div>

                        {{-- CUSTOM SIZE accordion row --}}
                        <button @click="customOpen = !customOpen; if(customOpen) selectedSize = 'custom'"
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

                {{-- ── RUG FINISH (Custom Size only) ── --}}
                @if($isCustomSize)
                <div class="mb-5">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="mb-3">Rug Finish</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($rugFinishes as $finish)
                        <button @click="selectedFinish = '{{ $finish['name'] }}'"
                                :class="selectedFinish === '{{ $finish['name'] }}' ? 'ring-2 ring-[#121212]' : 'ring-1 ring-[rgba(18,18,18,0.15)]'"
                                class="text-left p-2.5 bg-white transition-all"
                                style="border-radius:3px;">
                            {{-- Placeholder image area --}}
                            <div class="w-full mb-2 bg-stone-100" style="height:70px; border-radius:2px;"></div>
                            <p style="font-size:11px; font-weight:600; color:#121212; line-height:1.3;" class="mb-1">{{ $finish['name'] }}</p>
                            <p style="font-size:10px; color:rgba(18,18,18,0.55); line-height:1.4;">{{ $finish['desc'] }}</p>
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
                                    {{-- Sub-options --}}
                                    <div class="grid grid-cols-2 gap-2 mt-2" x-show="addOns.padding">
                                        <div class="border p-2" style="border-color:rgba(18,18,18,0.12); border-radius:2px;">
                                            <div class="w-full bg-stone-100 mb-1.5" style="height:40px;border-radius:1px;"></div>
                                            <p style="font-size:10px; font-weight:600; color:#121212;">Dry Grip</p>
                                            <p style="font-size:9px; color:rgba(18,18,18,0.5); line-height:1.3;">A thinner rug pad for a lower profile, non-slip finish.</p>
                                        </div>
                                        <div class="border p-2" style="border-color:rgba(18,18,18,0.12); border-radius:2px;">
                                            <div class="w-full bg-stone-100 mb-1.5" style="height:40px;border-radius:1px;"></div>
                                            <p style="font-size:10px; font-weight:600; color:#121212;">Durahold</p>
                                            <p style="font-size:9px; color:rgba(18,18,18,0.5); line-height:1.3;">A thicker, more cushioned rug pad—ideal for added comfort and support.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Spot Kit Cleaner --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" x-model="addOns.spot" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
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
                                <input type="checkbox" x-model="(delivery === 'whiteglove')"
                                       @change="delivery = 'whiteglove'"
                                       class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;" checked>
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
                                <input type="checkbox" class="mt-0.5 w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <p style="font-size:12px; font-weight:600; color:#121212; text-transform:uppercase; letter-spacing:0.06em;">Standard UPS Shipping</p>
                                        <span style="font-size:12px; font-weight:600; color:#121212;">+$500</span>
                                    </div>
                                    <button @click="showZip = !showZip" x-show="!showZip"
                                            class="border px-3 py-1.5 text-xs"
                                            style="border-color:rgba(18,18,18,0.25); border-radius:3px; color:#121212;">
                                        Enter Zip Code
                                    </button>
                                    <input x-show="showZip" x-model="zip" placeholder="Enter ZIP" type="text"
                                           class="border px-3 py-1.5 text-xs w-full focus:outline-none"
                                           style="border-color:rgba(18,18,18,0.25); border-radius:3px;">
                                </div>
                            </div>
                        </div>

                        {{-- Warehouse Pick-up --}}
                        <div class="border" style="border-color:rgba(18,18,18,0.15); border-radius:3px; padding:12px;">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="w-4 h-4 cursor-pointer flex-shrink-0" style="accent-color:#121212;">
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

                <button type="button"
                        class="w-full flex items-center justify-center border transition-colors hover:bg-stone-50 mb-2"
                        style="height:44px; border-color:rgba(18,18,18,0.25); border-radius:3px;
                               font-family:'Lusitana',serif; font-size:14px; color:#121212;">
                    Order Sample
                </button>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button type="button"
                            class="flex items-center justify-center border transition-colors hover:bg-stone-50"
                            style="height:40px; border-color:rgba(18,18,18,0.25); border-radius:3px;
                                   font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                        Email My Estimate
                    </button>
                    <button type="button"
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
        <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;" class="mb-6">You May Also Like</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($related as $relProduct)
                @include('partials.product-card', ['product' => $relProduct])
            @endforeach
        </div>
    </div>
    @endif

</div>{{-- /x-data --}}
@endsection
