<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trade Portal') — Trade Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="background:#f7f7f5; color:#121212;">

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside class="flex-shrink-0 flex flex-col bg-white border-r border-stone-200" style="width:200px;">

        {{-- Logo / Brand --}}
        <div class="px-6 py-5 border-b border-stone-100">
            <span style="font-family:'Lusitana',serif; font-size:17px; font-weight:700; color:#121212;">Trade Portal</span>
        </div>

        {{-- Main nav --}}
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            @php
            $navMain = [
                ['route'=>'trade.portal.dashboard',  'label'=>'Dashboard',        'icon'=>'<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>'],
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
               class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors text-sm {{ $active ? 'bg-stone-100 text-stone-900 font-medium' : 'text-stone-500 hover:text-stone-900 hover:bg-stone-50' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $active ? 'text-amber-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>

        {{-- Bottom nav --}}
        <div class="px-3 py-4 border-t border-stone-100 space-y-0.5">
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-stone-500 hover:text-stone-900 hover:bg-stone-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01"/></svg>
                Support Center
            </a>
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm text-stone-500 hover:text-stone-900 hover:bg-stone-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22" stroke-width="1.5"/></svg>
                Back to Home
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-sm text-stone-500 hover:text-stone-900 hover:bg-stone-50 transition-colors text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="flex-shrink-0 bg-white border-b border-stone-200 flex items-center justify-between px-6" style="height:56px;">
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Search..." class="focus:outline-none pl-9 pr-4 py-1.5 bg-stone-50 border border-stone-200 rounded text-sm" style="width:220px;">
            </div>

            {{-- User --}}
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p style="font-size:13px; font-weight:600; color:#121212;">{{ Auth::user()->name }}</p>
                    <p style="font-size:11px; font-weight:600; color:#B8860B; letter-spacing:0.06em;">GOLD TIER</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0"
                     style="font-family:'Lusitana',serif; font-size:14px; font-weight:700; color:#B8860B;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Flash --}}
        @if(session('success') || session('error'))
        <div class="px-8 pt-4">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2.5 text-sm rounded">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2.5 text-sm rounded">{{ session('error') }}</div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-8 py-8">
            @yield('trade-content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
