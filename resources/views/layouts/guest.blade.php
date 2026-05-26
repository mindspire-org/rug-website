<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Costikyan') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased" style="font-family:'Inter',sans-serif;">

<div class="flex min-h-screen">

    {{-- ══ LEFT PANEL: Dark brand ══ --}}
    <div class="hidden lg:flex lg:w-[480px] xl:w-[540px] flex-shrink-0 flex-col relative overflow-hidden"
         style="background:#111111;">

        {{-- Background texture overlay --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image:repeating-linear-gradient(45deg,transparent,transparent 40px,rgba(255,255,255,0.03) 40px,rgba(255,255,255,0.03) 80px);"></div>

        {{-- Accent top bar --}}
        <div class="absolute top-0 left-0 right-0 h-[3px]"
             style="background:linear-gradient(90deg,#E8651A,#EDB84A,#E8651A);"></div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col h-full px-12 py-12">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-stretch w-fit border border-white/20 mb-auto">
                <div class="w-[6px] flex-shrink-0" style="background:#E8651A;"></div>
                <div class="px-3 py-2 flex items-center gap-1">
                    <span style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#fff; letter-spacing:0.18em;">COSTI<span style="color:#E8651A;">K</span>YAN</span>
                    <sup style="font-size:7px; color:rgba(255,255,255,0.4); margin-top:-5px;">™</sup>
                </div>
            </a>

            {{-- Middle quote section --}}
            <div class="py-16">
                <div class="w-10 h-[2px] mb-8" style="background:#E8651A;"></div>
                <blockquote style="font-family:'Lusitana',serif; font-size:clamp(22px,2.5vw,32px); font-weight:700; color:#fff; line-height:1.35;">
                    "Every thread tells a story. Yours starts here."
                </blockquote>
                <p class="mt-6" style="font-size:14px; color:rgba(255,255,255,0.45); line-height:1.7;">
                    Handcrafted custom rugs since 1886. Trusted by designers,<br>architects, and homeowners across the world.
                </p>
            </div>

            {{-- Decorative rug pattern --}}
            <div class="relative h-[220px] overflow-hidden rounded-sm mb-8 opacity-60">
                <img src="{{ asset('images/cover.jpg') }}" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0" style="background:linear-gradient(to top,#111 0%,transparent 60%);"></div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between">
                <p style="font-size:11px; color:rgba(255,255,255,0.25); letter-spacing:0.15em; text-transform:uppercase;">Est. 1886 · New York</p>
                <a href="{{ route('home') }}" style="font-size:11px; color:rgba(255,255,255,0.35); letter-spacing:0.1em; text-transform:uppercase;" class="hover:text-white transition-colors">← Back to site</a>
            </div>
        </div>
    </div>

    {{-- ══ RIGHT PANEL: Form ══ --}}
    <div class="flex-1 flex flex-col bg-white">
        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center justify-between px-6 py-5 border-b border-stone-100">
            <a href="{{ route('home') }}" class="flex items-stretch border border-stone-200">
                <div class="w-[5px]" style="background:#E8651A;"></div>
                <div class="px-2.5 py-1.5 flex items-center">
                    <span style="font-family:'Lusitana',serif; font-size:14px; font-weight:700; color:#111; letter-spacing:0.18em;">COSTI<span style="color:#E8651A;">K</span>YAN</span>
                </div>
            </a>
            <a href="{{ route('home') }}" style="font-size:12px; color:#6b7280;">← Back to site</a>
        </div>

        <div class="flex-1 flex items-center justify-center px-6 sm:px-12 py-12">
            <div class="w-full max-w-[420px]">
                {{ $slot }}
            </div>
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>
