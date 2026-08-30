<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trade Portal') — Trade Portal</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#111111">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        .trade-sidebar-collapsed { width: 80px !important; }
        .trade-sidebar-collapsed .trade-sidebar-text { display: none !important; }
        .trade-sidebar-collapsed .trade-nav-link { justify-content: center; padding: 10px 0 !important; }
        .trade-sidebar-collapsed .trade-nav-link svg:first-child { margin: 0 !important; width: 22px !important; height: 22px !important; }
        .trade-sidebar-collapsed .trade-section-label { display: none !important; }
        .trade-sidebar-collapsed .trade-toggle-btn svg { transform: rotate(180deg); }
        .trade-sidebar-collapsed #tradeLogoArea { justify-content: center; padding-left: 0; padding-right: 0; gap: 0; }
        .trade-sidebar-collapsed #tradeLogoArea .trade-toggle-btn { margin-left: 0; }
        .trade-sidebar-collapsed #tradeLogoBlock span { display: none !important; }
        .trade-sidebar-collapsed #tradeLogoBlock { border: none !important; background: transparent !important; }
        .trade-sidebar-collapsed #tradeLogoBlock > div:last-child { padding: 0 !important; }
        .trade-toggle-btn { display: none !important; }
        .trade-mobile-close { display: flex !important; }
        @media (min-width: 1024px) {
            .trade-toggle-btn { display: flex !important; }
            .trade-mobile-close { display: none !important; }
        }
        /* Sidebar positioning: mobile fixed + offscreen, desktop normal flex flow */
        #tradeSidebar {
            position: fixed; left: 0; top: 0;
            transform: translateX(-100%);
            transition: transform 0.2s ease, width 0.2s ease;
        }
        #tradeSidebar.mobile-open { transform: translateX(0) !important; }
        @media (min-width: 1024px) {
            #tradeSidebar {
                position: relative !important;
                transform: none !important;
            }
            .trade-mobile-overlay { display: none !important; }
            .trade-mobile-nav { display: none !important; }
            .trade-mobile-hamburger { display: none !important; }
        }
    </style>
</head>
<body class="font-sans antialiased" style="background:#f7f7f5; color:#121212; padding-bottom:env(safe-area-inset-bottom);"
      x-data="{ sidebarOpen: false, userMenu: false, sidebarCollapsed: false, notifOpen: false }">

{{-- Mobile sidebar overlay --}}
<div x-show="sidebarOpen" x-cloak x-transition.opacity
     class="trade-mobile-overlay fixed inset-0 bg-black/40 z-40" @click="sidebarOpen = false"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside id="tradeSidebar" class="flex-shrink-0 flex flex-col z-50 h-full"
           :class="[
               sidebarOpen ? 'mobile-open' : '',
               sidebarCollapsed ? 'trade-sidebar-collapsed' : ''
           ]"
           style="background:linear-gradient(180deg, #141414 0%, #0f0f0f 60%, #0a0a0a 100%); width:220px; min-height:100vh;">

        {{-- Logo / Brand --}}
        <div id="tradeLogoArea" class="flex items-center gap-3 px-4 py-4 border-b flex-shrink-0" style="height:56px; border-color:rgba(255,255,255,0.08);">
            <div id="tradeLogoBlock" class="flex items-stretch border border-white/20 flex-shrink-0">
                <div class="w-[5px] flex-shrink-0" style="background:#B8860B;"></div>
                <div class="px-2 py-1 flex items-center">
                    <span style="font-family:'Lusitana',serif; font-size:13px; font-weight:700; color:#fff; letter-spacing:0.15em;">TP</span>
                </div>
            </div>
            <span class="trade-sidebar-text text-white font-semibold text-sm tracking-wide whitespace-nowrap">Trade Portal</span>
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="trade-toggle-btn ml-auto text-white/40 hover:text-white transition-colors" title="Toggle sidebar">
                <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="sidebarOpen = false" class="trade-mobile-close ml-auto text-white/60 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Main nav --}}
        <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
            @php
            $navMain = [
                ['route'=>'trade.portal.dashboard',  'label'=>'Dashboard',        'icon'=>'<rect x="3" y="3" width="7" height="7" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" stroke-width="1.5"/>'],
                ['route'=>'trade.portal.projects',   'label'=>'Projects',         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>'],
                ['route'=>'trade.portal.quotes',     'label'=>'Quotes',           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 8h6M9 16h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>'],
                ['route'=>'trade.portal.samples',    'label'=>'Sample Requests',  'icon'=>'<circle cx="12" cy="12" r="9" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3"/>'],
                ['route'=>'trade.portal.orders',     'label'=>'Orders',           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>'],
                ['route'=>'trade.portal.account',    'label'=>'Account',          'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'],
            ];
            @endphp
            @foreach($navMain as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               class="trade-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors text-sm {{ $active ? 'bg-white/10 text-white font-medium' : 'text-stone-400 hover:text-white hover:bg-white/5' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $active ? 'text-amber-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                <span class="trade-sidebar-text">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Bottom nav --}}
        <div class="px-2 py-3 border-t flex-shrink-0" style="border-color:rgba(255,255,255,0.08);">
            <a href="#" class="trade-nav-link flex items-center gap-3 px-3 py-2 rounded-md text-sm text-stone-400 hover:text-white hover:bg-white/5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01"/></svg>
                <span class="trade-sidebar-text">Support Center</span>
            </a>
            <a href="{{ route('home') }}" class="trade-nav-link flex items-center gap-3 px-3 py-2 rounded-md text-sm text-stone-400 hover:text-white hover:bg-white/5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.5"/></svg>
                <span class="trade-sidebar-text">Back to Home</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="trade-nav-link w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                    <span class="trade-sidebar-text">Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top bar --}}
        <header class="flex-shrink-0 bg-white border-b border-stone-200 flex items-center justify-between px-4 lg:px-6" style="height:56px;">
            {{-- Left: hamburger + title --}}
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true"
                        class="trade-mobile-hamburger text-stone-500 hover:text-stone-900 p-1 -ml-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="font-semibold text-sm lg:text-base text-stone-900">@yield('title', 'Trade Portal')</h1>
            </div>

            {{-- Right: notifications + user --}}
            <div class="flex items-center gap-2 lg:gap-3">
                @php
                    $notifUser = Auth::user();
                    $notifQuotes = $notifUser->tradeQuotes()->latest()->take(4)->get();
                    $notifProjects = $notifUser->tradeProjects()->latest()->take(3)->get();
                    $notifCount = $notifQuotes->count() + $notifProjects->count();
                @endphp
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" class="relative p-1.5 text-stone-400 hover:text-stone-900 transition-colors rounded-md hover:bg-stone-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full flex items-center justify-center text-white" style="font-size:9px; font-weight:700; background:#B8860B;">{{ $notifCount }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 bg-white border border-stone-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-stone-100">
                            <p class="text-sm font-semibold text-stone-900">Notifications</p>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @foreach($notifQuotes as $quote)
                            <a href="{{ route('trade.portal.quotes') }}" @click="notifOpen = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:#fefce8;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="#B8860B" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-stone-900 truncate">Quote {{ $quote->quote_number }} · {{ ucfirst($quote->status) }}</p>
                                    <p class="text-[11px] text-stone-500">${{ number_format($quote->total, 0) }}</p>
                                </div>
                                <span class="text-[10px] text-stone-400 whitespace-nowrap">{{ $quote->created_at->diffForHumans(null, true) }}</span>
                            </a>
                            @endforeach
                            @foreach($notifProjects as $project)
                            <a href="{{ route('trade.portal.projects') }}" @click="notifOpen = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:#f0fdf4;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="#15803d" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-stone-900 truncate">{{ $project->name }} · {{ ucfirst($project->status) }}</p>
                                    <p class="text-[11px] text-stone-500 truncate">{{ $project->client_name }}</p>
                                </div>
                                <span class="text-[10px] text-stone-400 whitespace-nowrap">{{ $project->created_at->diffForHumans(null, true) }}</span>
                            </a>
                            @endforeach
                            @if($notifCount === 0)
                            <p class="px-4 py-8 text-center text-xs text-stone-400">No notifications yet</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="hidden sm:block text-right">
                    <p style="font-size:13px; font-weight:600; color:#121212;">{{ Auth::user()->name }}</p>
                    <p style="font-size:11px; font-weight:600; color:#B8860B; letter-spacing:0.06em;">{{ Auth::user()->trade_discount }}% Trade Discount</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0"
                     style="font-family:'Lusitana',serif; font-size:14px; font-weight:700; color:#B8860B;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success') || session('error'))
        <div class="px-4 lg:px-8 pt-3 lg:pt-4">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2.5 text-sm rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 text-sm rounded">{{ session('error') }}</div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-4 lg:px-8 py-6 lg:py-8 pb-20 lg:pb-8">
            @yield('trade-content')
        </main>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MOBILE BOTTOM NAV — Trade Portal
  ══════════════════════════════════════════ --}}
<nav class="trade-mobile-nav fixed bottom-0 left-0 right-0 z-50 flex items-stretch border-t border-stone-200 bg-white"
     style="height:64px; padding-bottom:env(safe-area-inset-bottom);">
    @php
    $mobileNav = [
        ['route'=>'trade.portal.dashboard', 'label'=>'Home',      'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.2"/>'],
        ['route'=>'trade.portal.projects',  'label'=>'Projects',  'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>'],
        ['route'=>'trade.portal.quotes',   'label'=>'Quotes',    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8" stroke-width="1.2"/><line x1="16" y1="13" x2="8" y2="13" stroke-width="1.2"/><line x1="16" y1="17" x2="8" y2="17" stroke-width="1.2"/><line x1="10" y1="9" x2="8" y2="9" stroke-width="1.2"/>'],
        ['route'=>'trade.portal.orders',   'label'=>'Orders',    'icon'=>'<circle cx="9" cy="21" r="1" stroke-width="1.2"/><circle cx="20" cy="21" r="1" stroke-width="1.2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>'],
    ];
    @endphp
    @foreach($mobileNav as $item)
    @php $active = request()->routeIs($item['route']); @endphp
    <a href="{{ route($item['route']) }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
       style="color: {{ $active ? '#B8860B' : '#94a3b8' }};">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
        <span style="font-size:9px; letter-spacing:0.03em; font-weight:500;">{{ $item['label'] }}</span>
    </a>
    @endforeach
    <button @click="sidebarOpen = true"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
            style="color: {{ request()->routeIs('trade.portal.samples','trade.portal.account') ? '#B8860B' : '#94a3b8' }};">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 12h18M3 6h18M3 18h18"/></svg>
        <span style="font-size:9px; letter-spacing:0.03em; font-weight:500;">More</span>
    </button>
</nav>

@livewireScripts
@stack('scripts')
</body>
</html>
