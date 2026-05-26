@extends('layouts.site')
@section('title', 'Search: ' . $q)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="font-serif text-3xl font-bold mb-2">Search Results</h1>
    <p class="text-stone-500 text-sm mb-8">{{ $products->total() }} results for "<strong>{{ $q }}</strong>"</p>
    @if($products->count())
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($products as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-12">{{ $products->links() }}</div>
    @else
    <div class="text-center py-24">
        <p class="text-stone-500 mb-4">No products match your search.</p>
        <a href="{{ route('shop.index') }}" class="btn-dark">Browse All Products</a>
    </div>
    @endif
</div>
@endsection
