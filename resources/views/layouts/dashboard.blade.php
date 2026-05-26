@extends('layouts.site')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col md:flex-row gap-10">
        {{-- Sidebar --}}
        <aside class="w-full md:w-56 flex-shrink-0">
            <div class="mb-6">
                <p class="text-xs text-stone-400 uppercase tracking-widest mb-1">Signed in as</p>
                <p class="font-medium text-stone-900">{{ Auth::user()->name }}</p>
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm rounded {{ request()->routeIs('dashboard') && !request()->routeIs('dashboard.*') ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-50' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Overview
                </a>
                <a href="{{ route('dashboard.orders') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm rounded {{ request()->routeIs('dashboard.orders*') ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-50' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                    My Orders
                </a>
                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm rounded {{ request()->routeIs('wishlist*') ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-50' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Wishlist
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm rounded {{ request()->routeIs('profile*') ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-50' }} transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                    Profile Settings
                </a>
                <div class="pt-2 border-t border-stone-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2.5 w-full px-3 py-2.5 text-sm text-stone-500 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            @yield('dashboard-content')
        </div>
    </div>
</div>
@endsection
