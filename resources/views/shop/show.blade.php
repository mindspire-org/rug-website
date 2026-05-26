@extends('layouts.site')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    {{-- Breadcrumb --}}
    <nav class="text-xs text-stone-400 mb-8 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-stone-900">Home</a>
        <span>/</span>
        <a href="{{ route('shop.index') }}" class="hover:text-stone-900">Shop</a>
        @if($product->category)
        <span>/</span>
        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-stone-900">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-stone-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16" x-data="{ selectedImage: '{{ $product->primary_image_url }}', selectedColor: null, selectedSize: null, quantity: 1 }">

        {{-- Left: Image gallery --}}
        <div class="space-y-4">
            <div class="aspect-square bg-stone-100 overflow-hidden">
                <img :src="selectedImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            @if($product->images->count() > 1)
            <div class="grid grid-cols-5 gap-2">
                @foreach($product->images as $img)
                <button @click="selectedImage = '{{ $img->url }}'"
                        :class="selectedImage === '{{ $img->url }}' ? 'ring-2 ring-stone-900' : 'ring-1 ring-stone-200'"
                        class="aspect-square overflow-hidden bg-stone-100">
                    <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Product info --}}
        <div>
            @if($product->category)
            <p class="text-xs uppercase tracking-widest text-stone-400 mb-2">{{ $product->category->name }}</p>
            @endif
            <h1 class="font-serif text-3xl font-bold text-stone-900 mb-3">{{ $product->name }}</h1>

            {{-- Price --}}
            <div class="flex items-center gap-3 mb-6">
                @if($product->sale_price)
                <span class="text-2xl font-semibold text-red-600">${{ number_format($product->sale_price, 0) }}</span>
                <span class="text-lg text-stone-400 line-through">${{ number_format($product->price, 0) }}</span>
                @else
                <span class="text-2xl font-semibold text-stone-900">From ${{ number_format($product->price, 0) }}</span>
                @endif
            </div>

            {{-- Colors --}}
            @if($product->colors->count())
            <div class="mb-6">
                <p class="form-label mb-2">Color: <span class="normal-case font-normal text-stone-500" x-text="selectedColor ?? 'Select'"></span></p>
                <div class="flex gap-2">
                    @foreach($product->colors as $color)
                    <button @click="selectedColor = '{{ $color->color_name }}'"
                            :class="selectedColor === '{{ $color->color_name }}' ? 'color-swatch selected w-7 h-7' : 'color-swatch w-7 h-7'"
                            style="background-color: {{ $color->color_hex }}"
                            title="{{ $color->color_name }}"></button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Sizes --}}
            @if($product->sizes->count())
            <div class="mb-6">
                <p class="form-label mb-2">Size</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($product->sizes as $size)
                    <button @click="selectedSize = '{{ $size->label }}'"
                            :class="selectedSize === '{{ $size->label }}' ? 'border-stone-900 bg-stone-900 text-white' : 'border-stone-300 text-stone-700 hover:border-stone-900'"
                            class="border px-4 py-2 text-sm transition-colors">{{ $size->label }}</button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity --}}
            <div class="mb-8">
                <p class="form-label mb-2">Quantity</p>
                <div class="flex items-center border border-stone-300 w-32">
                    <button @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 text-stone-600 hover:bg-stone-50">–</button>
                    <span x-text="quantity" class="flex-1 text-center text-sm font-medium"></span>
                    <button @click="quantity = Math.min(99, quantity + 1)" class="px-3 py-2 text-stone-600 hover:bg-stone-50">+</button>
                </div>
            </div>

            {{-- Add to cart --}}
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" :value="quantity">
                <input type="hidden" name="color" :value="selectedColor">
                <input type="hidden" name="size" :value="selectedSize">
                <button type="submit" class="btn-dark w-full justify-center py-4">
                    Add to Cart
                </button>
            </form>

            {{-- Wishlist --}}
            @auth
            <form action="{{ route('wishlist.toggle') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn-outline-dark w-full justify-center py-3 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Add to Wishlist
                </button>
            </form>
            @endauth

            {{-- Meta details --}}
            <div class="mt-8 border-t border-stone-100 pt-6 space-y-2 text-sm text-stone-600">
                @if($product->material)<p><span class="font-medium text-stone-900">Material:</span> {{ $product->material }}</p>@endif
                @if($product->origin)<p><span class="font-medium text-stone-900">Origin:</span> {{ $product->origin }}</p>@endif
                @if($product->dimensions)<p><span class="font-medium text-stone-900">Dimensions:</span> {{ $product->dimensions }}</p>@endif
            </div>
        </div>
    </div>

    {{-- Tabs: Description / Details / Care --}}
    <div class="mb-16 border-t border-stone-200 pt-10" x-data="{ tab: 'description' }">
        <div class="flex gap-8 mb-8 border-b border-stone-200 pb-4">
            <button @click="tab='description'" :class="tab==='description' ? 'tab-btn active' : 'tab-btn'">Description</button>
            <button @click="tab='details'" :class="tab==='details' ? 'tab-btn active' : 'tab-btn'">Details</button>
            <button @click="tab='care'" :class="tab==='care' ? 'tab-btn active' : 'tab-btn'">Care</button>
        </div>
        <div class="prose max-w-none text-stone-700 text-sm leading-relaxed">
            <div x-show="tab === 'description'">{!! nl2br(e($product->description)) !!}</div>
            <div x-show="tab === 'details'" x-cloak>{!! nl2br(e($product->details ?? 'Details coming soon.')) !!}</div>
            <div x-show="tab === 'care'" x-cloak>{!! nl2br(e($product->care_instructions ?? 'Care instructions coming soon.')) !!}</div>
        </div>
    </div>

    {{-- Related products --}}
    @if($related->count())
    <div>
        <h2 class="section-title mb-8">You May Also Like</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($related as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
