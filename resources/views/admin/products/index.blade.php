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
    <div class="flex flex-wrap items-center gap-2">
        {{-- Export Button --}}
        <a href="{{ route('admin.products.export', request()->all()) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-stone-700 font-medium text-sm transition-all hover:bg-stone-100 border border-stone-200"
           title="Export to CSV">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export
        </a>
        {{-- Import Button --}}
        <button type="button" onclick="const m=document.getElementById('importModal');m.classList.remove('hidden');m.classList.add('flex');"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-stone-700 font-medium text-sm transition-all hover:bg-stone-100 border border-stone-200"
                title="Import from CSV">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Import
        </button>
        {{-- Filter Management --}}
        <a href="{{ route('admin.product-filters.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-stone-700 font-medium text-sm transition-all hover:bg-stone-100 border border-stone-200"
           title="Manage Filters">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filters
        </a>
        <a href="{{ route('admin.products.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-md text-white font-medium text-sm transition-all hover:opacity-90"
           style="background:#E8651A; letter-spacing:0.02em;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>
</div>

{{-- Advanced Filters + View Toggle --}}
<div class="bg-white rounded-xl border border-stone-200 p-4 mb-6">
    <form method="GET" id="filterForm" class="space-y-4">
        @if(request('view'))<input type="hidden" name="view" value="{{ $view }}">@endif
        
        {{-- Main Filters Row --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products…"
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 transition-colors"
                       style="color:#0f172a;">
            </div>
            
            {{-- Category --}}
            <select name="category"
                    class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                    style="color:#0f172a; min-width:160px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            
            {{-- Status --}}
            <select name="status"
                    class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                    style="color:#0f172a; min-width:140px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            
            {{-- Featured --}}
            <select name="featured"
                    class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                    style="color:#0f172a; min-width:140px;">
                <option value="">All Products</option>
                <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Non-Featured</option>
            </select>
            
            {{-- Filter Toggle Button --}}
            <button type="button" onclick="toggleAdvancedFilters()"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
                    style="border-color:#e5e7eb; color:#374151;">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Advanced
                </span>
            </button>
            
            {{-- Action Buttons --}}
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
                    style="border-color:#e5e7eb; color:#374151;">Filter</button>
            @if(request()->hasAny(['search', 'category', 'status', 'featured', 'material', 'origin', 'style', 'stock_status', 'price_min', 'price_max']))
            <a href="{{ route('admin.products.index', ['view' => $view]) }}"
               class="px-5 py-2.5 text-sm font-medium rounded-lg border transition-colors hover:bg-stone-50"
               style="border-color:#e5e7eb; color:#64748b;">Clear</a>
            @endif
            
            {{-- View toggle --}}
            <div class="flex items-center border border-stone-200 rounded-lg overflow-hidden flex-shrink-0 ml-auto">
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
        
        {{-- Advanced Filters (Hidden by default) --}}
        <div id="advancedFilters" class="hidden pt-4 border-t border-stone-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Material --}}
                <select name="material"
                        class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                        style="color:#0f172a;">
                    <option value="">All Materials</option>
                    @foreach($materials as $material)
                    <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                    @endforeach
                </select>
                
                {{-- Origin --}}
                <select name="origin"
                        class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                        style="color:#0f172a;">
                    <option value="">All Origins</option>
                    @foreach($origins as $origin)
                    <option value="{{ $origin }}" {{ request('origin') == $origin ? 'selected' : '' }}>{{ $origin }}</option>
                    @endforeach
                </select>
                
                {{-- Style --}}
                <select name="style"
                        class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                        style="color:#0f172a;">
                    <option value="">All Styles</option>
                    @foreach($styles as $style)
                    <option value="{{ $style }}" {{ request('style') == $style ? 'selected' : '' }}>{{ $style }}</option>
                    @endforeach
                </select>
                
                {{-- Stock Status --}}
                <select name="stock_status"
                        class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                        style="color:#0f172a;">
                    <option value="">All Stock Status</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="on_backorder" {{ request('stock_status') == 'on_backorder' ? 'selected' : '' }}>On Backorder</option>
                </select>
                
                {{-- Price Range --}}
                <div class="flex items-center gap-2">
                    <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min Price"
                           class="w-full px-3 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400"
                           style="color:#0f172a;">
                    <span class="text-stone-400">-</span>
                    <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max Price"
                           class="w-full px-3 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400"
                           style="color:#0f172a;">
                </div>
                
                {{-- Dynamic Filter Attributes --}}
                @foreach($filterAttributes as $attribute)
                <select name="filter_{{ $attribute->name }}"
                        class="px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 bg-white"
                        style="color:#0f172a;">
                    <option value="">All {{ $attribute->display_name }}</option>
                    @foreach($attribute->values as $value)
                    <option value="{{ $value->value }}" {{ request('filter_' . $attribute->name) == $value->value ? 'selected' : '' }}>{{ $value->display_value }}</option>
                    @endforeach
                </select>
                @endforeach
            </div>
        </div>
    </form>
</div>

{{-- Bulk Actions Bar --}}
<div id="bulkActionsBar" class="hidden bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-amber-900">
                <span id="selectedCount">0</span> products selected
            </span>
            <button type="button" id="selectAllBtn" onclick="toggleSelectAllMode()" class="text-sm text-amber-700 hover:text-amber-900 underline">
                Select all {{ $products->total() }}
            </button>
            <button type="button" id="clearSelectionBtn" onclick="clearSelection()" class="text-sm text-amber-700 hover:text-amber-900 underline hidden">
                Clear selection
            </button>
        </div>
        <div class="flex items-center gap-2">
            <form id="bulkStatusForm" action="{{ route('admin.products.bulk-toggle-status') }}" method="POST" class="inline-flex">
                @csrf
                <input type="hidden" name="product_ids" id="bulkStatusProductIds">
                <input type="hidden" name="select_all" id="bulkStatusSelectAll" value="0">
                <input type="hidden" name="status" id="bulkStatusValue">
                <button type="button" onclick="bulkAction('activate')" class="px-3 py-1.5 text-xs font-medium rounded-md bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                    Activate
                </button>
                <button type="button" onclick="bulkAction('deactivate')" class="px-3 py-1.5 text-xs font-medium rounded-md bg-stone-100 text-stone-700 hover:bg-stone-200 transition-colors">
                    Deactivate
                </button>
            </form>
            
            <form id="bulkEditForm" action="{{ route('admin.products.bulk-edit') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="product_ids" id="bulkEditProductIds">
                <input type="hidden" name="select_all" id="bulkEditSelectAll" value="0">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                    Bulk Edit
                </button>
            </form>
            
            <form id="bulkDeleteForm" action="{{ route('admin.products.bulk-destroy') }}" method="POST" class="inline" onsubmit="return confirmBulkDelete()">
                @csrf @method('DELETE')
                <input type="hidden" name="product_ids" id="bulkDeleteProductIds">
                <input type="hidden" name="select_all" id="bulkDeleteSelectAll" value="0">
                <input type="hidden" name="confirm_delete_all" id="bulkDeleteConfirmAll" value="0">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>
    <div id="selectAllMessage" class="hidden mt-2 pt-2 border-t border-amber-100">
        <span class="text-sm text-amber-800">
            ✅ All <strong>{{ $products->total() }}</strong> products on all pages are selected
        </span>
    </div>
</div>

@if($view === 'grid')
{{-- ══════════════════════════════════════════
     GRID VIEW
  ══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @forelse($products as $product)
    <div class="group bg-white rounded-xl border border-stone-200 overflow-hidden hover:shadow-md transition-all duration-200 relative">
        {{-- Bulk Selection Checkbox --}}
        <div class="absolute top-3 left-3 z-20">
            <input type="checkbox" value="{{ $product->id }}" 
                   class="product-checkbox w-5 h-5 rounded border-stone-300 text-amber-600 focus:ring-amber-500"
                   onchange="toggleProductSelection({{ $product->id }}, this)">
        </div>
        
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
                <h3 class="font-medium text-sm leading-snug" style="color:#0f172a;">{{ Str::limit($product->display_name, 40) }}</h3>
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
                <th class="px-5 py-3.5 w-10">
                    <input type="checkbox" id="selectAllCheckbox" 
                           class="w-4 h-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500"
                           onchange="toggleSelectAll(this)">
                </th>
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
                    <input type="checkbox" value="{{ $product->id }}" 
                           class="product-checkbox w-4 h-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500"
                           onchange="toggleProductSelection({{ $product->id }}, this)">
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-stone-200" style="background:#f8fafc;">
                            <img src="{{ $product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-sm hover:underline" style="color:#0f172a;">{{ Str::limit($product->display_name, 35) }}</a>
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
                <td colspan="8" class="px-5 py-12 text-center" style="font-size:14px; color:#94a3b8;">
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

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold" style="color:#0f172a;">Import Products</h3>
            <button type="button" onclick="const m=document.getElementById('importModal');m.classList.add('hidden');m.classList.remove('flex');" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.products.import-csv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           class="w-full px-3 py-2 border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <p class="text-xs text-stone-500 mt-1">Upload a CSV file with product data</p>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition-colors">
                        Import Products
                    </button>
                    <button type="button" onclick="const m=document.getElementById('importModal');m.classList.add('hidden');m.classList.remove('flex');" class="px-4 py-2.5 border border-stone-200 text-stone-700 rounded-lg font-medium hover:bg-stone-50 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
        <div class="mt-4 pt-4 border-t border-stone-200">
            <p class="text-xs text-stone-500">Required columns: name, price</p>
            <p class="text-xs text-stone-500">Optional: sku, description, category, status, stock, material, origin, style</p>
        </div>
    </div>
</div>

<script>
// Store selected product IDs
let selectedProducts = new Set();
let selectAllMode = false;

function toggleSelectAllMode() {
    selectAllMode = true;
    document.getElementById('selectAllMessage').classList.remove('hidden');
    document.getElementById('selectAllBtn').classList.add('hidden');
    document.getElementById('clearSelectionBtn').classList.remove('hidden');
    
    // Check all visible checkboxes
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    
    // Update forms with select_all flag
    document.getElementById('bulkStatusSelectAll').value = '1';
    document.getElementById('bulkEditSelectAll').value = '1';
    document.getElementById('bulkDeleteSelectAll').value = '1';
    
    // Update count to show total
    document.getElementById('selectedCount').textContent = '{{ $products->total() }}';
    
    updateBulkActionsBar();
}

function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    filters.classList.toggle('hidden');
}

function toggleProductSelection(productId, checkbox) {
    if (checkbox.checked) {
        selectedProducts.add(productId.toString());
    } else {
        selectedProducts.delete(productId.toString());
    }
    updateBulkActionsBar();
    updateSelectAllCheckbox();
}

function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
        const productId = checkbox.value;
        if (masterCheckbox.checked) {
            selectedProducts.add(productId);
        } else {
            selectedProducts.delete(productId);
        }
    });
    updateBulkActionsBar();
}

function updateSelectAllCheckbox() {
    const masterCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
    
    if (masterCheckbox) {
        masterCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
        masterCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }
}

function updateBulkActionsBar() {
    const bar = document.getElementById('bulkActionsBar');
    const countSpan = document.getElementById('selectedCount');
    
    if (selectAllMode || selectedProducts.size > 0) {
        bar.classList.remove('hidden');
        if (selectAllMode) {
            countSpan.textContent = '{{ $products->total() }}';
        } else {
            countSpan.textContent = selectedProducts.size;
            
            // Update hidden inputs
            const productIds = Array.from(selectedProducts);
            document.getElementById('bulkStatusProductIds').value = JSON.stringify(productIds);
            document.getElementById('bulkEditProductIds').value = JSON.stringify(productIds);
            document.getElementById('bulkDeleteProductIds').value = JSON.stringify(productIds);
        }
    } else {
        bar.classList.add('hidden');
    }
}

function selectAllOnPage() {
    // Select all checkboxes on current page only
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        const productId = checkbox.value;
        selectedProducts.add(productId);
    });
    updateBulkActionsBar();
}

function clearSelection() {
    selectedProducts.clear();
    selectAllMode = false;
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset select all UI
    document.getElementById('selectAllMessage').classList.add('hidden');
    document.getElementById('selectAllBtn').classList.remove('hidden');
    document.getElementById('clearSelectionBtn').classList.add('hidden');
    
    // Reset select_all flags
    document.getElementById('bulkStatusSelectAll').value = '0';
    document.getElementById('bulkEditSelectAll').value = '0';
    document.getElementById('bulkDeleteSelectAll').value = '0';
    
    updateBulkActionsBar();
}

function bulkAction(action) {
    const form = document.getElementById('bulkStatusForm');
    const statusInput = document.getElementById('bulkStatusValue');
    
    if (action === 'activate') {
        statusInput.value = 'active';
    } else if (action === 'deactivate') {
        statusInput.value = 'inactive';
    }
    
    form.submit();
}

// Initialize checkboxes on page load
document.addEventListener('DOMContentLoaded', function() {
    // Restore selection state for visible checkboxes
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        if (selectedProducts.has(checkbox.value)) {
            checkbox.checked = true;
        }
    });
    updateBulkActionsBar();
});

function confirmBulkDelete() {
    const selectAll = document.getElementById('bulkDeleteSelectAll').value === '1';
    const totalProducts = {{ $products->total() }};
    if (selectAll && totalProducts > 1) {
        if (!confirm('WARNING: You are about to delete ALL ' + totalProducts + ' products. This cannot be undone. Continue?')) {
            return false;
        }
        document.getElementById('bulkDeleteConfirmAll').value = '1';
    } else {
        if (!confirm('Delete selected products?')) {
            return false;
        }
    }
    return true;
}
</script>
@endsection
