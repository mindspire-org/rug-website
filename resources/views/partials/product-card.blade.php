@php
    $imgUrl        = $product->primary_image_url;
    $isPlaceholder = str_contains($imgUrl, 'placeholder');
    $c1            = $product->colors->first()?->color_hex ?? '#d4c5b0';
    $c2            = $product->colors->last()?->color_hex  ?? '#a08060';

    if ($product->is_new_arrival)    $badgeLabel = 'New Arrivals';
    elseif ($product->is_bestseller) $badgeLabel = 'In Stock';
    elseif ($product->sale_price)    $badgeLabel = 'Sale';
    else                             $badgeLabel = 'Custom Size';
@endphp

{{-- Card: gap 10px between image and meta ── --}}
<div class="group flex flex-col gap-[10px]">

    {{-- ── IMAGE: border-radius 4px, shadow, aspect 3:4 ── --}}
    <a href="{{ route('shop.show', $product->slug) }}"
       class="relative block overflow-hidden flex-shrink-0"
       style="aspect-ratio:3/4; border-radius:4px;
              box-shadow:0px 4px 8px rgba(10,13,18,0.02), 0px 2px 4px -2px rgba(10,13,18,0.02);
              background:{{ $c1 }}22;">

        {{-- Photo or gradient placeholder ── --}}
        @if($isPlaceholder)
        <div class="absolute inset-0 flex items-center justify-center"
             style="background:linear-gradient(145deg, {{ $c1 }}30 0%, {{ $c2 }}45 100%)">
            <svg class="w-20 h-20 opacity-20" viewBox="0 0 100 100" fill="none" stroke="#888" stroke-width="1.5">
                <rect x="8" y="8" width="84" height="84"/><rect x="18" y="18" width="64" height="64" stroke-width="1"/>
                <circle cx="50" cy="50" r="10" stroke-width="0.8"/>
            </svg>
        </div>
        @else
        <img src="{{ $imgUrl }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
             loading="lazy">
        @endif

        {{-- Gradient overlay: linear-gradient(0deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%) ── --}}
        <div class="absolute inset-0 pointer-events-none"
             style="background:linear-gradient(0deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%)"></div>

        {{-- Tags row: badge left + heart right, padding 14px ── --}}
        <div class="absolute top-[14px] left-[14px] right-[14px] flex items-center justify-between">

            {{-- Pill badge: rgba(255,255,255,0.1) bg, 1px white border, backdrop-blur(5px), border-radius 37px ── --}}
            <span style="background:rgba(255,255,255,0.1); border:1px solid #FFFFFF;
                         backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px);
                         border-radius:37px; padding:8px 14px;
                         font-family:'Lusitana',serif; font-size:14px; line-height:14px; color:#FFFFFF;">
                {{ $badgeLabel }}
            </span>

            {{-- Heart: backdrop-blur(4px), border-radius 37px, 28×28 ── --}}
            @auth
            <form action="{{ route('wishlist.toggle') }}" method="POST" class="flex-shrink-0">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                        class="flex items-center justify-center"
                        style="width:28px;height:28px; backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);
                               border-radius:37px;">
                    <svg width="23" height="20" fill="none" stroke="#FFFFFF" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}"
               class="flex items-center justify-center flex-shrink-0"
               style="width:28px;height:28px; backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);
                      border-radius:37px;">
                <svg width="23" height="20" fill="none" stroke="#FFFFFF" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </a>
            @endauth
        </div>

    </a>

    {{-- ── META: padding-top 6px ── --}}
    <a href="{{ route('shop.show', $product->slug) }}" class="block pt-[6px]">

        {{-- Name + Price on same row: name 20px Lusitana bold, price 18px regular, both #171717 ── --}}
        <div class="flex items-start justify-between gap-2">
            <p style="font-family:'Lusitana',serif; font-size:20px; line-height:26px; font-weight:700;
                      color:#171717; text-transform:capitalize;"
               class="flex-1 min-w-0 truncate">
                {{ $product->name }}
            </p>
            <span style="font-family:'Lusitana',serif; font-size:18px; line-height:26px; font-weight:400;
                         color:#171717; white-space:nowrap; flex-shrink:0;">
                From ${{ number_format($product->sale_price ?? $product->price, 0) }}
            </span>
        </div>

        {{-- Color swatches: 24px circles, exact shadow from spec ── --}}
        @if($product->colors->count())
        <div class="flex gap-2 mt-2">
            @foreach($product->colors->take(4) as $color)
            <span class="rounded-full flex-shrink-0"
                  style="width:24px;height:24px;background-color:{{ $color->color_hex }};
                         border:1px solid rgba(23,23,23,0.1);
                         box-shadow:0px 4px 8px rgba(10,13,18,0.1),0px 2px 4px -2px rgba(10,13,18,0.06);"
                  title="{{ $color->color_name }}"></span>
            @endforeach
            @if($product->colors->count() > 4)
            <span style="font-size:11px; color:rgba(18,18,18,0.5);" class="self-center">
                +{{ $product->colors->count() - 4 }}
            </span>
            @endif
        </div>
        @endif

    </a>

</div>
