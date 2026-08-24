@extends('layouts.site')
@section('title', 'Weave Your Dream Rug From Scratch')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HERO SECTION — Full width with background image
══════════════════════════════════════════════════════════════ --}}
<section class="relative w-full overflow-hidden" style="height: 420px;">
    {{-- Background: looping video (poster falls back to the photo) --}}
    <video class="absolute inset-0 w-full h-full object-cover object-center" autoplay muted loop playsinline
           poster="{{ asset('images/weave-hero.png') }}">
        <source src="{{ asset('videos/loop-video.mp4') }}" type="video/mp4">
        <img src="{{ asset('images/weave-hero.png') }}" alt="Custom rug weaving" class="absolute inset-0 w-full h-full object-cover object-center">
    </video>

    {{-- Subtle dark overlay only at the bottom --}}
    <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(0,0,0,0.58) 0%, rgba(0,0,0,0.18) 50%, rgba(0,0,0,0) 100%);"></div>

    {{-- Content pinned to bottom --}}
    <div class="absolute bottom-0 left-0 right-0 px-8 pb-8">
        <div class="flex items-end justify-between gap-8">

            {{-- Left: Heading --}}
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(28px,3.5vw,44px); font-weight:700; line-height:1.15; color:#ffffff; flex-shrink:0;">
                Create Your Dream<br>Rug From Scratch.
            </h1>

            {{-- Right: Description --}}
            <p style="font-size:14px; color:rgba(255,255,255,0.88); line-height:1.7; max-width:320px; text-align:left; flex-shrink:0;">
                Through a thoughtful, guided process, we help you weave your dream rug – from concept to completion. Timeline is 3-6 months.
            </p>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     PROCESS SECTION — 6-step grid
══════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-20">
    <div class="max-w-[1200px] mx-auto px-6">

        {{-- Section heading --}}
        <div class="text-center mb-16">
            <h2 style="font-family:'Playfair Display',serif; font-size:clamp(28px,3vw,36px); font-weight:700; color:#111;" class="mb-4">
                Weave Your Dream Rug
            </h2>
            <p class="text-gray-600 max-w-xl mx-auto text-base">
                From conception to completion, we go through a refined process to create a rug that's uniquely yours.
            </p>
        </div>

        {{-- 6-step grid --}}
        @php
        $steps = [
            [
                'num'   => '1',
                'title' => 'Design',
                'desc'  => "Share your vision with us. From existing concepts and inspirations you've collected, we translate your inspirations into a blueprint rug design.",
                'img'   => 'images/Frame 13.png',
            ],
            [
                'num'   => '2',
                'title' => 'Select Colors',
                'desc'  => "Whether chosen from our collection or custom dyed, colors will shape your rug into a palette that feels complete and refined.",
                'img'   => 'images/Frame 13.1.png',
            ],
            [
                'num'   => '3',
                'title' => 'Make Sample',
                'desc'  => "We prepare a sample of your rug so you can review the composition, and ultimately finalize it.",
                'img'   => 'images/Frame 14.png',
            ],
            [
                'num'   => '4',
                'title' => 'Production',
                'desc'  => "Your design is brought to life by skilled Kashmiri artisans using centuries-old techniques and the finest all-natural materials.",
                'img'   => 'images/Frame 15.png',
            ],
            [
                'num'   => '5',
                'title' => 'Finishing Touches',
                'desc'  => "Takes equal care in the finishing — your rug is carefully washed, trimmed, and styled to ensure ideal size and shape.",
                'img'   => 'images/Frame 16.png',
            ],
            [
                'num'   => '6',
                'title' => 'Deliver Rug',
                'desc'  => "Your rug is hand-rolled, carefully packaged, and delivered directly from the source through a white-glove delivery service.",
                'img'   => 'images/Frame 17.png',
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($steps as $step)
            <div class="group">
                {{-- Photo card with step number badge --}}
                <div class="relative overflow-hidden mb-5 aspect-[4/3] bg-gray-100">
                    <img src="{{ asset($step['img']) }}" alt="{{ $step['title'] }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    {{-- Number badge --}}
                    <span class="absolute top-4 left-4 w-10 h-10 bg-white/90 backdrop-blur-sm flex items-center justify-center text-lg font-bold text-gray-800 shadow-sm">
                        {{ $step['num'] }}
                    </span>
                </div>

                {{-- Text --}}
                <h3 class="text-xl font-semibold text-gray-900 mb-3" style="font-family:'Playfair Display',serif;">
                    {{ $step['title'] }}
                </h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                    {{ $step['desc'] }}
                </p>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 hover:text-orange-600 transition-colors border-b border-gray-900/30 hover:border-orange-600 pb-0.5">
                    Get Started
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     BOTTOM CTA — Full-bleed photo, centered text + button
══════════════════════════════════════════════════════════════ --}}
<section class="relative min-h-[450px] flex items-center justify-center overflow-hidden mb-20 md:mb-0">
    <div class="absolute inset-0">
        <img src="{{ asset('images/weave-deliver.png') }}" alt="Wool source"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <div class="relative z-10 text-center px-6 py-16 max-w-2xl mx-auto">
        <h2 class="mb-6" style="font-family:'Playfair Display',serif; font-size:clamp(28px,4vw,40px); font-weight:700; line-height:1.3; color:#ffffff;">
            Weave Your Dream Rug<br>From Scratch
        </h2>
        <p class="text-base mb-8 leading-relaxed" style="color:rgba(255,255,255,0.9);">
            Start with a blank canvas and collaborate with artisans to design a rug made entirely for your space.
        </p>
        <a href="{{ route('contact') }}"
           class="inline-flex items-center gap-3 bg-white text-gray-900 hover:bg-orange-500 hover:text-white transition-all px-8 py-3.5 font-medium text-sm rounded-sm">
            Begin Customizing
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

@endsection
