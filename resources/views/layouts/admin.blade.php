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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
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
        [x-cloak] { display: none !important; }
        .admin-sidebar-collapsed { width: 72px !important; }
        .admin-sidebar-collapsed .sidebar-text { display: none !important; }
        .admin-sidebar-collapsed .admin-section-label { display: none !important; }
        .admin-sidebar-collapsed .admin-nav-link { justify-content: center; padding: 10px 0 !important; }
        .admin-sidebar-collapsed .admin-nav-link svg:first-child { margin: 0 !important; width: 22px !important; height: 22px !important; }
        .admin-sidebar-collapsed .sidebar-toggle-btn svg { transform: rotate(180deg); }
        /* Collapsed sidebar header: center logo block */
        .admin-sidebar-collapsed #sidebarLogoArea { justify-content: center; padding-left: 0; padding-right: 0; gap: 0; }
        .admin-sidebar-collapsed #sidebarLogoArea .sidebar-toggle-btn { margin-left: 0; }
        .admin-sidebar-collapsed #sidebarLogoBlock span { display: none !important; }
        .admin-sidebar-collapsed #sidebarLogoBlock { border: none !important; background: transparent !important; }
        .admin-sidebar-collapsed #sidebarLogoBlock > div:last-child { padding: 0 !important; }
        .sidebar-toggle-btn { display: none !important; }
        .mobile-close-btn { display: flex !important; }
        @media (min-width: 768px) {
            .sidebar-toggle-btn { display: flex !important; }
            .mobile-close-btn { display: none !important; }
        }
        /* Sidebar positioning: mobile fixed + offscreen, desktop normal flex flow */
        #adminSidebar {
            position: fixed; left: 0; top: 0;
            transform: translateX(-100%);
            transition: transform 0.2s ease, width 0.2s ease;
        }
        #adminSidebar.mobile-open { transform: translateX(0) !important; }
        @media (min-width: 768px) {
            #adminSidebar {
                position: relative !important;
                transform: none !important;
            }
        }
        .admin-section-label {
            font-size:10px; font-weight:600; letter-spacing:0.1em;
            text-transform:uppercase; color:#475569;
            padding:4px 12px 6px; margin-top:8px;
        }
        @media (max-width: 767px) {
            body { padding-bottom: 64px !important; }
        }
        .mobile-sidebar-overlay { display: block; }
        @media (min-width: 768px) {
            .mobile-sidebar-overlay { display: none !important; }
            .mobile-bottom-nav { display: none !important; }
        }
        @media print {
            aside, .mobile-bottom-nav, #dashboard-toolbar form, #dashboard-toolbar .flex.items-center.gap-1\.5,
            #dashboard-toolbar .flex.items-center.gap-2 { display: none !important; }
            #dashboard-toolbar { margin-bottom: 12px !important; }
            .flex-1.min-w-0 { margin-left: 0 !important; }
            body { padding-bottom: 0 !important; background: #fff !important; }
            .bg-white { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body class="antialiased bg-[#f1f5f9]" x-data="{ sidebarOpen: true, userMenu: false, mobileMenuOpen: false, productsOpen: @json(request()->routeIs('admin.products.*')), notifOpen: false }"
      style="padding-bottom: env(safe-area-inset-bottom);">

{{-- Mobile sidebar overlay backdrop --}}
<div x-show="mobileMenuOpen" x-cloak x-transition.opacity
     class="mobile-sidebar-overlay fixed inset-0 bg-black/50 z-40" @click="mobileMenuOpen = false"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ══ SIDEBAR ══ --}}
    <aside id="adminSidebar" class="flex-shrink-0 flex flex-col overflow-hidden z-50 h-full"
           :class="[
               mobileMenuOpen ? 'mobile-open' : '',
               sidebarOpen ? '' : 'admin-sidebar-collapsed'
           ]"
           style="background:linear-gradient(180deg, #141414 0%, #0f0f0f 60%, #0a0a0a 100%); width:240px; min-height:100vh;">

        {{-- Logo --}}
        <div id="sidebarLogoArea" class="flex items-center gap-3 px-4 py-4 border-b flex-shrink-0" style="height:56px; border-color:rgba(255,255,255,0.08);">
            <div id="sidebarLogoBlock" class="flex items-stretch border border-white/20 flex-shrink-0">
                <div class="w-[5px] flex-shrink-0" style="background:#E8651A;"></div>
                <div class="px-2 py-1 flex items-center">
                    <span style="font-family:'Lusitana',serif; font-size:13px; font-weight:700; color:#fff; letter-spacing:0.15em;">CC</span>
                </div>
            </div>
            <span class="sidebar-text text-white font-semibold text-sm tracking-wide whitespace-nowrap">Admin Panel</span>
            <button @click="sidebarOpen = !sidebarOpen" class="sidebar-toggle-btn ml-auto text-white/40 hover:text-white transition-colors" title="Toggle sidebar">
                <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="mobileMenuOpen = false" class="mobile-close-btn ml-auto text-white/60 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-3 overflow-y-auto overflow-x-hidden">
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
                    ['route'=>'admin.zip-prices.index','label'=>'ZIP Pricing',  'match'=>'admin.zip-prices.*',    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/><circle cx="12" cy="11" r="3" stroke-width="1.5"/>'],
                    ['route'=>'admin.trade-accounts.index','label'=>'Trade Accounts','match'=>'admin.trade-accounts.*','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0 1 12 15c-3.183 0-6.164-.62-9-1.745M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2m0 0V4a2 2 0 0 0 2-2h4a2 2 0 0 0 2 2v2m-6 4h.01M12 12h.01M8 12h.01M16 12h.01"/>'],
                ],
                'Submissions' => [
                    ['route'=>'admin.submissions.estimates',     'label'=>'Estimates',      'match'=>'admin.submissions.estimates',      'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 8h6M9 16h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>'],
                    ['route'=>'admin.submissions.visualizations','label'=>'Visualizations', 'match'=>'admin.submissions.visualizations', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>'],
                    ['route'=>'admin.submissions.samples',       'label'=>'Samples',        'match'=>'admin.submissions.samples',        'icon'=>'<circle cx="12" cy="12" r="9" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3"/>'],
                ],
            ];
            @endphp

            @foreach($navGroups as $groupLabel => $items)
                <p class="sidebar-text admin-section-label">{{ $groupLabel }}</p>
                @foreach($items as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                @if(($item['label'] ?? '') === 'Products')
                <div>
                    <button @click="productsOpen = !productsOpen"
                            class="admin-nav-link w-full text-left {{ $isActive ? 'active' : '' }}">
                        <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-orange-500' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                        <span class="sidebar-text flex-1">{{ $item['label'] }}</span>
                        <svg class="sidebar-text w-3 h-3 transition-transform" :class="productsOpen ? 'rotate-90' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div x-show="productsOpen" class="pl-9 space-y-0.5">
                        @php $productsActive = request()->routeIs('admin.products.index'); @endphp
                        <a href="{{ route('admin.products.index') }}"
                           class="block py-1.5 text-xs rounded-md transition-colors {{ $productsActive ? 'text-white font-medium' : 'text-stone-500 hover:text-stone-300' }}"
                           style="padding-left:8px;">All Products</a>
                        @php $createActive = request()->routeIs('admin.products.create'); @endphp
                        <a href="{{ route('admin.products.create') }}"
                           class="block py-1.5 text-xs rounded-md transition-colors {{ $createActive ? 'text-white font-medium' : 'text-stone-500 hover:text-stone-300' }}"
                           style="padding-left:8px;">Add New</a>
                        @php $importActive = request()->routeIs('admin.products.import'); @endphp
                        <a href="{{ route('admin.products.import') }}"
                           class="block py-1.5 text-xs rounded-md transition-colors {{ $importActive ? 'text-white font-medium' : 'text-stone-500 hover:text-stone-300' }}"
                           style="padding-left:8px;">Bulk Import</a>
                    </div>
                </div>
                @else
                <a href="{{ route($item['route']) }}"
                   class="admin-nav-link {{ $isActive ? 'active' : '' }}"
                   title="{{ $item['label'] }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-orange-500' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    <span class="sidebar-text">{{ $item['label'] }}</span>
                </a>
                @endif
                @endforeach
            @endforeach
        </nav>

        {{-- Bottom --}}
        <div class="px-2 py-3 border-t flex-shrink-0" style="border-color:rgba(255,255,255,0.08);">
            <a href="{{ route('home') }}" class="admin-nav-link" title="Back to site">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/>
                </svg>
                <span class="sidebar-text">Back to Site</span>
            </a>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top bar --}}
        <header class="flex-shrink-0 flex items-center justify-between bg-white border-b border-stone-200 px-3 md:px-5"
                style="height:56px;">
            {{-- Left: mobile hamburger + toggle + breadcrumb --}}
            <div class="flex items-center gap-3">
                <button @click="mobileMenuOpen = true"
                        class="md:hidden text-stone-500 hover:text-stone-900 p-1 -ml-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="flex items-center gap-2 md:gap-3">
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
                <div class="relative" @click.outside="notifOpen=false">
                    <button @click="notifOpen=!notifOpen" class="relative p-1.5 text-stone-400 hover:text-stone-900 transition-colors rounded-md hover:bg-stone-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                        </svg>
                        @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                        @if($pendingCount > 0)
                        <span class="absolute top-0.5 right-0.5 w-4 h-4 rounded-full flex items-center justify-center text-white"
                              style="font-size:9px; font-weight:700; background:#E8651A;">{{ $pendingCount }}</span>
                        @endif
                    </button>
                    <div x-show="notifOpen" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 bg-white border border-stone-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-stone-100 flex items-center justify-between">
                            <p class="text-sm font-semibold text-stone-900">Notifications</p>
                            @if($pendingCount > 0)
                            <span class="px-1.5 py-0.5 rounded-full text-white text-[10px] font-bold" style="background:#E8651A;">{{ $pendingCount }}</span>
                            @endif
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            @php
                            $notifOrders = \App\Models\Order::with('user')->where('status','pending')->latest()->take(6)->get();
                            @endphp
                            @forelse($notifOrders as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" @click="notifOpen=false"
                               class="flex items-center gap-3 px-4 py-3 hover:bg-stone-50 transition-colors {{ !$loop->last ? 'border-b border-stone-50' : '' }}">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background:#fff7ed;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-stone-900 truncate">Order {{ $order->order_number }}</p>
                                    <p class="text-[11px] text-stone-500">{{ $order->user?->name ?? 'Guest' }} · ${{ number_format($order->total, 0) }}</p>
                                </div>
                                <span class="text-[10px] text-stone-400 whitespace-nowrap">{{ $order->created_at->diffForHumans() }}</span>
                            </a>
                            @empty
                            <p class="px-4 py-8 text-center text-xs text-stone-400">No pending orders</p>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2.5 text-xs font-semibold text-center text-orange-600 hover:bg-orange-50 border-t border-stone-100 transition-colors">View all orders</a>
                    </div>
                </div>

                {{-- User dropdown --}}
                <div class="relative" @click.outside="userMenu=false">
                    <button @click="userMenu=!userMenu"
                            class="flex items-center gap-2 px-1 md:px-2.5 py-1.5 rounded-md hover:bg-stone-100 transition-colors">
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
                        <svg class="w-3.5 h-3.5 text-stone-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div class="px-3 md:px-6 pt-3 md:pt-4">
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
        <main class="flex-1 overflow-y-auto p-4 md:p-6 pb-20 md:pb-6">
            @yield('admin-content')
        </main>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MOBILE BOTTOM NAV — Admin
  ══════════════════════════════════════════ --}}
<nav class="mobile-bottom-nav fixed bottom-0 left-0 right-0 z-50 flex items-stretch border-t border-white/10"
     style="background:#0f172a; height:64px; padding-bottom:env(safe-area-inset-bottom);">
    @php
    $mobileNav = [
        ['route'=>'admin.dashboard',       'label'=>'Home',     'icon'=>'<rect x="3" y="3" width="7" height="7" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" stroke-width="1.5"/>'],
        ['route'=>'admin.products.index',  'label'=>'Products', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
        ['route'=>'admin.orders.index',    'label'=>'Orders',   'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
        ['route'=>'admin.customers.index', 'label'=>'Users',    'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>'],
    ];
    @endphp
    @foreach($mobileNav as $item)
    @php $active = request()->routeIs($item['route']); @endphp
    <a href="{{ route($item['route']) }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
       style="color: {{ $active ? '#E8651A' : '#94a3b8' }};">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
        <span style="font-size:10px; letter-spacing:0.02em;">{{ $item['label'] }}</span>
    </a>
    @endforeach
    <button @click="mobileMenuOpen = true"
            class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors"
            style="color: {{ request()->routeIs('admin.*') && !request()->routeIs('admin.dashboard','admin.products.*','admin.orders.*','admin.customers.*') ? '#E8651A' : '#94a3b8' }};">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <span style="font-size:10px; letter-spacing:0.02em;">More</span>
    </button>
</nav>

@livewireScripts
@stack('scripts')
</body>
</html>
