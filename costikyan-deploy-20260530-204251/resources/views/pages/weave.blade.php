@extends('layouts.site')
@section('title', 'Weave Your Dream Rug From Scratch')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     1. PROJECT INQUIRY — left photo + right form
══════════════════════════════════════════════════════════════ --}}
<section class="bg-white">
<div class="max-w-[1100px] mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-[420px_1fr] gap-10 items-start">

        {{-- ── LEFT: tall photo ── --}}
        <div class="hidden lg:block overflow-hidden" style="border-radius:4px; height:700px;">
            <img src="{{ asset('images/cover.jpg') }}" alt="Custom rug weaving"
                 class="w-full h-full object-cover">
        </div>

        {{-- ── RIGHT: form panel ── --}}
        <div style="background:#fff; border:1px solid rgba(18,18,18,0.1); border-radius:4px; padding:40px 36px;">

            {{-- Badge + heading --}}
            <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#8B6914; text-transform:uppercase;
                         background:#F9F0DC; border-radius:20px; padding:4px 14px; display:inline-block;" class="mb-5">
                WEAVE YOUR DREAM RUG
            </span>
            <h1 style="font-family:'Lusitana',serif; font-size:clamp(26px,3vw,38px); font-weight:700; color:#121212; line-height:1.2;" class="mt-4 mb-2">
                Project Inquiry
            </h1>
            <p style="font-size:14px; color:rgba(18,18,18,0.6);" class="mb-7">
                Fill out the details below to begin your custom rug journey.
            </p>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 mb-5 text-sm rounded">{{ session('success') }}</div>
            @endif

            <form action="{{ route('weave.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- First + Last --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">First name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First name"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last name"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                </div>

                {{-- Email + Phone --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter your Email"
                               class="w-full focus:outline-none" required
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Enter your Number"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                </div>

                {{-- Street Address --}}
                <div>
                    <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Street Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Enter your address"
                           class="w-full focus:outline-none"
                           style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                </div>

                {{-- City + State + ZIP --}}
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="City"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">State</label>
                        <input type="text" name="state" value="{{ old('state') }}" placeholder="State"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">ZIP Code</label>
                        <input type="text" name="zip" value="{{ old('zip') }}" placeholder="ZIP code"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                </div>

                {{-- Upload Image --}}
                <div>
                    <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Upload Image</label>
                    <label class="flex flex-col items-center justify-center cursor-pointer"
                           style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:32px; background:#fafafa;">
                        <svg width="28" height="28" fill="none" stroke="rgba(18,18,18,0.35)" stroke-width="1.5" viewBox="0 0 24 24" class="mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span style="font-size:13px; color:rgba(18,18,18,0.45);">Upload an Image</span>
                        <input type="file" name="image" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Size + Quality --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Size of Rug</label>
                        <input type="text" name="size" value="{{ old('size') }}" placeholder="e.g. 10' x 14'"
                               class="w-full focus:outline-none"
                               style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                    </div>
                    <div>
                        <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Quality of Rug</label>
                        <select name="quality" class="w-full focus:outline-none appearance-none"
                                style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212; background:#fff; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E\"); background-repeat:no-repeat; background-position:right 12px center;">
                            <option value="" disabled selected>Select Quality</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                            <option value="luxury">Luxury</option>
                            <option value="ultra-luxury">Ultra Luxury</option>
                        </select>
                    </div>
                </div>

                {{-- Budget --}}
                <div>
                    <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Budget</label>
                    <input type="text" name="budget" value="{{ old('budget') }}" placeholder="Budget"
                           class="w-full focus:outline-none"
                           style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center text-white transition-colors"
                        style="background:#121212; height:50px; font-family:'Lusitana',serif; font-size:16px; border-radius:3px; margin-top:8px;">
                    Submit
                </button>
            </form>

        </div>{{-- /form panel --}}
    </div>
</div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     2. PROCESS — title + 6-step grid
══════════════════════════════════════════════════════════════ --}}
<section class="bg-white py-16">
    <div class="max-w-[1100px] mx-auto px-6">

        {{-- Section heading --}}
        <div class="text-center mb-12">
            <h2 style="font-family:'Lusitana',serif; font-size:30px; font-weight:700; color:#121212;" class="mb-3">
                Weave Your Dream Rug
            </h2>
            <p style="font-size:14px; color:rgba(18,18,18,0.6); max-width:460px;" class="mx-auto">
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
                'img'   => 'images/cover.jpg',
            ],
            [
                'num'   => '2',
                'title' => 'Select Colors',
                'desc'  => 'Whether chosen from our collection or custom dyed, colors will shape your rug into a palette that feels complete and refined.',
                'img'   => 'images/cover.jpg',
            ],
            [
                'num'   => '3',
                'title' => 'Make Sample',
                'desc'  => 'We prepare a sample of your rug so you can review the composition, and ultimately finalize it.',
                'img'   => 'images/cover.jpg',
            ],
            [
                'num'   => '4',
                'title' => 'Production',
                'desc'  => 'Your design is brought to life by skilled Kashmiri artisans using centuries-old techniques and the finest all-natural materials.',
                'img'   => 'images/cover.jpg',
            ],
            [
                'num'   => '5',
                'title' => 'Finishing Touches',
                'desc'  => 'Takes equal care in the finishing — your rug is carefully washed, trimmed, and styled to ensure ideal size and shape.',
                'img'   => 'images/cover.jpg',
            ],
            [
                'num'   => '6',
                'title' => 'Deliver Rug',
                'desc'  => 'Your rug is handrolled, placed in a tough-yet-white-glove delivery service, and delivered right from the source.',
                'img'   => 'images/cover.jpg',
            ],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
            @foreach($steps as $step)
            <div>
                {{-- Photo card with step number badge --}}
                <div class="relative overflow-hidden mb-4" style="aspect-ratio:4/3; border-radius:2px; background:#f0ece6;">
                    <img src="{{ asset($step['img']) }}" alt="{{ $step['title'] }}"
                         class="w-full h-full object-cover">
                    {{-- Number badge --}}
                    <span class="absolute top-3 left-3 flex items-center justify-center"
                          style="width:28px; height:28px; background:rgba(18,18,18,0.65); border-radius:50%;">
                        <span style="font-size:12px; color:#fff; font-family:'Lusitana',serif; font-weight:700;">{{ $step['num'] }}</span>
                    </span>
                </div>

                {{-- Text --}}
                <h3 style="font-family:'Lusitana',serif; font-size:17px; font-weight:700; color:#121212;" class="mb-1.5">
                    {{ $step['title'] }}
                </h3>
                <p style="font-size:13px; color:rgba(18,18,18,0.65); line-height:1.65;" class="mb-3">
                    {{ $step['desc'] }}
                </p>
                <a href="{{ route('contact') }}"
                   style="font-size:13px; color:#121212; display:inline-flex; align-items:center; gap:6px; border-bottom:1px solid rgba(18,18,18,0.3); padding-bottom:1px;">
                    Get Started
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     3. BOTTOM CTA — full-bleed photo, centered text + button
══════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden flex items-center justify-center" style="min-height:420px;">
    <div class="absolute inset-0">
        <img src="{{ asset('images/cover.jpg') }}" alt="Wool source"
             class="w-full h-full object-cover object-center">
        <div class="absolute inset-0" style="background:rgba(18,18,18,0.52);"></div>
    </div>

    <div class="relative z-10 text-center px-6 py-20 max-w-xl mx-auto">
        <h2 style="font-family:'Lusitana',serif; font-size:clamp(28px,3.5vw,46px); font-weight:700; color:#fff; line-height:1.2;" class="mb-5">
            Weave Your Dream Rug<br>From Scratch
        </h2>
        <p style="font-size:14px; color:rgba(255,255,255,0.82); line-height:1.7;" class="mb-8">
            Start with a consultation. We collaborate with clients to design inspiring, made-entirely for-your-space.
        </p>
        <a href="{{ route('contact') }}"
           class="inline-flex items-center gap-2 text-white transition-colors"
           style="border:1px solid rgba(255,255,255,0.6); padding:11px 28px; font-family:'Lusitana',serif; font-size:14px; border-radius:2px;">
            Expert Consultation
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</section>

@endsection
