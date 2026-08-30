@extends('layouts.admin')
@section('title', 'Products')

@php
$view = request('view', 'list');
@endphp

@section('admin-content')

{{-- Header bar --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#0f172a;">Products</h1>
        <p style="font-size:13px; color:#64748b; margin-top:3px;">{{ $products->total() }} items in catalogue</p>
    </div>
    <a href="{{ route('admin.products.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md text-white font-medium text-sm transition-all hover:opacity-90"
       style="background:#E8651A; letter-spacing:0.02em;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Product
    </a>
</div>

{{-- Filters + View Toggle --}}
<div class="bg-white rounded-xl border border-stone-200 p-4 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            @if(request('view'))<input type="hidden" name="view" value="{{ $view }}">@endif
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 transition-colors"
                       style="color:#0f172a;">
            </div>
            <select name="category"
                    class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                    style="color:#0f172a; min-width:160px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
                    style="border-color:#e5e7eb; color:#374151;">Filter</button>
            @if(request('search') || request('category'))
            <a href="{{ route('admin.products.index', ['view' => $view]) }}"
               class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
               style="border-color:#e5e7eb; color:#64748b;">Clear</a>
            @endif
        </form>

        {{-- View toggle --}}
        <div class="flex items-center border border-stone-200 rounded-lg overflow-hidden flex-shrink-0">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
               class="flex items-center justify-center w-9 h-9 transition-colors {{ $view === 'grid' ? 'bg-stone-100 text-stone-900' : 'text-stone-400 hover:text-stone-600' }}"
               title="Grid view">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
               class="flex items-center justify-center w-9 h-9 transition-colors {{ $view === 'list' ? 'bg-stone-100 text-stone-900' : 'text-stone-400 hover:text-stone-600' }}"
               title="List view">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </a>
        </div>
    </div>
</div>

@if($view === 'grid')
{{-- ══════════════════════════════════════════
     GRID VIEW
  ══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @forelse($products as $product)
    <div class="group bg-white rounded-xl border border-stone-200 overflow-hidden hover:shadow-md transition-all duration-200">
        {{-- Image --}}
        <a href="{{ route('admin.products.edit', $product) }}" class="block relative overflow-hidden" style="aspect-ratio:4/5;">
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>

            {{-- Status badge --}}
            <div class="absolute top-3 left-3">
                @if($product->status === 'active')
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" style="background:#dcfce7; color:#15803d;">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Active
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" style="background:#f3f4f6; color:#6b7280;">
                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Draft
                </span>
                @endif
            </div>

            {{-- Featured / Bestseller / New badges --}}
            <div class="absolute top-3 right-3 flex flex-col gap-1 items-end">
                @if($product->is_new_arrival)
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider" style="background:#E8651A; color:#fff;">New</span>
                @endif
                @if($product->is_bestseller)
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider" style="background:#0f172a; color:#fff;">Best</span>
                @endif
                @if($product->featured)
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider" style="background:#EDB84A; color:#0f172a;">Featured</span>
                @endif
            </div>

            {{-- Hover overlay actions --}}
            <div class="absolute bottom-3 left-3 right-3 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-200 translate-y-2 group-hover:translate-y-0">
                <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
                   class="flex items-center justify-center w-8 h-8 rounded-md text-white hover:opacity-90 transition-opacity"
                   style="background:#0f172a;" title="Preview">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-medium rounded-md text-white hover:opacity-90 transition-opacity"
                   style="background:#E8651A;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </a>
                <form action="{{ route('admin.products.duplicate', $product) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit"
                            class="w-8 h-8 flex items-center justify-center rounded-md text-white hover:opacity-90 transition-opacity"
                            style="background:#3b82f6;" title="Duplicate"
                            onclick="return confirm('Duplicate this product?')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </form>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="flex-shrink-0"
                      onsubmit="return confirm('Delete this product?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-md text-white hover:opacity-90 transition-opacity" style="background:#ef4444;" title="Delete">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </a>

        {{-- Meta --}}
        <div class="p-4">
            <p class="text-xs text-stone-500 mb-1">{{ $product->category?->name ?? 'Uncategorized' }}</p>
            <a href="{{ route('admin.products.edit', $product) }}" class="block mb-2">
                <h3 class="font-medium text-sm leading-snug" style="color:#0f172a;">{{ Str::limit($product->name, 40) }}</h3>
            </a>
            <div class="flex items-center justify-between">
                <p class="font-semibold text-sm" style="color:#0f172a;">
                    @if($product->sale_price)
                    <span class="text-red-600">${{ number_format($product->sale_price, 0) }}</span>
                    <span class="text-stone-400 line-through text-xs ml-1">${{ number_format($product->price, 0) }}</span>
                    @else
                    ${{ number_format($product->price, 0) }}
                    @endif
                </p>
                <p class="text-xs font-medium px-2 py-1 rounded-md" style="background:#f1f5f9; color:#475569;">
                    {{ $product->stock }} in stock
                </p>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background:#f8fafc;">
            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <p style="font-size:14px; color:#94a3b8;">No products found.</p>
        <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-sm font-medium" style="color:#E8651A;">Create your first product →</a>
    </div>
    @endforelse
</div>

@else
{{-- ══════════════════════════════════════════
     LIST VIEW
  ══════════════════════════════════════════ --}}
<div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <tr>
                <th class="text-left px-5 py-3.5" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Product</th>
                <th class="text-left px-5 py-3.5 hidden md:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Category</th>
                <th class="text-left px-5 py-3.5 hidden sm:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Price</th>
                <th class="text-left px-5 py-3.5 hidden sm:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Stock</th>
                <th class="text-left px-5 py-3.5 hidden lg:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Flags</th>
                <th class="text-left px-5 py-3.5 hidden sm:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                <th class="px-5 py-3.5 text-right" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr class="hover:bg-stone-50 transition-colors" style="border-bottom:1px solid #f1f5f9;">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-stone-200" style="background:#f8fafc;">
                            <img src="{{ $product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-sm hover:underline" style="color:#0f172a;">{{ Str::limit($product->name, 35) }}</a>
                            <p class="text-xs text-stone-400">{{ $product->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 hidden md:table-cell text-stone-600">{{ $product->category?->name ?? '—' }}</td>
                <td class="px-5 py-3.5 hidden sm:table-cell font-medium">
                    @if($product->sale_price)
                    <span class="text-red-600">${{ number_format($product->sale_price, 0) }}</span>
                    <span class="text-stone-400 line-through text-xs ml-1">${{ number_format($product->price, 0) }}</span>
                    @else
                    ${{ number_format($product->price, 0) }}
                    @endif
                </td>
                <td class="px-5 py-3.5 hidden sm:table-cell text-stone-600">{{ $product->stock }}</td>
                <td class="px-5 py-3.5 hidden lg:table-cell">
                    <div class="flex flex-wrap gap-1">
                        @if($product->featured)<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background:#fffbeb; color:#b45309;">Featured</span>@endif
                        @if($product->is_bestseller)<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background:#f1f5f9; color:#374151;">Best</span>@endif
                        @if($product->is_new_arrival)<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background:#fef2f2; color:#b91c1c;">New</span>@endif
                    </div>
                </td>
                <td class="px-5 py-3.5 hidden sm:table-cell">
                    @if($product->status === 'active')
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color:#15803d;">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Active
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium" style="color:#6b7280;">
                        <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span> Draft
                    </span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-stone-900 hover:border-stone-300 transition-colors"
                           title="Preview">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-stone-900 hover:border-stone-300 transition-colors"
                           title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('admin.products.duplicate', $product) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-blue-600 hover:border-blue-200 transition-colors"
                                    title="Duplicate">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-red-600 hover:border-red-200 transition-colors"
                                    title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center" style="font-size:14px; color:#94a3b8;">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background:#f8fafc;">
                        <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p>No products found.</p>
                    <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-sm font-medium" style="color:#E8651A;">Create your first product →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

<div class="mt-8">{{ $products->links() }}</div>
@endsection
