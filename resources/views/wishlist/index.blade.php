@extends('layouts.site')
@section('title', 'Your Favorite Rugs')

@section('content')
<div class="max-w-[1200px] mx-auto px-6 py-14">

    {{-- ── Hero text ── --}}
    <div class="text-center mb-12">
        <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#8B6914; text-transform:uppercase;
                     background:#F9F0DC; border-radius:20px; padding:4px 14px; display:inline-block;" class="mb-5">
            SAVED RUGS
        </span>
        <h1 style="font-family:'Lusitana',serif; font-size:clamp(32px,4.5vw,52px); font-weight:700; color:#121212; line-height:1.2;" class="mt-4 mb-4">
            Your Favorite Rugs
        </h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.6); line-height:1.7; max-width:520px;" class="mx-auto mb-8">
            Your personal collection of saved rugs, allowing you to review designs, compare details, and confidently plan your next purchase or custom project at your own pace.
        </p>
        <a href="{{ route('shop.index') }}"
           class="inline-flex items-center justify-center border transition-colors hover:bg-stone-900 hover:text-white"
           style="border-color:#121212; border-radius:3px; padding:11px 32px;
                  font-family:'Lusitana',serif; font-size:15px; color:#121212;">
            Explore Collection
        </a>
    </div>

    @if($items->isEmpty())
    {{-- Empty state --}}
    <div class="text-center py-20" style="border:1px solid rgba(18,18,18,0.08); border-radius:4px;">
        <svg class="w-14 h-14 mx-auto mb-5" style="color:rgba(18,18,18,0.15);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <p style="font-size:15px; color:rgba(18,18,18,0.5);" class="mb-6">You haven't saved any rugs yet.</p>
        <a href="{{ route('shop.index') }}"
           class="inline-flex items-center justify-center text-white"
           style="background:#121212; border-radius:3px; padding:11px 32px; font-family:'Lusitana',serif; font-size:14px;">
            Explore Our Collection
        </a>
    </div>
    @else
    {{-- Product grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($items as $item)
            @include('partials.product-card', ['product' => $item->product])
        @endforeach
    </div>
    @endif

</div>
@endsection
