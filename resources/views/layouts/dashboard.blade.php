<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Costikyan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#111111">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f7f7f7] overflow-x-hidden" style="font-family:'Inter',sans-serif; padding-bottom:env(safe-area-inset-bottom);"
      x-data="{ sidebarOpen: false, userMenu: false, notifOpen: false }">

{{-- Mobile sidebar overlay --}}
<div x-show="sidebarOpen" x-cloak x-transition.opacity
     class="fixed inset-0 bg-black/40 z-40 lg:hidden" @click="sidebarOpen = false"></div>

<div class="flex min-h-screen lg:h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside class="flex-shrink-0 flex flex-col bg-white border-r border-stone-200 fixed lg:relative z-50 h-full transition-transform duration-200"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           style="width:220px; max-width:85vw; min-height:100vh;">

        {{-- Logo --}}
        <div class="flex items-center justify-between px-5 py-5 border-b border-stone-100">
            <a href="{{ route('home') }}" class="block">
                <img src="{{ asset('images/costikyan-logo.png') }}" alt="Costikyan Custom Carpet"
                     style="height:34px; width:auto; display:block;">
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-stone-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav items --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">
            @php
            $navItems = [
                ['label'=>'Dashboard',     'route'=>'dashboard',          'match'=>'dashboard',          'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
                ['label'=>'My Orders',     'route'=>'dashboard.orders',   'match'=>'dashboard.orders*',  'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
                ['label'=>'Shop',          'route'=>'shop.index',         'match'=>'shop.*',             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                ['label'=>'Wishlist',      'route'=>'wishlist.index',     'match'=>'wishlist*',          'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/>'],
                ['label'=>'Room Viz',      'route'=>'room.visualizations','match'=>'room.visualizations','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.5"/>'],
                ['label'=>'Settings',      'route'=>'profile.show',       'match'=>'profile*',           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>'],
            ];
            @endphp

            @foreach($navItems as $item)
            @php $isActive = $item['match'] && request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-2.5 px-3 py-2.5 mb-0.5 rounded-lg transition-colors text-[13px]
                      {{ $isActive ? 'bg-stone-900 text-white font-medium' : 'text-stone-500 hover:bg-stone-100 hover:text-stone-800' }}">
                <svg class="w-[18px] h-[18px] flex-shrink-0 {{ $isActive ? 'text-white' : 'text-stone-400' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- Bottom links --}}
        <div class="px-3 pb-5 pt-2 border-t border-stone-100 space-y-0.5">
            <a href="{{ route('contact') }}"
               class="flex items-center gap-2.5 px-3 py-2 rounded-md text-[13px] text-stone-500 hover:bg-stone-50 hover:text-stone-800 transition-colors">
                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01"/>
                </svg>
                Support
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2.5 w-full px-3 py-2 rounded-md text-[13px] text-red-500 hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top bar --}}
        <header class="flex-shrink-0 flex items-center justify-between bg-white border-b border-stone-200 px-4 lg:px-6"
                style="height:56px;">
            {{-- Left: hamburger + title --}}
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true"
                        class="lg:hidden text-stone-500 hover:text-stone-900 p-1 -ml-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="font-semibold text-sm lg:text-base text-stone-900">@yield('title', 'Dashboard')</h1>
            </div>

            {{-- Right: bell + user --}}
            <div class="flex items-center gap-2 lg:gap-4">
                @php
                    $notifUser = Auth::user();
                    $notifOrders = $notifUser->orders()->latest()->take(5)->get();
                    $notifViz = $notifUser->roomVisualizations()->where('status', 'completed')->latest()->take(3)->get();
                    $notifCount = $notifOrders->count() + $notifViz->count();
                @endphp
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" class="relative p-1.5 text-stone-400 hover:text-stone-900 transition-colors rounded-md hover:bg-stone-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full flex items-center justify-center text-white" style="font-size:9px; font-weight:700; background:#E8651A;">{{ $notifCount }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 bg-white border border-stone-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-stone-100">
                            <p class="text-sm font-semibold text-stone-900">Notifications</p>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($notifViz as $viz)
                            <a href="{{ route('room.visualizations') }}" @click="notifOpen = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:#fff7ed;">
                                    <svg class="w-4 h-4" fill="none" stroke="#E8651A" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-stone-900 truncate">Room visualization ready</p>
                                    <p class="text-[11px] text-stone-500 truncate">{{ $viz->product->name ?? 'Your rug' }}</p>
                                </div>
                                <span class="text-[10px] text-stone-400 whitespace-nowrap">{{ $viz->created_at->diffForHumans(null, true) }}</span>
                            </a>
                            @empty
                            @endforelse

                            @forelse($notifOrders as $order)
                            <a href="{{ route('dashboard.orders') }}" @click="notifOpen = false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:#f0fdf4;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="#15803d" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 1 1 0-4h14a2 2 0 1 1 0 4M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-stone-900 truncate">Order {{ $order->order_number }} · {{ ucfirst($order->status) }}</p>
                                    <p class="text-[11px] text-stone-500">${{ number_format($order->total, 0) }}</p>
                                </div>
                                <span class="text-[10px] text-stone-400 whitespace-nowrap">{{ $order->created_at->diffForHumans(null, true) }}</span>
                            </a>
                            @empty
                            @if($notifViz->isEmpty())
                            <p class="px-4 py-8 text-center text-xs text-stone-400">No notifications yet</p>
                            @endif
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" @click.outside="userMenu = false">
                    <button @click="userMenu = !userMenu"
                            class="flex items-center gap-2 rounded-md transition-colors">
                        <div class="w-8 h-8 rounded-full bg-stone-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if(Auth::user()->profile_photo_url ?? false)
                            <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover" alt="">
                            @else
                            <span style="font-size:13px; font-weight:700; color:#fff; font-family:'Lusitana',serif;">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </span>
                            @endif
                        </div>
                        <svg class="w-3.5 h-3.5 text-stone-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userMenu" x-cloak x-transition
                         class="absolute right-0 mt-2 w-44 bg-white border border-stone-200 rounded-xl shadow-lg z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-stone-100">
                            <p class="text-sm font-semibold text-stone-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-stone-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 transition-colors">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                            Profile
                        </a>
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 hover:bg-stone-50 transition-colors">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
                            Back to Site
                        </a>
                        <div class="border-t border-stone-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Scrollable content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6 pb-20 lg:pb-6">
            @yield('dashboard-content')
        </main>
    </div>

</div>

{{-- ══════════════════════════════════════════
     MOBILE BOTTOM NAV — Client Dashboard
  ══════════════════════════════════════════ --}}
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 flex items-stretch border-t border-stone-200 bg-white"
     style="height:64px; padding-bottom:env(safe-area-inset-bottom);">
    @php
    $mobileNav = [
        ['route'=>'dashboard',           'label'=>'Home',     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>'],
        ['route'=>'dashboard.orders',    'label'=>'Orders',   'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
        ['route'=>'shop.index',          'label'=>'Shop',     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
        ['route'=>'wishlist.index',      'label'=>'Saved',    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/>'],
    ];
    @endphp
    @foreach($mobileNav as $item)
    @php $active = request()->routeIs($item['route']); @endphp
    <a href="{{ route($item['route']) }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
       style="color: {{ $active ? '#E8651A' : '#94a3b8' }};">
        <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
        <span style="font-size:10px; letter-spacing:0.02em; font-weight:500;">{{ $item['label'] }}</span>
    </a>
    @endforeach
    <button @click="sidebarOpen = true"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
            style="color: {{ request()->routeIs('room.visualizations','profile*') ? '#E8651A' : '#94a3b8' }};">
        <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <span style="font-size:10px; letter-spacing:0.02em; font-weight:500;">More</span>
    </button>
</nav>

@stack('scripts')
@livewireScripts
</body>
</html>
