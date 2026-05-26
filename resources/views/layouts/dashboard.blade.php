<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | Costikyan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f7f7f7]" style="font-family:'Inter',sans-serif;" x-data="{}">

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside class="flex-shrink-0 flex flex-col bg-white border-r border-stone-200"
           style="width:200px; min-height:100vh;">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-stone-100">
            <a href="{{ route('home') }}" class="block">
                <span style="font-family:'Lusitana',serif; font-size:18px; font-weight:700; color:#121212; letter-spacing:0.04em;">COSTIKYAN</span>
            </a>
        </div>

        {{-- Nav items --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto">

            {{-- Active item (Engagement Data highlighted) --}}
            @php
            $navItems = [
                ['label'=>'Engagement Data',   'route'=>'dashboard',          'match'=>'dashboard',          'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
                ['label'=>'Engagement Data',   'route'=>'dashboard.orders',   'match'=>'dashboard.orders*',  'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],
                ['label'=>'Assessment Results','route'=>'shop.index',         'match'=>'shop.*',             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
                ['label'=>'Checklists',        'route'=>'wishlist.index',     'match'=>'wishlist*',          'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2m-2 4l2 2 4-4"/>'],
                ['label'=>'Care Plans',        'route'=>'weave',              'match'=>'weave',              'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/>'],
                ['label'=>'Coaching',          'route'=>'about',              'match'=>'about',              'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5z"/>'],
                ['label'=>'Resources',         'route'=>'shop.index',         'match'=>'',                   'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 0-2 2h-2a2 2 0 0 0-2-2z"/>'],
                ['label'=>'Messages & AI',     'route'=>'contact',            'match'=>'contact',            'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>'],
                ['label'=>'Settings',          'route'=>'profile.show',       'match'=>'profile*',           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>'],
            ];
            @endphp

            @foreach($navItems as $item)
            @php $isActive = $item['match'] && request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-2.5 px-3 py-2 mb-0.5 rounded-md transition-colors text-[13px]
                      {{ $isActive ? 'bg-stone-100 text-stone-900 font-medium' : 'text-stone-500 hover:bg-stone-50 hover:text-stone-800' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-stone-900' : 'text-stone-400' }}"
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
                Support Center
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
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="flex-shrink-0 flex items-center justify-between bg-white border-b border-stone-200 px-6"
                style="height:56px;">
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Search…"
                       class="pl-8 pr-4 py-1.5 text-sm bg-transparent border-0 focus:outline-none text-stone-500 w-48"
                       style="min-width:160px;">
            </div>

            {{-- Right: bell + user --}}
            <div class="flex items-center gap-4">
                {{-- Bell --}}
                <button class="relative text-stone-500 hover:text-stone-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-stone-900 rounded-full"></span>
                </button>

                {{-- User name + plan + avatar --}}
                <div class="flex items-center gap-2.5">
                    <div class="text-right">
                        <p style="font-size:13px; font-weight:600; color:#121212; line-height:1.2;">{{ Auth::user()->name }}</p>
                        <p style="font-size:11px; color:#8a8a8a; line-height:1.2;">
                            @if(Auth::user()->isAdmin()) Admin Plan @else Standard Plan @endif
                        </p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-stone-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if(Auth::user()->profile_photo_url ?? false)
                        <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover" alt="">
                        @else
                        <span style="font-size:14px; font-weight:700; color:#fff; font-family:'Lusitana',serif;">
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        {{-- Scrollable content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('dashboard-content')
        </main>
    </div>

</div>

@stack('scripts')
@livewireScripts
</body>
</html>
