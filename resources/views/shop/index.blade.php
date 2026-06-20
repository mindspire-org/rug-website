@extends('layouts.site')
@section('title', 'Explore Our Rug Gallery')

@section('content')

{{-- Filter option states — explicit CSS so selected/hover survive the prebuilt Tailwind purge on production --}}
<style>
    #filter-form .cc-fopt { cursor:pointer; transition:all .15s ease; }
    #filter-form .cc-pill:hover { border-color:#121212 !important; background:#f5f5f5; }
    #filter-form .cc-color:hover { box-shadow:0 0 0 1px #fff, 0 0 0 3px rgba(18,18,18,0.4); }
    #filter-form input:checked + .cc-pill { background:#121212 !important; color:#fff !important; border-color:#121212 !important; }
    #filter-form input:checked + .cc-color { box-shadow:0 0 0 2px #fff, 0 0 0 4px #121212; }
    #product-results { transition:opacity .2s ease; }
</style>

{{-- ── PAGE HEADER ── Figma: padding 100px, gap 24px, bg #FFF ── --}}
<div class="bg-white pt-16 pb-10 text-center">
    {{-- Badge: bg #F3E7CF, border-radius 40px, px 16px py 10px ── --}}
    <span class="inline-flex items-center justify-center px-4 py-[10px] mb-6 rounded-[40px] text-[14px] tracking-[0.04em] uppercase"
          style="background:#F3E7CF; font-family:'Lusitana',serif; color:#000000; letter-spacing:0.04em">
        Our Collection
    </span>
    {{-- H2: Lusitana 700, 48px, line-height 52px, color #171717 ── --}}
    <h1 style="font-family:'Lusitana',serif; font-size:48px; line-height:52px; font-weight:700; color:#171717;">
        Explore Our Rug Gallery
    </h1>
</div>

{{-- ── TAB ROW — separated bar with border top+bottom, white bg ── --}}
<div class="bg-white" style="border-top:1px solid rgba(18,18,18,0.08); border-bottom:1px solid rgba(18,18,18,0.08);">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between gap-6" style="height:68px;">

            {{-- Tabs ── --}}
            <div class="flex items-center gap-8">
                @foreach([
                    ['all',         'All'],
                    ['signature',   'Signature Items'],
                    ['bestseller',  'Best Sellers'],
                    ['new',         'New Arrivals'],
                    ['in_stock',    'In Stock'],
                    ['made_to_order','Made to Order'],
                    ['custom_size', 'Custom Size'],
                ] as [$val, $label])
                <a href="{{ route('shop.index', array_merge(request()->except('tab'), ['tab' => $val])) }}"
                   style="font-family:'Lusitana',serif; font-size:16px; line-height:21px; white-space:nowrap;
                          {{ request('tab','all') === $val
                             ? 'font-weight:700; color:#121212;'
                             : 'font-weight:400; color:rgba(18,18,18,0.6);' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            {{-- Sort Dropdown ── --}}
            <form method="GET" action="{{ route('shop.index') }}" class="flex-shrink-0">
                @foreach(request()->except('sort') as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $item)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <div class="relative" style="width:210px">
                    <select name="sort" onchange="if(!window.ccAjaxFilters)this.form.submit()"
                            class="appearance-none w-full bg-white focus:outline-none cursor-pointer pl-[14px] pr-8"
                            style="border:1px solid rgba(18,18,18,0.15); border-radius:4px; height:40px;
                                   font-family:'Lusitana',serif; font-size:15px; color:#121212;">
                        <option value="featured"   {{ request('sort','featured')==='featured'  ?'selected':'' }}>Sort by</option>
                        <option value="price_asc"  {{ request('sort')==='price_asc'            ?'selected':'' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort')==='price_desc'           ?'selected':'' }}>Price: High to Low</option>
                        <option value="newest"     {{ request('sort')==='newest'               ?'selected':'' }}>Newest</option>
                        <option value="name_asc"   {{ request('sort')==='name_asc'             ?'selected':'' }}>Name A–Z</option>
                        <option value="name_desc"  {{ request('sort')==='name_desc'            ?'selected':'' }}>Name Z–A</option>
                    </select>
                    <svg class="pointer-events-none absolute right-[12px] top-1/2 -translate-y-1/2"
                         width="16" height="16" fill="none" stroke="#121212" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── BODY: SIDEBAR + GRID ── --}}
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
    <div class="flex gap-10">

        {{-- ── LEFT SIDEBAR: REFINE ──
             Figma: bg #F9F9F9, border rgba(18,18,18,0.05), shadow, border-radius 4px,
             padding 30px, gap 50px, width 310px ── --}}
        <aside class="hidden md:block flex-shrink-0"
               style="width:310px"
               x-data="{
                   color:false, pattern:false, material:false,
                   room:false, construction:false, size:false,
                   availability:false, budget:false
               }">
            <div style="background:#F9F9F9; border:1px solid rgba(18,18,18,0.05);
                        box-shadow:0px 4px 8px rgba(10,13,18,0.02), 0px 2px 4px -2px rgba(10,13,18,0.02);
                        border-radius:4px; padding:30px;">
                {{-- Robust hover/checked states (independent of the compiled Tailwind build) --}}
                <style>
                    #filter-form label { cursor: pointer; }
                    #filter-form label > span { transition: background-color .15s ease, color .15s ease, border-color .15s ease, box-shadow .15s ease; }
                    /* Pills / size / availability: hover */
                    #filter-form label:hover > span:not([class*="rounded-[6px]"]) { border-color:#121212 !important; background:#f5f3f0; }
                    /* Pills / size / availability: checked */
                    #filter-form input:not([name="color[]"]):checked + span { background:#121212 !important; color:#fff !important; border-color:#121212 !important; }
                    /* Colour swatch: hover + checked rings */
                    #filter-form label:hover input[name="color[]"]:not(:checked) + span { box-shadow:0 0 0 2px #fff, 0 0 0 3px rgba(18,18,18,.4); }
                    #filter-form input[name="color[]"]:checked + span { box-shadow:0 0 0 2px #fff, 0 0 0 3px #121212 !important; }
                    /* Keyboard focus accessibility */
                    #filter-form input:focus-visible + span { outline:2px solid #E8651A; outline-offset:2px; }
                </style>
                <form method="GET" action="{{ route('shop.index') }}" id="filter-form">
                    @if(request('tab'))  <input type="hidden" name="tab"  value="{{ request('tab') }}">  @endif
                    @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                    {{-- Header: "Refine" 32px bold + reset ── --}}
                    <div class="flex items-center justify-between mb-[30px]">
                        <span style="font-family:'Lusitana',serif; font-size:32px; line-height:42px; font-weight:700; color:#171717;">
                            Refine
                        </span>
                        <a href="{{ route('shop.index') }}"
                           class="flex items-center gap-1"
                           style="font-family:'Inter',sans-serif; font-size:14px; color:rgba(18,18,18,0.7); text-transform:uppercase;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                 style="color:rgba(18,18,18,0.7)">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            reset
                        </a>
                    </div>

                    {{-- Filter Sections: label 15px Lusitana uppercase, border-bottom rgba(18,18,18,0.1) ── --}}
                    @php
                    $filterSections = [
                        ['key'=>'color',        'label'=>'REFINED COLOR',          'open'=>'color'],
                        ['key'=>'pattern',      'label'=>'PATTERN / STYLE',        'open'=>'pattern'],
                        ['key'=>'material',     'label'=>'MATERIAL',               'open'=>'material'],
                        ['key'=>'construction', 'label'=>'CONSTRUCTION',           'open'=>'construction'],
                        ['key'=>'size',         'label'=>'SIZE',                   'open'=>'size'],
                        ['key'=>'availability', 'label'=>'AVAILABILITY / TIMELINE','open'=>'availability'],
                        ['key'=>'budget',       'label'=>'BUDGET',                 'open'=>'budget'],
                    ];
                    @endphp

                    <div class="flex flex-col gap-6">
                    @foreach($filterSections as $section)
                    <div style="border-bottom:1px solid rgba(18,18,18,0.1); padding-bottom:20px;">
                        <button type="button"
                                @click="{{ $section['open'] }} = !{{ $section['open'] }}"
                                class="flex items-center justify-between w-full text-left">
                            <span style="font-family:'Lusitana',serif; font-size:15px; line-height:19px;
                                         font-weight:400; text-transform:uppercase; color:#121212;">
                                {{ $section['label'] }}
                            </span>
                            <svg class="transition-transform duration-200 flex-shrink-0"
                                 :class="{{ $section['open'] }} ? 'rotate-180' : ''"
                                 style="width:15px; height:15px;" fill="none" stroke="#121212" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        @php
                        // Defaults (override from admin settings if set)
                        $fo_color = $filterOptions['color'] ?? [
                            ['hex'=>'#D4CFC6','name'=>'Neutrals'],['hex'=>'#3A6EA8','name'=>'Blues'],
                            ['hex'=>'#8B2020','name'=>'Reds'],['hex'=>'#2D5C3A','name'=>'Greens'],
                            ['hex'=>'#B07A4A','name'=>'Warm Tones'],['hex'=>'#5B7B8A','name'=>'Cool Tones'],
                            ['hex'=>'#D4C832','name'=>'Yellow'],
                            ['hex'=>'#2B2B2B','name'=>'Black & Grey'],
                        ];
                        $fo_pattern      = $filterOptions['pattern']      ?? ['Solid','Stripe','Grid','Geometric','Abstract','Classic & Ornate'];
                        $fo_material     = $filterOptions['material']     ?? ['Wool','Wool & Silk','Natural Fibers','Silk','Performance Fibers'];
                        $fo_room         = $filterOptions['room']         ?? ['Living Room','Bedroom','Dining Room','Hallway','Office','Outdoor','Staircase'];
                        $fo_construction = $filterOptions['construction'] ?? ['Hand-Knotted','Hand-Tufted','Flatweave','Machine Made','Hand-Loomed','Hooked'];
                        $fo_size         = $filterOptions['size']         ?? ['6×9','8×10','9×12','10×14','12×15','Custom'];
                        $fo_avail        = $filterOptions['availability'] ?? [
                            ['value'=>'In Stock',      'label'=>'In Stock (2 Weeks)'],
                            ['value'=>'Custom Size',   'label'=>'Custom Size (2-4 weeks)'],
                            ['value'=>'Made to Order', 'label'=>'Made to Order (8-12 weeks)'],
                        ];
                        @endphp

                        <div x-show="{{ $section['open'] }}" x-cloak class="mt-4">

                            @if($section['key'] === 'color')
                                <div class="grid grid-cols-4 gap-x-3 gap-y-4">
                                    @foreach($fo_color as $c)
                                    <label class="cursor-pointer flex flex-col items-center gap-1">
                                        <input type="checkbox" name="color[]" value="{{ $c['name'] }}"
                                               {{ in_array($c['name'],(array)request('color',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-color block rounded-[6px] peer-checked:ring-2 peer-checked:ring-[#121212] peer-checked:ring-offset-1 transition-all"
                                              style="width:52px; height:52px; background-color:{{ $c['hex'] }};
                                                     border:1px solid rgba(18,18,18,0.08);"></span>
                                        <span style="font-family:'Lusitana',serif; font-size:11px; line-height:14px;
                                                     color:#121212; text-align:center;">{{ $c['name'] }}</span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'pattern')
                                <div class="flex flex-wrap gap-2">
                                    @foreach($fo_pattern as $opt)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="pattern[]" value="{{ $opt }}"
                                               {{ in_array($opt,(array)request('pattern',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill inline-block px-3 py-1.5 rounded-full peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25);
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $opt }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'material')
                                <div class="flex flex-wrap gap-2">
                                    @foreach($fo_material as $mat)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="material[]" value="{{ $mat }}"
                                               {{ in_array($mat,(array)request('material',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill inline-block px-3 py-1.5 rounded-full peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25);
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $mat }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'room')
                                <div class="flex flex-wrap gap-2">
                                    @foreach($fo_room as $opt)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="room[]" value="{{ $opt }}"
                                               {{ in_array($opt,(array)request('room',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill inline-block px-3 py-1.5 rounded-full peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25);
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $opt }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'construction')
                                <div class="flex flex-wrap gap-2">
                                    @foreach($fo_construction as $opt)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="construction[]" value="{{ $opt }}"
                                               {{ in_array($opt,(array)request('construction',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill inline-block px-3 py-1.5 rounded-full peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25);
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $opt }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'size')
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($fo_size as $opt)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="size[]" value="{{ $opt }}"
                                               {{ in_array($opt,(array)request('size',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill flex items-center justify-center py-2 peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25); border-radius:4px;
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $opt }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'availability')
                                <div class="flex flex-col gap-2">
                                    @foreach($fo_avail as $a)
                                    <label class="cursor-pointer w-full">
                                        <input type="checkbox" name="availability[]" value="{{ $a['value'] }}"
                                               {{ in_array($a['value'],(array)request('availability',[])) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <span class="cc-fopt cc-pill flex items-center w-full px-3 py-2.5 peer-checked:bg-[#121212] peer-checked:text-white transition-colors"
                                              style="border:1px solid rgba(18,18,18,0.25); border-radius:4px;
                                                     font-family:'Lusitana',serif; font-size:13px; color:#121212;">
                                            {{ $a['label'] }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($section['key'] === 'budget')
                                {{-- Range slider with $0 / $X display ── --}}
                                <div x-data="{
                                        minVal: {{ request('min_price', 0) }},
                                        maxVal: {{ request('max_price', 3100) }},
                                        absMax: 15000
                                     }" class="pt-1">
                                    {{-- Track + thumb ── --}}
                                    <div class="relative h-1 mb-4" style="background:rgba(18,18,18,0.12); border-radius:2px;">
                                        <div class="absolute h-1 rounded"
                                             style="background:#121212;"
                                             :style="`left:${minVal/absMax*100}%; right:${100-maxVal/absMax*100}%`"></div>
                                        <input type="range" name="min_price"
                                               :min="0" :max="absMax" x-model.number="minVal"
                                               class="absolute w-full h-1 appearance-none bg-transparent cursor-pointer"
                                               style="top:0; pointer-events:auto;"
                                               @input="if(minVal > maxVal - 100) minVal = maxVal - 100">
                                        <input type="range" name="max_price"
                                               :min="0" :max="absMax" x-model.number="maxVal"
                                               class="absolute w-full h-1 appearance-none bg-transparent cursor-pointer"
                                               style="top:0; pointer-events:auto;"
                                               @input="if(maxVal < minVal + 100) maxVal = minVal + 100">
                                    </div>
                                    {{-- Labels ── --}}
                                    <div class="flex items-center justify-between gap-2 mt-4">
                                        <span class="flex items-center justify-center px-3 py-1.5"
                                              style="border:1px solid rgba(18,18,18,0.2); border-radius:4px; min-width:72px;
                                                     font-family:'Lusitana',serif; font-size:14px; color:#121212;"
                                              x-text="'$' + minVal.toLocaleString()"></span>
                                        <span style="color:rgba(18,18,18,0.3); font-size:12px;">—</span>
                                        <span class="flex items-center justify-center px-3 py-1.5"
                                              style="border:1px solid rgba(18,18,18,0.2); border-radius:4px; min-width:72px;
                                                     font-family:'Lusitana',serif; font-size:14px; color:#121212;"
                                              x-text="'$' + maxVal.toLocaleString()"></span>
                                    </div>
                                    <button type="submit" class="w-full py-2 text-white mt-3"
                                            style="background:#121212; font-family:'Lusitana',serif; font-size:14px; border-radius:4px;">
                                        Apply
                                    </button>
                                </div>

                            @endif
                        </div>
                    </div>
                    @endforeach
                    </div>

                </form>

                {{-- Auto-apply filters via AJAX — no full page refresh (#1) --}}
                <script>
                (function () {
                    var ff = document.getElementById('filter-form');
                    var results = document.getElementById('product-results');
                    if (!ff || !results || !window.fetch || !window.history.pushState) {
                        // Fallback: full-submit on checkbox change
                        if (ff) ff.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
                            cb.addEventListener('change', function () { ff.submit(); });
                        });
                        return;
                    }

                    window.ccAjaxFilters = true;
                    var base = @json(route('shop.index'));

                    function buildUrl() {
                        var params = new URLSearchParams(new FormData(ff));
                        var sortSel = document.querySelector('select[name=sort]');
                        if (sortSel) params.set('sort', sortSel.value);
                        var cur = new URLSearchParams(window.location.search);
                        ['search', 'category', 'tab'].forEach(function (k) {
                            if (cur.get(k) && !params.get(k)) params.set(k, cur.get(k));
                        });
                        return base + '?' + params.toString();
                    }

                    function apply(url) {
                        url = url || buildUrl();
                        results.style.opacity = '0.45';
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (r) { return r.text(); })
                            .then(function (html) {
                                var doc = new DOMParser().parseFromString(html, 'text/html');
                                var fresh = doc.getElementById('product-results');
                                if (fresh) results.innerHTML = fresh.innerHTML;
                                results.style.opacity = '1';
                                window.history.pushState({}, '', url);
                                var top = results.getBoundingClientRect().top + window.pageYOffset - 90;
                                window.scrollTo({ top: top, behavior: 'smooth' });
                            })
                            .catch(function () { window.location = url; });
                    }

                    ff.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
                        cb.addEventListener('change', function () { apply(); });
                    });
                    var sortSel = document.querySelector('select[name=sort]');
                    if (sortSel) sortSel.addEventListener('change', function () { apply(); });
                    // Price range "Apply" (and any submit) → AJAX
                    ff.addEventListener('submit', function (e) { e.preventDefault(); apply(); });
                    // Pagination links inside the results → AJAX (product links have no page=)
                    results.addEventListener('click', function (e) {
                        var a = e.target.closest('a[href*="page="]');
                        if (a) { e.preventDefault(); apply(a.href); }
                    });
                    window.addEventListener('popstate', function () { apply(window.location.href); });
                })();
                </script>
            </div>
        </aside>

        {{-- ── PRODUCT GRID ── Figma: 3 cols, gap 24px, card w 314px ── --}}
        <div class="flex-1 min-w-0" id="product-results" style="transition:opacity .2s ease;">
            @if($products->count())
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-12">{{ $products->links() }}</div>
            @else
            <div class="flex flex-col items-center justify-center py-32 text-center">
                <svg class="w-14 h-14 text-stone-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <p style="font-family:'Lusitana',serif; font-size:18px; color:#121212;" class="mb-3">No products found</p>
                <a href="{{ route('shop.index') }}"
                   style="font-family:'Lusitana',serif; font-size:14px; color:rgba(18,18,18,0.7);"
                   class="underline hover:text-stone-900">Clear filters</a>
            </div>
            @endif
        </div>

    </div>
</div>

@endsection
