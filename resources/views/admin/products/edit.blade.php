@extends('layouts.admin')
@section('title', 'Edit Product')

@section('admin-content')

{{-- Header --}}
<div class="mb-8">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium transition-colors hover:underline" style="color:#64748b;">Products</a>
        <span class="text-stone-300">/</span>
        <span class="text-sm font-medium" style="color:#0f172a;">Edit</span>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 style="font-family:'Lusitana',serif; font-size:26px; font-weight:700; color:#0f172a;">{{ $product->name }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
               style="border-color:#e5e7eb; color:#374151;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View on site
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form')

    {{-- Sticky action bar --}}
    <div class="sticky bottom-0 left-0 right-0 bg-white/90 backdrop-blur border-t border-stone-200 py-4 mt-8 -mx-6 px-6 z-30">
        <div class="flex items-center justify-between">
            <p class="text-sm text-stone-500">Last updated {{ $product->updated_at->diffForHumans() }}</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.index') }}"
                   class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
                   style="border-color:#e5e7eb; color:#374151;">Cancel</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-white text-sm font-medium transition-all hover:opacity-90"
                        style="background:#E8651A;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
