<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Costikyan Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        .admin-nav-link {
            display:flex; align-items:center; gap:10px;
            padding:8px 12px; border-radius:6px;
            font-size:13px; font-weight:500; color:#94a3b8;
            text-decoration:none; transition:all 0.15s;
            white-space:nowrap; overflow:hidden;
        }
        .admin-nav-link:hover { background:rgba(255,255,255,0.06); color:#e2e8f0; }
        .admin-nav-link.active { background:rgba(255,255,255,0.1); color:#fff; }
        .admin-nav-link.active svg { color:#E8651A; }
        .admin-section-label {
            font-size:10px; font-weight:600; letter-spacing:0.1em;
            text-transform:uppercase; color:#475569;
            padding:4px 12px 6px; margin-top:8px;
        }
    </style>
</head>
<body class="antialiased bg-[#f1f5f9]" x-data="{ sidebarOpen: true, userMenu: false }">

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside class="flex-shrink-0 flex flex-col overflow-hidden transition-all duration-200"
           :style="sidebarOpen ? 'width:240px;' : 'width:60px;'"
           style="background:#0f172a; min-height:100vh;">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/5 flex-shrink-0" style="height:56px;">
            <div class="flex items-stretch border border-white/20 flex-shrink-0">
                <div class="w-[5px] flex-shrink-0" style="background:#E8651A;"></div>
                <div class="px-2 py-1 flex items-center">
                    <span style="font-family:'Lusitana',serif; font-size:13px; font-weight:700; color:#fff; letter-spacing:0.15em;">CC</span>
                </div>
            </div>
            <span x-show="sidebarOpen" x-transition class="text-white font-semibold text-sm tracking-wide whitespace-nowrap">Admin Panel</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-3 overflow-y-auto overflow-x-hidden">

            <p class="admin-section-label" x-show="sidebarOpen">Overview</p>
            @php
            $navGroups = [
                'Overview' => [
                    ['route'=>'admin.dashboard',       'label'=>'Dashboard',   'match'=>'admin.dashboard',       'icon'=>'<rect x="3" y="3" width="7" height="7" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" stroke-width="1.5"/>'],
                ],
                'Catalogue' => [
                    ['route'=>'admin.products.index',  'label'=>'Products',    'match'=>'admin.products.*',      'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                    ['route'=>'admin.categories.index','label'=>'Categories',  'match'=>'admin.categories.*',    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4z"/>'],
                    ['route'=>'admin.filters.index',   'label'=>'Filters',     'match'=>'admin.filters.*',       'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .78 1.625L13 12.586V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 7 17v-4.414L3.22 5.625A1 1 0 0 1 3 5V4z"/>'],
                ],
                'Commerce' => [
                    ['route'=>'admin.orders.index',    'label'=>'Orders',      'match'=>'admin.orders.*',        'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
                    ['route'=>'admin.customers.index', 'label'=>'Customers',   'match'=>'admin.customers.*',     'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>'],
                    ['route'=>'admin.coupons.index',   'label'=>'Coupons',     'match'=>'admin.coupons.*',       'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>'],
                ],
            ];
            @endphp

            @foreach($navGroups as $groupLabel => $items)
                <p class="admin-section-label" x-show="sidebarOpen">{{ $groupLabel }}</p>
                @foreach($items as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="admin-nav-link {{ $isActive ? 'active' : '' }}"
                   title="{{ $item['label'] }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-orange-500' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    <span x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                </a>
                @endforeach
            @endforeach
        </nav>

        {{-- Bottom --}}
        <div class="px-2 py-3 border-t border-white/5">
            <a href="{{ route('home') }}" class="admin-nav-link" title="Back to site">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" x-transition>Back to Site</span>
            </a>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="flex-shrink-0 flex items-center justify-between bg-white border-b border-stone-200 px-5"
                style="height:56px;">
            {{-- Left: toggle + breadcrumb --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="text-stone-400 hover:text-stone-900 transition-colors p-1 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="hidden sm:flex items-center gap-1.5 text-xs text-stone-400">
                    <span>Admin</span>
                    <span>/</span>
                    <span class="text-stone-700 font-medium">@yield('title', 'Dashboard')</span>
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-md border border-stone-200 bg-stone-50 text-stone-400"
                     style="min-width:180px;">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-width="1.8" d="M21 21l-4.35-4.35"/>
                    </svg>
                    <input type="text" placeholder="Quick search…"
                           class="bg-transparent border-0 outline-none text-xs text-stone-600 w-full"
                           style="min-width:0;">
                </div>

                {{-- Notification bell --}}
                <button class="relative p-1.5 text-stone-400 hover:text-stone-900 transition-colors rounded-md hover:bg-stone-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                    </svg>
                    @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full flex items-center justify-center text-white"
                          style="font-size:9px; font-weight:700; background:#E8651A;">{{ $pendingCount }}</span>
                    @endif
                </button>

                {{-- User dropdown --}}
                <div class="relative" @click.outside="userMenu=false">
                    <button @click="userMenu=!userMenu"
                            class="flex items-center gap-2 px-2.5 py-1.5 rounded-md hover:bg-stone-100 transition-colors">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                             style="background:#0f172a;">
                            <span style="font-size:11px; font-weight:700; color:#fff; font-family:'Lusitana',serif;">
                                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                            </span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p style="font-size:12px; font-weight:600; color:#111; line-height:1.2;">{{ Auth::user()->name }}</p>
                            <p style="font-size:10px; color:#9ca3af;">Administrator</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="userMenu" x-cloak x-transition
                         class="absolute right-0 mt-1 w-44 bg-white border border-stone-200 rounded-lg shadow-lg z-50 overflow-hidden">
                        <a href="{{ route('profile.show') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-stone-700 hover:bg-stone-50 transition-colors">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                            My Profile
                        </a>
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-stone-700 hover:bg-stone-50 transition-colors">
                            <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
                            View Site
                        </a>
                        <div class="border-t border-stone-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2.5 w-full px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div class="px-6 pt-4">
            @if(session('success'))
            <div class="flex items-center gap-2.5 bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm rounded-md">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-2.5 bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm rounded-md">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM8.707 7.293a1 1 0 0 0-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 1 0 1.414 1.414L10 11.414l1.293 1.293a1 1 0 0 0 1.414-1.414L11.414 10l1.293-1.293a1 1 0 0 0-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('admin-content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
