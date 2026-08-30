@extends('layouts.site')
@section('title', 'Contact Us')

@section('content')
<div class="max-w-[1100px] mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-[400px_1fr] gap-10 items-start">

        {{-- ── LEFT: Photo ── --}}
        <div class="hidden lg:block overflow-hidden" style="border-radius:4px; aspect-ratio:3/4;">
            <img src="{{ asset('images/contact.png') }}" alt="Contact Costikyan"
                 class="w-full h-full object-cover">
        </div>

        {{-- ── RIGHT: Form + Info ── --}}
        <div>
            {{-- Badge + heading --}}
            <div class="mb-6">
                <span style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:#8B6914; text-transform:uppercase;
                             background:#F9F0DC; border-radius:20px; padding:4px 12px; display:inline-block;" class="mb-4">
                    CONTACT US
                </span>
                <h1 style="font-family:'Lusitana',serif; font-size:clamp(26px,3.5vw,40px); font-weight:700; color:#121212; line-height:1.2;" class="mt-3 mb-2">
                    Speak With Our Experts
                </h1>
                <p style="font-size:14px; color:rgba(18,18,18,0.6);">
                    From standard orders to bespoke rugs, our specialists are ready to assist.
                </p>
            </div>

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 mb-5 text-sm rounded">{{ session('success') }}</div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- First + Last name --}}
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

                {{-- Message --}}
                <div>
                    <label style="font-size:12px; color:rgba(18,18,18,0.6);" class="block mb-1">Message</label>
                    <textarea name="message" rows="5" placeholder="Enter message"
                              class="w-full focus:outline-none resize-none" required
                              style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:10px 12px; font-size:14px; color:#121212;">{{ old('message') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center text-white transition-colors"
                        style="background:#121212; height:48px; font-family:'Lusitana',serif; font-size:15px; border-radius:3px;">
                    Submit Request
                </button>
            </form>

            {{-- Locations --}}
            <div class="mt-10 pt-8" style="border-top:1px solid rgba(18,18,18,0.1);">
                <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;" class="mb-1">Locations</h2>
                <p style="font-size:13px; color:rgba(18,18,18,0.55);" class="mb-6">From standard orders to bespoke rugs, our specialists are ready to assist.</p>

                <div class="space-y-6">
                    @foreach([
                        ['New York Flagship:', '37-11 48th Avenue, Long Island City, NY 11101', '800-247-7847'],
                        ['Boston Studio:',     '808 Main St, Winchester, MA 01890',             '888-432-1266'],
                    ] as [$loc, $addr, $phone])
                    <div class="pb-6" style="border-bottom:1px solid rgba(18,18,18,0.08);">
                        <p style="font-size:14px; font-weight:600; color:#121212;" class="mb-2">{{ $loc }}</p>
                        <div class="flex items-start gap-2 mb-1.5">
                            <svg width="14" height="14" fill="none" stroke="rgba(18,18,18,0.5)" stroke-width="1.5" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            </svg>
                            <span style="font-size:13px; color:rgba(18,18,18,0.65);">{{ $addr }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg width="14" height="14" fill="none" stroke="rgba(18,18,18,0.5)" stroke-width="1.5" viewBox="0 0 24 24" class="flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .948.684l1.498 4.493a1 1 0 0 1-.502 1.21l-2.257 1.13a11.042 11.042 0 0 0 5.516 5.516l1.13-2.257a1 1 0 0 1 1.21-.502l4.493 1.498a1 1 0 0 1 .684.949V19a2 2 0 0 1-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span style="font-size:13px; color:rgba(18,18,18,0.65);">{{ $phone }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Business Hours --}}
            <div class="mt-8">
                <h2 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#121212;" class="mb-1">Business Hours</h2>
                <p style="font-size:13px; color:rgba(18,18,18,0.55);" class="mb-5">From standard orders to bespoke rugs, our specialists are ready to assist.</p>
                <div class="space-y-2">
                    @foreach([
                        ['clock', 'Monday – Friday: 9:00 am – 5:00 pm'],
                        ['clock', 'Saturday and Sunday: Closed'],
                    ] as [$icon, $text])
                    <div class="flex items-center gap-2">
                        <svg width="14" height="14" fill="none" stroke="rgba(18,18,18,0.5)" stroke-width="1.5" viewBox="0 0 24 24" class="flex-shrink-0">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span style="font-size:13px; color:rgba(18,18,18,0.65);">{{ $text }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /right --}}
    </div>
</div>
@endsection
