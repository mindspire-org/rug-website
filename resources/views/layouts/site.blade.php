<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Costikyan Custom Carpet') | Est. 1886</title>
    <meta name="description" content="@yield('meta_description', 'Costikyan Custom Carpet – handcrafted rugs made to your specifications since 1886.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Costikyan Custom Carpet') | Est. 1886">
    <meta property="og:description" content="@yield('meta_description', 'Costikyan Custom Carpet – handcrafted rugs made to your specifications since 1886.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/cover.jpg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Costikyan Custom Carpet') | Est. 1886">
    <meta name="twitter:description" content="@yield('meta_description', 'Costikyan Custom Carpet – handcrafted rugs made to your specifications since 1886.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/cover.jpg'))">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-white text-stone-900 pb-[64px] md:pb-0" x-data="{ mobileOpen: false, searchOpen: false }">

{{-- ══════════════════════════════════════════
     NEWSLETTER POPUP — shows once per session
══════════════════════════════════════════ --}}
<div
    x-data="{
        open: false,
        email: '',
        phone: '',
        agreed: false,
        submitted: false,
        init() {
            if (!localStorage.getItem('cc_popup_dismissed')) {
                setTimeout(() => { this.open = true; }, 800);
            }
        },
        dismiss() {
            this.open = false;
            localStorage.setItem('cc_popup_dismissed', '1');
        },
        submit() {
            if (!this.email || !this.agreed) return;
            this.submitted = true;
            setTimeout(() => { this.dismiss(); }, 1800);
        }
    }"
    x-cloak
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    style="background:rgba(0,0,0,0.45);"
    @click.self="dismiss()">

    {{-- Modal card --}}
    <div class="relative flex overflow-hidden w-full mx-4 sm:mx-0"
         style="width:min(940px,95vw); max-height:92vh; background:#fff; border-radius:4px; box-shadow:0 24px 64px rgba(0,0,0,0.22);"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        {{-- ── LEFT: room photo + quote ── --}}
        <div class="relative hidden sm:block flex-shrink-0" style="width:50%;">
            <img src="{{ asset('images/cover.jpg') }}" alt="Luxury room"
                 class="w-full h-full object-cover">
            {{-- Dark gradient at bottom for quote legibility --}}
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(0,0,0,0.55) 0%, transparent 55%);"></div>
            {{-- Quote --}}
            <div class="absolute bottom-0 left-0 right-0 px-6 py-5">
                <p style="font-family:'Lusitana',serif; font-size:15px; font-style:italic; color:#fff; line-height:1.5;">
                    "Quality is the only thing that endures."
                </p>
            </div>
        </div>

        {{-- ── RIGHT: form ── --}}
        <div class="flex-1 flex flex-col justify-center px-6 sm:px-10 py-8 sm:py-12 overflow-y-auto" style="min-height:0;">

            {{-- Close button --}}
            <button @click="dismiss()"
                    class="absolute top-4 right-4 text-stone-400 hover:text-stone-900 transition-colors"
                    style="line-height:1;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>

            {{-- Success state --}}
            <div x-show="submitted" class="text-center">
                <svg class="w-12 h-12 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
                <h3 style="font-family:'Lusitana',serif; font-size:22px; color:#121212;" class="mb-2">Welcome!</h3>
                <p style="font-size:14px; color:rgba(18,18,18,0.6);">You're now part of our inner circle.</p>
            </div>

            {{-- Form state --}}
            <div x-show="!submitted">
                <h2 style="font-family:'Lusitana',serif; font-size:clamp(28px,3.5vw,42px); font-weight:700; color:#121212; line-height:1.15;" class="mb-4">
                    Exclusive Updates
                </h2>
                <p style="font-size:14px; color:rgba(18,18,18,0.65); line-height:1.65; max-width:340px;" class="mb-7">
                    Join our inner circle for exclusive releases and members-only offers.
                </p>

                {{-- Email --}}
                <div class="mb-4">
                    <label style="font-size:13px; font-weight:500; color:#121212;" class="block mb-1.5">Email</label>
                    <input x-model="email" type="email" placeholder="Enter your Email"
                           class="w-full focus:outline-none"
                           style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:12px 14px; font-size:14px; color:#121212; background:#fff;"
                           :style="!email && submitted ? 'border-color:#e53e3e;' : ''">
                </div>

                {{-- Phone --}}
                <div class="mb-5">
                    <label style="font-size:13px; font-weight:500; color:#121212;" class="block mb-1.5">Phone</label>
                    <input x-model="phone" type="tel" placeholder="Enter your Number"
                           class="w-full focus:outline-none"
                           style="border:1px solid rgba(18,18,18,0.2); border-radius:3px; padding:12px 14px; font-size:14px; color:#121212; background:#fff;">
                </div>

                {{-- T&C checkbox --}}
                <label class="flex items-start gap-3 cursor-pointer mb-7">
                    <input type="checkbox" x-model="agreed" class="mt-0.5 flex-shrink-0" style="width:16px; height:16px; accent-color:#121212; cursor:pointer;">
                    <span style="font-size:13px; color:rgba(18,18,18,0.7); line-height:1.5;">
                        By signing up, you agree to our
                        <a href="#" style="color:#121212; font-weight:600; text-decoration:none; border-bottom:1px solid rgba(18,18,18,0.3);">Terms &amp; Conditions</a>
                    </span>
                </label>

                {{-- CTA --}}
                <button @click="submit()"
                        :disabled="!email || !agreed"
                        class="w-full flex items-center justify-center text-white transition-colors"
                        style="background:#121212; height:52px; font-family:'Lusitana',serif; font-size:16px; border-radius:3px; cursor:pointer;"
                        :style="(!email || !agreed) ? 'opacity:0.5; cursor:not-allowed;' : 'opacity:1;'">
                    Become a Member
                </button>
            </div>

        </div>{{-- /right --}}
    </div>{{-- /card --}}
</div>{{-- /popup --}}

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
<header class="sticky top-0 z-40 bg-[#111111] border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-[54px] md:h-[60px]">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <img src="{{ asset('images/costikyan-logo.png') }}" alt="Costikyan Custom Carpet — Since 1886" class="h-11 md:h-12 w-auto">
            </a>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('shop.index') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Our Collection</a>
                <a href="{{ route('weave') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Weave Your Dream Rug</a>
                <a href="{{ route('about') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">About</a>
                @auth
                <a href="{{ route('trade.portal.dashboard') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Trade Portal</a>
                @else
                <a href="{{ route('trade') }}" class="text-[13px] text-white/90 hover:text-white transition-colors duration-150">Trade</a>
                @endauth
            </nav>

            {{-- Right icons --}}
            <div class="flex items-center gap-4">
                {{-- Cart — minimal modern basket icon with count badge --}}
                <a href="{{ route('cart.index') }}" class="relative text-white/80 hover:text-white transition-colors group" title="Cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.17 0 .318.114.362.278l2.755 9.978c.097.354.42.599.79.599h10.5a.75.75 0 0 0 .68-.43l2.46-5.538a.75.75 0 0 0-1.36-.604L16.697 8.25H4.268l-.577-2.09a.75.75 0 0 0-.722-.543H2.25z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM18.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                    </svg>
                    {{-- Count badge --}}
                    <span id="header-cart-badge"
                          class="absolute -top-2 -right-2.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full text-white text-[10px] font-bold leading-none px-1"
                          style="background: #E8651A; display: none;">
                        0
                    </span>
                </a>

                {{-- Search (visible on both) --}}
                <button @click="searchOpen = !searchOpen" class="text-white/80 hover:text-white transition-colors" title="Search">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                </button>

                {{-- Desktop-only account dropdown --}}
                @auth
                <div class="relative group hidden md:block">
                    <button class="text-white/80 hover:text-white transition-colors" title="Account">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    </button>
                    <div class="absolute top-full right-0 mt-2 w-48 bg-stone-900 border border-stone-700 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="px-4 py-2.5 text-xs text-stone-500 border-b border-stone-700">{{ Auth::user()->name }}</div>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">My Account</a>
                        <a href="{{ route('dashboard.orders') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">My Orders</a>
                        <a href="{{ route('wishlist.index') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">Wishlist</a>
                        <a href="{{ route('room.visualizations') }}" class="block px-4 py-2.5 text-sm text-stone-300 hover:text-white hover:bg-stone-800">My Visualizations</a>
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
                <a href="{{ route('login') }}" class="hidden md:block text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                </a>
                @endauth

                {{-- Desktop wishlist --}}
                <a href="{{ Auth::check() ? route('wishlist.index') : route('login') }}"
                   title="Wishlist"
                   class="hidden md:block text-white/80 hover:text-white transition-colors">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Search bar --}}
    <div x-show="searchOpen" x-cloak class="border-t border-stone-800 bg-stone-900">
        <form action="{{ route('shop.search') }}" method="GET" class="max-w-2xl mx-auto px-4 py-3 flex gap-2">
            <input type="text" name="q" placeholder="Search rugs, styles, materials…" autofocus
                   class="flex-1 bg-stone-800 border border-stone-700 text-white placeholder-stone-500 px-4 py-3 text-sm focus:outline-none focus:border-amber-400 transition-colors"
                   required>
            <button type="submit" class="bg-white text-stone-900 font-medium text-sm px-5 py-2">Search</button>
        </form>
    </div>
</header>

{{-- Page Content --}}
<main>
    @yield('content')
</main>

{{-- FOOTER --}}
<footer class="bg-[#1a1a1a] text-stone-400">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-12 md:pt-16 pb-10 md:pb-12">
        <div class="grid grid-cols-2 md:grid-cols-[280px_1fr_1fr_1fr] gap-8 md:gap-12">

            {{-- ── COL 1: Logo + Social + Contact ── --}}
            <div class="col-span-2 md:col-span-1">
                {{-- Large logo --}}
                <a href="{{ route('home') }}" class="inline-block mb-7">
                    <img src="{{ asset('images/costikyan-logo.png') }}" alt="Costikyan Custom Carpet" class="h-16 w-auto">
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
                    <li><a href="{{ route('about') }}" class="text-[13px] text-stone-400 hover:text-white transition-colors">Delivery &amp; Lead Times</a></li>
                    <li><a href="https://www.costikyan.com/" target="_blank" rel="noopener" class="text-[13px] text-stone-400 hover:text-white transition-colors">Care &amp; Maintenance</a></li>
                </ul>
            </div>

            {{-- ── COL 3: Services ── --}}
            <div>
                <h4 class="text-white text-[15px] font-medium mb-2">Services</h4>
                <div class="w-full h-px bg-stone-700 mb-5"></div>
                <ul class="space-y-3">
                    <li><a href="https://www.costikyan.com/" target="_blank" rel="noopener" class="text-[13px] text-stone-400 hover:text-white transition-colors">Visit Our Services Site</a></li>
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

{{-- ══════════════════════════════════════════
     BOTTOM MOBILE NAV — hidden on md+
══════════════════════════════════════════ --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 flex items-stretch"
     style="background:#111111; border-top:1px solid rgba(255,255,255,0.08); height:64px; padding-bottom:env(safe-area-inset-bottom);">

    {{-- Home --}}
    <a href="{{ route('home') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('home') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" points="9 22 9 12 15 12 15 22"/>
        </svg>
        <span style="font-size:10px; letter-spacing:0.02em;">Home</span>
    </a>

    {{-- Shop --}}
    <a href="{{ route('shop.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('shop.*') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span style="font-size:10px; letter-spacing:0.02em;">Shop</span>
    </a>

    {{-- Cart — minimal modern basket icon with count badge --}}
    <a href="{{ route('cart.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('cart.*') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <div class="relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.17 0 .318.114.362.278l2.755 9.978c.097.354.42.599.79.599h10.5a.75.75 0 0 0 .68-.43l2.46-5.538a.75.75 0 0 0-1.36-.604L16.697 8.25H4.268l-.577-2.09a.75.75 0 0 0-.722-.543H2.25z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM18.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
            </svg>
            <span id="mobile-cart-badge"
                  class="absolute -top-1.5 -right-2 min-w-[14px] h-[14px] flex items-center justify-center rounded-full text-white text-[8px] font-bold leading-none px-0.5"
                  style="background: #E8651A; display: none;">
                0
            </span>
        </div>
        <span style="font-size:10px; letter-spacing:0.02em;">Cart</span>
    </a>

    {{-- Search (centre, larger pill) --}}
    <button @click="searchOpen = !searchOpen"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
                   {{ request()->routeIs('shop.search') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <div class="w-10 h-10 rounded-full bg-amber-400 flex items-center justify-center -mt-5 shadow-lg">
            <svg class="w-5 h-5 text-stone-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
        </div>
        <span style="font-size:10px; letter-spacing:0.02em;">Search</span>
    </button>

    {{-- Wishlist --}}
    <a href="{{ Auth::check() ? route('wishlist.index') : route('login') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('wishlist.*') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span style="font-size:10px; letter-spacing:0.02em;">Saved</span>
    </a>

    {{-- Account --}}
    @auth
    <a href="{{ route('dashboard') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('dashboard*') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <div class="w-6 h-6 rounded-full bg-amber-700 flex items-center justify-center"
             style="font-size:11px; font-weight:700; color:#fff; font-family:'Lusitana',serif;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <span style="font-size:10px; letter-spacing:0.02em;">Account</span>
    </a>
    @else
    <a href="{{ route('login') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors
              {{ request()->routeIs('login') ? 'text-amber-400' : 'text-stone-500 hover:text-stone-200' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
        </svg>
        <span style="font-size:10px; letter-spacing:0.02em;">Sign In</span>
    </a>
    @endauth
</nav>

{{-- ══════════════════════════════════════════
     MOBILE FLOATING CART BUTTON
     Appears when cart has items, fixed bottom-right
══════════════════════════════════════════ --}}
<a href="{{ route('cart.index') }}"
   id="cart-fab"
   class="md:hidden fixed bottom-[80px] right-4 z-50 w-[52px] h-[52px] rounded-full items-center justify-center shadow-xl"
   style="background: linear-gradient(135deg, #E8651A 0%, #EDB84A 100%); display: none;">
    <div class="relative">
        <svg class="w-[22px] h-[22px] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.17 0 .318.114.362.278l2.755 9.978c.097.354.42.599.79.599h10.5a.75.75 0 0 0 .68-.43l2.46-5.538a.75.75 0 0 0-1.36-.604L16.697 8.25H4.268l-.577-2.09a.75.75 0 0 0-.722-.543H2.25z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM18.75 19.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
        </svg>
        <span id="fab-cart-badge"
              class="absolute -top-2.5 -right-2.5 min-w-[18px] h-[18px] flex items-center justify-center rounded-full bg-white text-stone-900 text-[10px] font-bold leading-none px-1"
              style="display: none;">
            0
        </span>
    </div>
</a>

<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.wishlist-toggle');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();
    if (btn.dataset.authenticated !== 'true') {
        window.location.href = '/login';
        return;
    }
    const productId = btn.dataset.productId;
    const isIn = btn.dataset.inWishlist === 'true';
    const svg = btn.querySelector('svg');
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    }).then(r => {
        if (r.ok) {
            const newState = !isIn;
            btn.dataset.inWishlist = newState ? 'true' : 'false';
            if (newState) {
                btn.classList.remove('text-white/80', 'text-stone-400');
                btn.classList.add('text-red-500');
                if (svg) { svg.setAttribute('fill', 'currentColor'); svg.setAttribute('stroke', 'none'); }
            } else {
                btn.classList.remove('text-red-500');
                if (btn.closest('.product-card')) {
                    btn.classList.add('text-white/80');
                } else {
                    btn.classList.add('text-stone-400');
                }
                if (svg) { svg.setAttribute('fill', 'none'); svg.setAttribute('stroke', 'currentColor'); }
            }
        }
    }).catch(() => {});
});
</script>

<script>
(function() {
    function updateCartBadges(count) {
        const show = count > 0;
        const display = show ? 'flex' : 'none';
        const els = {
            header: document.getElementById('header-cart-badge'),
            mobile: document.getElementById('mobile-cart-badge'),
            fab:    document.getElementById('cart-fab'),
            fabBadge: document.getElementById('fab-cart-badge'),
        };
        if (els.header) { els.header.textContent = count; els.header.style.display = display; }
        if (els.mobile) { els.mobile.textContent = count; els.mobile.style.display = display; }
        if (els.fabBadge) { els.fabBadge.textContent = count; els.fabBadge.style.display = display; }
        if (els.fab) { els.fab.style.display = display; }
    }

    fetch('/cart/count', { headers: { 'Accept': 'application/json' } })
        .then(r => r.ok ? r.json() : { count: 0 })
        .then(d => updateCartBadges(d.count || 0))
        .catch(() => {});
})();
</script>

@stack('scripts')
@livewireScripts
</body>
</html>
