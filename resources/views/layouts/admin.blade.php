<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Costikyan Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-stone-100" x-data="{ sidebarOpen: true }">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-60 bg-stone-900 flex flex-col flex-shrink-0" :class="sidebarOpen ? 'w-60' : 'w-16'" style="transition: width 0.2s">
        <div class="flex items-center gap-2 px-4 py-4 border-b border-stone-800">
            <div class="bg-stone-800 px-2 py-1 flex-shrink-0">
                <span class="font-serif font-bold text-white text-sm tracking-widest">CC</span>
            </div>
            <span x-show="sidebarOpen" class="text-white text-xs font-semibold tracking-wide">Admin Panel</span>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-0.5 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>'],
                    ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                    ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4z"/>'],
                    ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
                    ['route' => 'admin.customers.index', 'label' => 'Customers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>'],
                    ['route' => 'admin.coupons.index', 'label' => 'Coupons', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>'],
                    ['route' => 'admin.filters.index', 'label' => 'Filters', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .78 1.625L13 12.586V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 7 17v-4.414L3.22 5.625A1 1 0 0 1 3 5V4z"/>'],
                ];
            @endphp

            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="admin-nav-link {{ request()->routeIs($item['route']) || (str_contains($item['route'], '.index') && request()->routeIs(str_replace('.index','',$item['route']).'.*')) ? 'active' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                <span x-show="sidebarOpen">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        <div class="px-2 py-3 border-t border-stone-800">
            <a href="{{ route('home') }}" class="admin-nav-link">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11l2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen">Back to Site</span>
            </a>
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="h-14 bg-white border-b border-stone-200 flex items-center justify-between px-6 flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="text-stone-400 hover:text-stone-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-3">
                <span class="text-sm text-stone-600">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-stone-400 hover:text-red-600 transition-colors">Logout</button>
                </form>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div class="px-6 pt-4">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
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
