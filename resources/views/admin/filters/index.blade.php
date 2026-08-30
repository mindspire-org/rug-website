@extends('layouts.admin')
@section('title', 'Collection Filters')

@section('admin-content')
<style>
    .filter-tab {
        display:flex; align-items:center; gap:8px;
        padding:10px 16px; border-radius:8px 8px 0 0;
        font-size:13px; font-weight:600; color:#94a3b8;
        cursor:pointer; transition:all 0.15s;
        border-bottom:2px solid transparent;
        white-space:nowrap;
    }
    .filter-tab:hover { color:#475569; background:rgba(0,0,0,0.02); }
    .filter-tab.active {
        color:#0f172a; border-bottom-color:#E8651A;
        background:#fff;
    }
    .filter-tab svg { width:16px; height:16px; flex-shrink:0; }
    .filter-panel { display:none; }
    .filter-panel.active { display:block; animation: fadeIn 0.2s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:translateY(0); } }
    .stat-card {
        background:#fff; border:1px solid #e5e7eb; border-radius:12px;
        padding:16px 20px; display:flex; align-items:center; gap:14px;
        transition:all 0.2s;
    }
    .stat-card:hover { border-color:#fbbf24; box-shadow:0 4px 12px rgba(251,191,36,0.08); }
    .stat-icon {
        width:42px; height:42px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        flex-shrink:0;
    }
    .stat-value { font-size:22px; font-weight:700; color:#0f172a; font-family:'Lusitana',serif; line-height:1; }
    .stat-label { font-size:11px; color:#94a3b8; font-weight:500; text-transform:uppercase; letter-spacing:0.04em; margin-top:3px; }
    .filter-card {
        background:#fff; border:1px solid #e5e7eb; border-radius:12px;
        overflow:hidden;
    }
    .filter-card-header {
        padding:18px 24px; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .filter-card-title {
        font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;
        display:flex; align-items:center; gap:8px;
    }
    .filter-card-body { padding:24px; }
    .color-swatch-preview {
        width:36px; height:36px; border-radius:8px;
        border:2px solid #fff; box-shadow:0 1px 3px rgba(0,0,0,0.12);
        cursor:pointer; transition:transform 0.15s;
    }
    .color-swatch-preview:hover { transform:scale(1.1); }
    .chip {
        display:inline-flex; align-items:center; gap:6px;
        padding:5px 12px; border-radius:20px;
        font-size:12px; font-weight:500;
        background:#f8fafc; border:1px solid #e2e8f0; color:#475569;
        transition:all 0.15s;
    }
    .chip-count {
        font-size:10px; font-weight:700; color:#94a3b8;
        background:#f1f5f9; border-radius:10px; padding:1px 7px;
    }
    .chip.has-products { border-color:#fbbf24; background:#fffbeb; color:#92400e; }
    .chip.has-products .chip-count { background:#fef3c7; color:#92400e; }
    .list-item {
        display:flex; align-items:center; gap:10px;
        padding:10px 14px; border-radius:8px;
        background:#f8fafc; border:1px solid #e2e8f0;
        transition:all 0.15s;
    }
    .list-item:hover { border-color:#cbd5e1; background:#f1f5f9; }
    .list-item .item-text { flex:1; font-size:13px; color:#334155; font-weight:500; }
    .list-item .item-count {
        font-size:11px; font-weight:600; color:#64748b;
        background:#e2e8f0; border-radius:10px; padding:2px 8px;
    }
    .list-item.has-products .item-count { background:#fef3c7; color:#92400e; }
    .list-item.has-products { border-color:#fde68a; background:#fffbeb; }
    .save-bar {
        position:sticky; bottom:0; left:0; right:0;
        background:rgba(255,255,255,0.95); backdrop-filter:blur(8px);
        border-top:1px solid #e5e7eb;
        padding:14px 24px; z-index:30;
        display:flex; align-items:center; justify-content:space-between;
    }
    .btn-primary {
        background:#E8651A; color:#fff; font-weight:600;
        padding:10px 28px; border-radius:8px; font-size:13px;
        transition:all 0.15s; border:none; cursor:pointer;
    }
    .btn-primary:hover { opacity:0.9; transform:translateY(-1px); box-shadow:0 4px 12px rgba(232,101,26,0.25); }
    .btn-secondary {
        background:#fff; color:#475569; font-weight:500;
        padding:10px 20px; border-radius:8px; font-size:13px;
        border:1px solid #e2e8f0; transition:all 0.15s; cursor:pointer;
    }
    .btn-secondary:hover { background:#f8fafc; border-color:#cbd5e1; }
    .section-badge {
        font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em;
        padding:3px 8px; border-radius:6px;
    }
    .input-field {
        width:100%; padding:9px 12px; font-size:13px;
        border:1px solid #e2e8f0; border-radius:8px;
        transition:all 0.15s; color:#0f172a;
    }
    .input-field:focus { outline:none; border-color:#E8651A; box-shadow:0 0 0 3px rgba(232,101,26,0.08); }
    .textarea-field {
        width:100%; padding:12px 14px; font-size:13px; font-family:'Courier New',monospace;
        border:1px solid #e2e8f0; border-radius:8px; line-height:1.8;
        transition:all 0.15s; color:#0f172a; min-height:140px;
    }
    .textarea-field:focus { outline:none; border-color:#E8651A; box-shadow:0 0 0 3px rgba(232,101,26,0.08); }
    .add-btn {
        width:100%; padding:9px; font-size:13px; font-weight:500;
        border:1px dashed #cbd5e1; border-radius:8px;
        color:#64748b; background:transparent; cursor:pointer;
        transition:all 0.15s;
    }
    .add-btn:hover { border-color:#E8651A; color:#E8651A; background:#fff7ed; }
    .remove-btn {
        width:28px; height:28px; border-radius:6px;
        display:flex; align-items:center; justify-content:center;
        color:#cbd5e1; cursor:pointer; transition:all 0.15s;
        border:none; background:transparent; flex-shrink:0;
    }
    .remove-btn:hover { color:#ef4444; background:#fef2f2; }
    .preview-section {
        background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border:1px solid #e2e8f0; border-radius:10px;
        padding:16px; margin-top:16px;
    }
    .preview-label {
        font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em;
        color:#94a3b8; margin-bottom:10px;
    }
</style>

{{-- Header --}}
<div class="mb-6">
    <div class="flex items-center gap-2 mb-2">
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium transition-colors hover:underline" style="color:#64748b;">Dashboard</a>
        <span class="text-stone-300">/</span>
        <span class="text-sm font-medium" style="color:#0f172a;">Filters</span>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 style="font-family:'Lusitana',serif; font-size:26px; font-weight:700; color:#0f172a;">Collection Filters</h1>
            <p style="font-size:13px; color:#94a3b8; margin-top:2px;">Manage the filter options shown in the shop sidebar &amp; product forms</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ $stats['total_products'] }} Products</span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm" style="animation:fadeIn 0.3s ease;">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff7ed;">
            <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['total_products'] }}</div>
            <div class="stat-label">Total Products</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;">
            <svg class="w-5 h-5" style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a2 2 0 002-2V5a2 2 0 00-2-2H7"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['with_material'] }}</div>
            <div class="stat-label">Have Material</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;">
            <svg class="w-5 h-5" style="color:#22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['with_style'] }}</div>
            <div class="stat-label">Have Pattern</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2;">
            <svg class="w-5 h-5" style="color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a2 2 0 002-2V5a2 2 0 00-2-2H7"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $stats['with_color'] }}</div>
            <div class="stat-label">Have Color Tag</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.filters.update') }}">
    @csrf
    @method('PUT')

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-1 border-b border-stone-200 mb-6 overflow-x-auto" style="background:#f8fafc; border-radius:12px 12px 0 0; padding:0 8px;">
        <div class="filter-tab active" data-tab="color" onclick="switchTab('color')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a2 2 0 002-2V5a2 2 0 00-2-2H7"/></svg>
            Colors
        </div>
        <div class="filter-tab" data-tab="pattern" onclick="switchTab('pattern')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            Pattern
        </div>
        <div class="filter-tab" data-tab="material" onclick="switchTab('material')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Material
        </div>
        <div class="filter-tab" data-tab="room" onclick="switchTab('room')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Room
        </div>
        <div class="filter-tab" data-tab="construction" onclick="switchTab('construction')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Construction
        </div>
        <div class="filter-tab" data-tab="size" onclick="switchTab('size')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3zM9 3v18M3 9h18"/></svg>
            Size
        </div>
        <div class="filter-tab" data-tab="availability" onclick="switchTab('availability')">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Availability
        </div>
    </div>

    {{-- ════ COLOR TAB ════ --}}
    <div class="filter-panel active" id="panel-color">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h8a2 2 0 002-2V5a2 2 0 00-2-2H7"/></svg>
                    Color Swatches
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['color']) }} groups</span>
            </div>
            <div class="filter-card-body">
                <div class="space-y-3" id="color-rows">
                    @foreach($options['color'] as $i => $c)
                    <div class="flex items-center gap-3 color-row">
                        <input type="color" name="color_hex[]" value="{{ $c['hex'] }}"
                               class="w-12 h-12 rounded-lg border-2 border-stone-200 cursor-pointer p-1">
                        <input type="text" name="color_name[]" value="{{ $c['name'] }}"
                               placeholder="Label e.g. Blues"
                               class="input-field flex-1">
                        @php $cnt = $counts['color'][$c['name']] ?? 0; @endphp
                        <span class="chip {{ $cnt > 0 ? 'has-products' : '' }}" style="flex-shrink:0;">
                            {{ $c['name'] }}
                            <span class="chip-count">{{ $cnt }}</span>
                        </span>
                        <button type="button" onclick="this.closest('.color-row').remove()" class="remove-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addColorRow()" class="add-btn mt-3">+ Add Color Group</button>

                {{-- Live Preview --}}
                <div class="preview-section">
                    <div class="preview-label">Shop Sidebar Preview</div>
                    <div class="flex flex-wrap gap-2" id="color-preview">
                        @foreach($options['color'] as $c)
                        <div class="flex items-center gap-2">
                            <div class="color-swatch-preview" style="background:{{ $c['hex'] }};"></div>
                            <span style="font-size:12px; color:#475569; font-weight:500;">{{ $c['name'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ PATTERN TAB ════ --}}
    <div class="filter-panel" id="panel-pattern">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Pattern / Style Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['pattern']) }} options</span>
            </div>
            <div class="filter-card-body">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Options (one per line)</label>
                        <textarea name="pattern_items" class="textarea-field">{{ implode("\n", $options['pattern']) }}</textarea>
                        <p style="font-size:11px; color:#94a3b8; margin-top:6px;">These appear as filter chips in the shop sidebar and as dropdown options in the product form.</p>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Current Options &amp; Product Counts</label>
                        <div class="space-y-2">
                            @foreach($options['pattern'] as $p)
                            @php $cnt = $counts['pattern'][$p] ?? 0; @endphp
                            <div class="list-item {{ $cnt > 0 ? 'has-products' : '' }}">
                                <span class="item-text">{{ $p }}</span>
                                <span class="item-count">{{ $cnt }} products</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ MATERIAL TAB ════ --}}
    <div class="filter-panel" id="panel-material">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Material Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['material']) }} options</span>
            </div>
            <div class="filter-card-body">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Options (one per line)</label>
                        <textarea name="material_items" class="textarea-field">{{ implode("\n", $options['material']) }}</textarea>
                        <p style="font-size:11px; color:#94a3b8; margin-top:6px;">These appear as filter chips in the shop sidebar and as dropdown options in the product form.</p>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Current Options &amp; Product Counts</label>
                        <div class="space-y-2">
                            @foreach($options['material'] as $m)
                            @php $cnt = $counts['material'][$m] ?? 0; @endphp
                            <div class="list-item {{ $cnt > 0 ? 'has-products' : '' }}">
                                <span class="item-text">{{ $m }}</span>
                                <span class="item-count">{{ $cnt }} products</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ ROOM TAB ════ --}}
    <div class="filter-panel" id="panel-room">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Room Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['room']) }} rooms</span>
            </div>
            <div class="filter-card-body">
                <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Options (one per line)</label>
                <textarea name="room_items" class="textarea-field">{{ implode("\n", $options['room']) }}</textarea>
                <div class="preview-section">
                    <div class="preview-label">Shop Sidebar Preview</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($options['room'] as $r)
                        <span class="chip">{{ $r }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ CONSTRUCTION TAB ════ --}}
    <div class="filter-panel" id="panel-construction">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Construction Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['construction']) }} methods</span>
            </div>
            <div class="filter-card-body">
                <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Options (one per line)</label>
                <textarea name="construction_items" class="textarea-field" style="min-height:120px;">{{ implode("\n", $options['construction']) }}</textarea>
                <div class="preview-section">
                    <div class="preview-label">Shop Sidebar Preview</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($options['construction'] as $c)
                        <span class="chip">{{ $c }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ SIZE TAB ════ --}}
    <div class="filter-panel" id="panel-size">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3zM9 3v18M3 9h18"/></svg>
                    Size Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['size']) }} sizes</span>
            </div>
            <div class="filter-card-body">
                <label style="display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:8px;">Options (one per line, e.g. 6×9)</label>
                <textarea name="size_items" class="textarea-field" style="min-height:120px;">{{ implode("\n", $options['size']) }}</textarea>
                <div class="preview-section">
                    <div class="preview-label">Shop Sidebar Preview</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($options['size'] as $s)
                        <span class="chip">{{ $s }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ AVAILABILITY TAB ════ --}}
    <div class="filter-panel" id="panel-availability">
        <div class="filter-card">
            <div class="filter-card-header">
                <div class="filter-card-title">
                    <svg class="w-5 h-5" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Availability / Timeline Options
                </div>
                <span class="section-badge" style="background:#fff7ed; color:#E8651A;">{{ count($options['availability']) }} options</span>
            </div>
            <div class="filter-card-body">
                <div class="space-y-3" id="avail-rows">
                    @foreach($options['availability'] as $i => $a)
                    <div class="flex items-center gap-3 avail-row">
                        <div class="flex-1">
                            <label style="display:block; font-size:11px; font-weight:500; color:#94a3b8; margin-bottom:4px;">Filter Value</label>
                            <input type="text" name="avail_value[]" value="{{ $a['value'] }}"
                                   placeholder="e.g. In Stock" class="input-field">
                        </div>
                        <div class="flex-1">
                            <label style="display:block; font-size:11px; font-weight:500; color:#94a3b8; margin-bottom:4px;">Display Label</label>
                            <input type="text" name="avail_label[]" value="{{ $a['label'] }}"
                                   placeholder="e.g. In Stock (2 Weeks)" class="input-field">
                        </div>
                        <button type="button" onclick="this.closest('.avail-row').remove()" class="remove-btn" style="margin-top:18px;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addAvailRow()" class="add-btn mt-3">+ Add Availability Option</button>
                <div class="preview-section">
                    <div class="preview-label">Shop Sidebar Preview</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($options['availability'] as $a)
                        <span class="chip">{{ $a['label'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Save Bar --}}
    <div class="save-bar mt-6">
        <div class="flex items-center gap-2" style="font-size:12px; color:#94a3b8;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Changes apply to the shop sidebar &amp; product forms instantly after saving.
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save All Filters
            </button>
        </div>
    </div>
</form>

<script>
function switchTab(tab) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.filter-panel').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    document.getElementById(`panel-${tab}`).classList.add('active');
}

function addColorRow() {
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 color-row';
    row.innerHTML = `
        <input type="color" name="color_hex[]" value="#cccccc"
               class="w-12 h-12 rounded-lg border-2 border-stone-200 cursor-pointer p-1">
        <input type="text" name="color_name[]" placeholder="Label e.g. Blues"
               class="input-field flex-1">
        <button type="button" onclick="this.closest('.color-row').remove()" class="remove-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    document.getElementById('color-rows').appendChild(row);
}

function addAvailRow() {
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 avail-row';
    row.innerHTML = `
        <div class="flex-1">
            <label style="display:block; font-size:11px; font-weight:500; color:#94a3b8; margin-bottom:4px;">Filter Value</label>
            <input type="text" name="avail_value[]" placeholder="e.g. In Stock" class="input-field">
        </div>
        <div class="flex-1">
            <label style="display:block; font-size:11px; font-weight:500; color:#94a3b8; margin-bottom:4px;">Display Label</label>
            <input type="text" name="avail_label[]" placeholder="e.g. In Stock (2 Weeks)" class="input-field">
        </div>
        <button type="button" onclick="this.closest('.avail-row').remove()" class="remove-btn" style="margin-top:18px;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    document.getElementById('avail-rows').appendChild(row);
}
</script>
@endsection
