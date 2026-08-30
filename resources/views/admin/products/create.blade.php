@extends('layouts.admin')
@section('title', 'Add Product')

@section('admin-content')

{{-- Header --}}
<div class="mb-8">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium transition-colors hover:underline" style="color:#64748b;">Products</a>
        <span class="text-stone-300">/</span>
        <span class="text-sm font-medium" style="color:#0f172a;">Add New</span>
    </div>
    <h1 style="font-family:'Lusitana',serif; font-size:26px; font-weight:700; color:#0f172a;">Add Product</h1>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form')

    {{-- Sticky action bar --}}
    <div class="sticky bottom-0 left-0 right-0 bg-white/90 backdrop-blur border-t border-stone-200 py-4 mt-8 -mx-6 px-6 z-30">
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.products.index') }}"
               class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
               style="border-color:#e5e7eb; color:#374151;">Cancel</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-white text-sm font-medium transition-all hover:opacity-90"
                    style="background:#E8651A;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Product
            </button>
        </div>
    </div>
</form>
@endsection
