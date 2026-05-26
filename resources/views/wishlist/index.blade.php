@extends('layouts.site')
@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="font-serif text-3xl font-bold mb-10">My Wishlist</h1>
    @if($items->isEmpty())
    <div class="text-center py-24 border border-stone-200">
        <svg class="w-16 h-16 text-stone-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <p class="text-stone-500 mb-6">Your wishlist is empty.</p>
        <a href="{{ route('shop.index') }}" class="btn-dark">Explore Our Collection</a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($items as $item)
        <div class="product-card relative">
            <a href="{{ route('shop.show', $item->product->slug) }}" class="block">
                <div class="product-card-img">
                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" loading="lazy">
                </div>
            </a>
            <form action="{{ route('wishlist.remove', $item) }}" method="POST" class="absolute top-2 right-2">
                @csrf @method('DELETE')
                <button type="submit" class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow hover:bg-red-50 transition-colors text-stone-400 hover:text-red-600">✕</button>
            </form>
            <div class="mt-3 flex items-center justify-between gap-2">
                <a href="{{ route('shop.show', $item->product->slug) }}" class="text-sm font-medium text-stone-900 hover:text-stone-600 transition-colors line-clamp-1">{{ $item->product->name }}</a>
                <span class="text-sm text-stone-500 flex-shrink-0">From ${{ number_format($item->product->effective_price, 0) }}</span>
            </div>
            @if($item->product->colors->count())
            <div class="flex gap-1.5 mt-2">
                @foreach($item->product->colors->take(4) as $color)
                <span class="color-swatch" style="background-color: {{ $color->color_hex }}"></span>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
