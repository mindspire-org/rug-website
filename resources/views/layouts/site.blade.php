<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Costikyan Custom Carpet') | Est. 1886</title>
    <meta name="description" content="@yield('meta_description', 'Costikyan Custom Carpet – handcrafted rugs made to your specifications since 1886.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-white text-stone-900" x-data="{ mobileOpen: false, searchOpen: false }">

{{-- Flash Messages --}}
@if(session('success') || session('error'))
<div class="fixed top-4 right-4 z-50 max-w-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-5 py-3 shadow-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-3 shadow-lg text-sm">
        {{ session('error') }}
    </div>
    @endif
</div>
@endif

{{-- HEADER --}}
<header class="sticky top-0 z-40 bg-[#111111] border-b border-white/10" x-data="{ cartCount: 0 }" x-init="fetch('/cart/count').then(r=>r.json()).then(d=> cartCount = d.count)">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between h-[60px]">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex-shrink-0 flex flex-col">
                {{-- Logo box: orange bar + COSTIKYAN --}}
                <div class="flex items-stretch border border-white/20">
                    <div class="w-[5px] bg-orange-600 flex-shrink-0"></div>
                    <div class="px-2.5 py-[5px] flex items-center gap-0.5">
                        <span class="font-serif font-bold text-white text-[15px] tracking-[0.18em] leading-none">COSTI<span class="text-orange-500">K</span>YAN</span>
                        <sup class="text-white/50 text-[7px] leading-none mt-[-4px]">™</sup>
                    </div>
                </div>
                {{-- Since 1886 below --}}
                <span class="text-[9px] text-stone-500 tracking-[0.2em] uppercase mt-[3px] pl-[6px]">Since 1886</span>
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('shop.index') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Our Collection</a>
                <a href="{{ route('weave') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Weave Your Dream Rug</a>
                <a href="{{ route('about') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">About</a>
                <a href="{{ route('trade') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Trade</a>
            </nav>

            {{-- Right icons --}}
            <div class="flex items-center gap-5">
                {{-- Search --}}
                <button @click="searchOpen = !searchOpen" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                </button>

                {{-- Account --}}
                @auth
                <div class="relative group">
                    <button class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    </button>
                    <div class="absolute top-full right-0 mt-2 w-48 bg-stone-900 border border-stone-700 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="px-4 py-2.5 text-xs text-stone-500 border-b border-stone-700">{{ Auth::user()->name }}</div>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">My Account</a>
                        <a href="{{ route('dashboard.orders') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">My Orders</a>
                        <a href="{{ route('wishlist.index') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">Wishlist</a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-amber-400 hover:bg-stone-800">Admin Panel</a>
                        @endif
                        <div class="border-t border-stone-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                </a>
                @endauth

                {{-- Wishlist --}}
                @auth
                <a href="{{ route('wishlist.index') }}" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a>
                @endauth
                {{-- Show wishlist icon for guests too --}}
                @guest
                <a href="{{ route('login') }}" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a>
                @endguest

                {{-- Cart (hidden to match Figma — only search/heart/person shown) --}}
                {{-- <a href="{{ route('cart.index') }}" class="relative text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-2 -right-2 bg-amber-400 text-stone-900 text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center"></span>
                </a> --}}

                {{-- Mobile menu --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Search bar --}}
    <div x-show="searchOpen" x-cloak class="border-t border-stone-800 bg-stone-900">
        <form action="{{ route('shop.search') }}" method="GET" class="max-w-2xl mx-auto px-4 py-3 flex gap-2">
            <input type="text" name="q" placeholder="Search rugs, styles, materials…" autofocus class="flex-1 bg-stone-800 border border-stone-700 text-white placeholder-stone-500 px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition-colors" required>
            <button type="submit" class="btn-dark px-5 py-2 text-sm">Search</button>
        </form>
    </div>

    {{-- Mobile nav --}}
    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-stone-800 bg-stone-950">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('shop.index') }}" class="block text-sm text-stone-300 hover:text-white">Our Collection</a>
            <a href="{{ route('weave') }}" class="block text-sm text-stone-300 hover:text-white">Weave Your Dream Rug</a>
            <a href="{{ route('services') }}" class="block text-sm text-stone-300 hover:text-white">Services</a>
            <a href="{{ route('about') }}" class="block text-sm text-stone-300 hover:text-white">About</a>
            <a href="{{ route('trade') }}" class="block text-sm text-stone-300 hover:text-white">Trade & Design</a>
            <a href="{{ route('contact') }}" class="block text-sm text-stone-300 hover:text-white">Contact</a>
        </div>
    </div>
</header>

{{-- Page Content --}}
<main>
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="bg-[#1a1a1a] text-stone-400">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-16 pb-12">
        <div class="grid grid-cols-1 md:grid-cols-[280px_1fr_1fr_1fr] gap-12">

            {{-- ── COL 1: Logo + Social + Contact ── --}}
            <div>
                {{-- Large logo box --}}
                <a href="{{ route('home') }}" class="inline-block mb-7">
                    <div class="flex items-stretch border border-white/15 bg-[#232323]" style="min-width:200px">
                        <div class="w-[7px] bg-orange-600 flex-shrink-0"></div>
                        <div class="px-4 py-3 flex items-center gap-1">
                            <span class="font-serif font-bold text-white text-[22px] tracking-[0.18em] leading-none">COSTI<span class="text-orange-500">K</span>YAN</span>
                            <sup class="text-white/40 text-[9px] leading-none" style="margin-top:-6px">™</sup>
                        </div>
                    </div>
                </a>

                {{-- Connect With Us --}}
                <p class="text-[11px] text-stone-500 uppercase tracking-widest mb-3">Connect With Us</p>
                <div class="flex items-center gap-2 mb-7">
                    {{-- Facebook --}}
                    <a href="#" class="w-8 h-8 rounded-full border border-stone-700 flex items-center justify-center text-stone-400 hover:text-white hover:border-stone-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#" class="w-8 h-8 rounded-full border border-stone-700 flex items-center justify-center text-stone-400 hover:text-white hover:border-stone-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    {{-- YouTube --}}
                    <a href="#" class="w-8 h-8 rounded-full border border-stone-700 flex items-center justify-center text-stone-400 hover:text-white hover:border-stone-500 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
                    </a>
                </div>

                {{-- Contact Information --}}
                <p class="text-[11px] text-stone-500 uppercase tracking-widest mb-3">Contact Information</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5 text-[13px] text-stone-400">
                        <svg class="w-3.5 h-3.5 text-stone-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .948.684l1.498 4.493a1 1 0 0 1-.502 1.21l-2.257 1.13a11.042 11.042 0 0 0 5.516 5.516l1.13-2.257a1 1 0 0 1 1.21-.502l4.493 1.498a1 1 0 0 1 .684.949V19a2 2 0 0 1-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>800-247-7847</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-[13px] text-stone-400">
                        <svg class="w-3.5 h-3.5 text-stone-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                        <span>info@costikyancustomcarpet.com</span>
                    </div>
                </div>
            </div>

            {{-- ── COL 2: Create + Support ── --}}
            <div>
                <h4 class="text-white text-[15px] font-medium mb-2">Create</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3 mb-8">
                    <li><a href="{{ route('shop.index') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Explore Our Collection</a></li>
                    <li><a href="{{ route('weave') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Weave Your Dream Rug</a></li>
                </ul>

                <h4 class="text-white text-[15px] font-medium mb-2">Support</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3">
                    <li><a href="{{ route('contact') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Contact</a></li>
                    <li><a href="#" class="text-[13px] text-stone-400 hover:text-white transition-colors">Delivery &amp; Lead Times</a></li>
                    <li><a href="#" class="text-[13px] text-stone-400 hover:text-white transition-colors">Care &amp; Maintenance</a></li>
                </ul>
            </div>

            {{-- ── COL 3: Services ── --}}
            <div>
                <h4 class="text-white text-[15px] font-medium mb-2">Services</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3">
                    <li><a href="{{ route('services') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Visit Our Services Site</a></li>
                </ul>
            </div>

            {{-- ── COL 4: About + Trade ── --}}
            <div>
                <h4 class="text-white text-[15px] font-medium mb-2">About</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3 mb-8">
                    <li><a href="{{ route('about') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Our Story</a></li>
                </ul>

                <h4 class="text-white text-[15px] font-medium mb-2">Trade</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3">
                    <li><a href="{{ route('trade') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Trade &amp; Design</a></li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ── Bottom bar ── --}}
    <div class="border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex flex-col sm:flex-row justify-between items-center gap-3">
            <p class="text-[12px] text-stone-600">© {{ date('Y') }} Costikyan Custom Carpet · Est. 1886</p>
            <div class="flex gap-7">
                <a href="#" class="text-[12px] text-stone-600 hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="text-[12px] text-stone-600 hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
@livewireScripts
</body>
</html>
