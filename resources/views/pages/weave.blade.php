@extends('layouts.site')
@section('title', 'Weave Your Dream Rug From Scratch')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. HERO — full-width dark image with overlay text
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden" style="min-height:520px; background:#121212;">
    <div class="absolute inset-0">
        <img src="{{ asset('images/weave-hero-v2.jpg') }}" alt="Custom rug weaving"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:linear-gradient(to bottom, rgba(18,18,18,0.35) 0%, rgba(18,18,18,0.55) 50%, rgba(18,18,18,0.75) 100%);"></div>
    </div>

    <div class="relative z-10 flex items-center" style="min-height:520px;">
        <div class="max-w-[1200px] mx-auto px-6 w-full py-20">
            <span style="font-size:11px; font-weight:600; letter-spacing:0.14em; color:rgba(255,255,255,0.8); text-transform:uppercase;
                         background:rgba(232,101,26,0.9); border-radius:20px; padding:5px 16px; display:inline-block;" class="mb-5">
                Since 1886
            </span>
            <h1 style="font-family:'Lusitana',serif; font-size:clamp(34px,5vw,58px); font-weight:700; color:#fff; line-height:1.15;" class="mb-5 max-w-[700px]">
                Weave Your Dream Rug<br>From Scratch
            </h1>
            <p style="font-size:16px; color:rgba(255,255,255,0.75); line-height:1.7; max-width:480px;" class="mb-8">
                Every rug starts with a conversation. Share your vision and we'll bring it to life with centuries-old craftsmanship.
            </p>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 text-white transition-all hover:gap-3"
               style="border:1px solid rgba(255,255,255,0.5); padding:13px 32px; font-family:'Lusitana',serif; font-size:15px; border-radius:2px; backdrop-filter:blur(4px); background:rgba(255,255,255,0.05);">
                Start Your Project
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     2. PROCESS — 6-step grid
══════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16" style="border-top:1px solid rgba(18,18,18,0.06);">
    <div class="max-w-[1100px] mx-auto px-6">

        {{-- Section heading --}}
        <div class="text-center mb-12">
            <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#8B6914; text-transform:uppercase;
                         background:#F9F0DC; border-radius:20px; padding:5px 16px; display:inline-block;" class="mb-4">
                Our Process
            </span>
            <h2 style="font-family:'Lusitana',serif; font-size:clamp(26px,3.5vw,36px); font-weight:700; color:#121212; line-height:1.2;" class="mb-3">
                Weave Your Dream Rug
            </h2>
            <p style="font-size:15px; color:rgba(18,18,18,0.55); line-height:1.6; max-width:480px;" class="mx-auto">
                From conception to completion, we go through a refined process to create a rug that's uniquely yours.
            </p>
        </div>

        {{-- 6-step grid --}}
        @php
        $steps = [
            [
                'num'   => '1',
                'title' => 'Design',
                'desc'  => 'Share your vision with us. From existing concepts and inspirations you\'ve collected, we translate your inspirations into a blueprint rug design.',
                'img'   => 'images/home-gen-top.jpg',
            ],
            [
                'num'   => '2',
                'title' => 'Select Colors',
                'desc'  => 'Whether chosen from our collection or custom dyed, colors will shape your rug into a palette that feels complete and refined.',
                'img'   => 'images/home-truly-bottom.jpg',
            ],
            [
                'num'   => '3',
                'title' => 'Make Sample',
                'desc'  => 'We prepare a sample of your rug so you can review the composition, and ultimately finalize it.',
                'img'   => 'images/home-truly-top.jpg',
            ],
            [
                'num'   => '4',
                'title' => 'Production',
                'desc'  => 'Your design is brought to life by skilled Kashmiri artisans using centuries-old techniques and the finest all-natural materials.',
                'img'   => 'images/home-gen-bottom.jpg',
            ],
            [
                'num'   => '5',
                'title' => 'Finishing Touches',
                'desc'  => 'Takes equal care in the finishing — your rug is carefully washed, trimmed, and styled to ensure ideal size and shape.',
                'img'   => 'images/home-finish-top.jpg',
            ],
            [
                'num'   => '6',
                'title' => 'Deliver Rug',
                'desc'  => 'Your rug is handrolled, placed in a tough-yet-white-glove delivery service, and delivered right from the source.',
                'img'   => 'images/weave-step-deliver-v2.jpg',
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @foreach($steps as $step)
            <div class="group">
                {{-- Photo card with step number badge --}}
                <div class="relative overflow-hidden mb-4" style="aspect-ratio:4/3; border-radius:6px; background:#f0ece6;">
                    <img src="{{ asset($step['img']) }}" alt="{{ $step['title'] }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    {{-- Gradient overlay --}}
                    <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(18,18,18,0.3) 0%, transparent 50%);"></div>
                    {{-- Number badge --}}
                    <span class="absolute top-3 left-3 flex items-center justify-center"
                          style="width:32px; height:32px; background:rgba(255,255,255,0.95); border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                        <span style="font-size:14px; color:#121212; font-family:'Lusitana',serif; font-weight:700;">{{ $step['num'] }}</span>
                    </span>
                </div>

                {{-- Text --}}
                <h3 style="font-family:'Lusitana',serif; font-size:18px; font-weight:700; color:#121212;" class="mb-2">
                    {{ $step['title'] }}
                </h3>
                <p style="font-size:14px; color:rgba(18,18,18,0.6); line-height:1.7;" class="mb-3">
                    {{ $step['desc'] }}
                </p>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     4. BOTTOM CTA — full-bleed photo, centered text + button
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden flex items-center justify-center" style="min-height:420px;">
    <div class="absolute inset-0">
        <img src="{{ asset('images/weave-hero-v2.jpg') }}" alt="Wool source"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:rgba(18,18,18,0.58);"></div>
    </div>

    <div class="relative z-10 text-center px-6 py-20 max-w-xl mx-auto">
        <h2 style="font-family:'Lusitana',serif; font-size:clamp(28px,3.5vw,46px); font-weight:700; color:#fff; line-height:1.2;" class="mb-5">
            Weave Your Dream Rug<br>From Scratch
        </h2>
        <p style="font-size:15px; color:rgba(255,255,255,0.8); line-height:1.7;" class="mb-8">
            Start with a consultation. We collaborate with clients to design inspiring, made-entirely for-your-space.
        </p>
        <a href="{{ route('contact') }}"
           class="inline-flex items-center gap-2 text-white transition-all hover:gap-3"
           style="border:1px solid rgba(255,255,255,0.5); padding:13px 32px; font-family:'Lusitana',serif; font-size:15px; border-radius:2px; backdrop-filter:blur(4px); background:rgba(255,255,255,0.05);">
            Expert Consultation
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

@endsection
