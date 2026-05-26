@extends('layouts.admin')
@section('title', 'Manage Filters')

@section('admin-content')
<div class="flex items-center justify-between mb-8">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Collection Filters</h1>
    <p class="text-xs text-stone-400">Edit options shown in the shop sidebar</p>
</div>

@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded text-sm">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('admin.filters.update') }}">
    @csrf
    @method('PUT')

    <div class="space-y-8">

        {{-- COLOR SWATCHES --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-4">Color Swatches</h2>
            <div class="space-y-3" id="color-rows">
                @foreach($options['color'] as $i => $c)
                <div class="flex items-center gap-3 color-row">
                    <input type="color" name="color_hex[]" value="{{ $c['hex'] }}"
                           class="w-10 h-10 rounded border border-stone-200 cursor-pointer p-0.5">
                    <input type="text" name="color_name[]" value="{{ $c['name'] }}"
                           placeholder="Label e.g. Blues"
                           class="flex-1 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
                    <button type="button" onclick="this.closest('.color-row').remove()"
                            class="text-stone-400 hover:text-red-500 text-lg leading-none">×</button>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addColorRow()"
                    class="mt-3 text-sm text-stone-500 hover:text-stone-900 border border-dashed border-stone-300 px-3 py-1.5 rounded w-full">
                + Add Color
            </button>
        </div>

        {{-- PATTERN / STYLE --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-2">Pattern / Style Options</h2>
            <p class="text-xs text-stone-400 mb-3">One option per line</p>
            <textarea name="pattern_items" rows="6"
                      class="w-full border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500 font-mono">{{ implode("\n", $options['pattern']) }}</textarea>
        </div>

        {{-- MATERIAL --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-2">Material Options</h2>
            <p class="text-xs text-stone-400 mb-3">One option per line</p>
            <textarea name="material_items" rows="6"
                      class="w-full border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500 font-mono">{{ implode("\n", $options['material']) }}</textarea>
        </div>

        {{-- ROOM --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-2">Room Options</h2>
            <p class="text-xs text-stone-400 mb-3">One option per line</p>
            <textarea name="room_items" rows="6"
                      class="w-full border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500 font-mono">{{ implode("\n", $options['room']) }}</textarea>
        </div>

        {{-- CONSTRUCTION --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-2">Construction Options</h2>
            <p class="text-xs text-stone-400 mb-3">One option per line</p>
            <textarea name="construction_items" rows="5"
                      class="w-full border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500 font-mono">{{ implode("\n", $options['construction']) }}</textarea>
        </div>

        {{-- SIZE --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-2">Size Options</h2>
            <p class="text-xs text-stone-400 mb-3">One option per line (e.g. 6×9)</p>
            <textarea name="size_items" rows="5"
                      class="w-full border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500 font-mono">{{ implode("\n", $options['size']) }}</textarea>
        </div>

        {{-- AVAILABILITY --}}
        <div class="bg-white border border-stone-200 rounded p-6">
            <h2 class="font-semibold text-stone-900 mb-4">Availability / Timeline Options</h2>
            <div class="space-y-3" id="avail-rows">
                @foreach($options['availability'] as $i => $a)
                <div class="flex items-center gap-3 avail-row">
                    <input type="text" name="avail_value[]" value="{{ $a['value'] }}"
                           placeholder="Filter value (e.g. In Stock)"
                           class="w-1/3 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
                    <input type="text" name="avail_label[]" value="{{ $a['label'] }}"
                           placeholder="Display label (e.g. In Stock (2 Weeks))"
                           class="flex-1 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
                    <button type="button" onclick="this.closest('.avail-row').remove()"
                            class="text-stone-400 hover:text-red-500 text-lg leading-none">×</button>
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addAvailRow()"
                    class="mt-3 text-sm text-stone-500 hover:text-stone-900 border border-dashed border-stone-300 px-3 py-1.5 rounded w-full">
                + Add Option
            </button>
        </div>

    </div>

    <div class="mt-8 flex gap-3">
        <button type="submit"
                class="bg-stone-900 text-white px-8 py-2.5 rounded text-sm font-medium hover:bg-stone-700 transition-colors">
            Save Filter Options
        </button>
        <a href="{{ route('admin.dashboard') }}"
           class="px-6 py-2.5 border border-stone-300 rounded text-sm text-stone-600 hover:bg-stone-50 transition-colors">
            Cancel
        </a>
    </div>
</form>

<script>
function addColorRow() {
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 color-row';
    row.innerHTML = `
        <input type="color" name="color_hex[]" value="#cccccc"
               class="w-10 h-10 rounded border border-stone-200 cursor-pointer p-0.5">
        <input type="text" name="color_name[]" placeholder="Label e.g. Blues"
               class="flex-1 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
        <button type="button" onclick="this.closest('.color-row').remove()"
                class="text-stone-400 hover:text-red-500 text-lg leading-none">×</button>
    `;
    document.getElementById('color-rows').appendChild(row);
}
function addAvailRow() {
    const row = document.createElement('div');
    row.className = 'flex items-center gap-3 avail-row';
    row.innerHTML = `
        <input type="text" name="avail_value[]" placeholder="Filter value"
               class="w-1/3 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
        <input type="text" name="avail_label[]" placeholder="Display label"
               class="flex-1 border border-stone-200 rounded px-3 py-2 text-sm focus:outline-none focus:border-stone-500">
        <button type="button" onclick="this.closest('.avail-row').remove()"
                class="text-stone-400 hover:text-red-500 text-lg leading-none">×</button>
    `;
    document.getElementById('avail-rows').appendChild(row);
}
</script>
@endsection
