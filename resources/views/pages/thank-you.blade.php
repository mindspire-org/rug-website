@extends('layouts.site')
@section('title', 'Thank You')

@section('content')
<div class="max-w-xl mx-auto px-6 py-24 text-center">
    <div class="w-16 h-16 mx-auto mb-6 rounded-full flex items-center justify-center" style="background:#fef3c7;">
        <svg class="w-8 h-8" style="color:#E8651A;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 style="font-family:'Lusitana',serif; font-size:34px; font-weight:700; color:#121212;" class="mb-3">Thank You</h1>
    <p style="font-size:15px; color:rgba(18,18,18,0.6); line-height:1.7;" class="mb-8">
        Your message has been received. A member of our team will be in touch shortly. We appreciate your interest in Costikyan Custom Carpet.
    </p>
    <div class="flex items-center justify-center gap-3">
        <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-white text-sm font-medium px-6 py-3" style="background:#121212; border-radius:3px;">
            Explore the Collection
        </a>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium px-6 py-3" style="border:1px solid rgba(18,18,18,0.2); color:#121212; border-radius:3px;">
            Back to Home
        </a>
    </div>
</div>
@endsection
