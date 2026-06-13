@extends('layouts.admin')
@section('title', 'ZIP Pricing')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">ZIP-Code Shipping Pricing</h1>
        <p class="text-sm text-stone-500 mt-1">Define shipping prices by ZIP-code range. When a customer enters a ZIP at checkout, the matching range's price is applied.</p>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-2.5 rounded bg-green-50 border border-green-200 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="mb-4 px-4 py-2.5 rounded bg-red-50 border border-red-200 text-red-700 text-sm">{{ $errors->first() }}</div>
    @endif

    {{-- Add new range --}}
    <div class="bg-white border border-stone-200 rounded-lg p-5 mb-6">
        <h2 class="text-sm font-semibold text-stone-900 mb-4">Add a ZIP range</h2>
        <form action="{{ route('admin.zip-prices.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-stone-500 mb-1">Label (optional)</label>
                <input type="text" name="label" placeholder="e.g. Northeast" class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">ZIP from</label>
                <input type="text" name="zip_start" placeholder="10000" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">ZIP to</label>
                <input type="text" name="zip_end" placeholder="19999" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Price ($)</label>
                <input type="number" step="0.01" min="0" name="price" placeholder="250" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-stone-900 text-white text-sm font-medium rounded px-4 py-2 hover:bg-stone-800">Add range</button>
        </form>
    </div>

    {{-- Existing ranges --}}
    <div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-stone-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="text-left px-4 py-3">Label</th>
                    <th class="text-left px-4 py-3">ZIP from</th>
                    <th class="text-left px-4 py-3">ZIP to</th>
                    <th class="text-left px-4 py-3">Price</th>
                    <th class="text-left px-4 py-3">Active</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($zipPrices as $zp)
                <tr>
                    <form action="{{ route('admin.zip-prices.update', $zp) }}" method="POST" id="zp-{{ $zp->id }}">@csrf @method('PUT')</form>
                    <td class="px-4 py-2"><input form="zp-{{ $zp->id }}" name="label" value="{{ $zp->label }}" class="w-full border border-stone-200 rounded px-2 py-1.5"></td>
                    <td class="px-4 py-2"><input form="zp-{{ $zp->id }}" name="zip_start" value="{{ $zp->zip_start }}" class="w-24 border border-stone-200 rounded px-2 py-1.5"></td>
                    <td class="px-4 py-2"><input form="zp-{{ $zp->id }}" name="zip_end" value="{{ $zp->zip_end }}" class="w-24 border border-stone-200 rounded px-2 py-1.5"></td>
                    <td class="px-4 py-2"><input form="zp-{{ $zp->id }}" name="price" type="number" step="0.01" value="{{ $zp->price }}" class="w-24 border border-stone-200 rounded px-2 py-1.5"></td>
                    <td class="px-4 py-2"><input form="zp-{{ $zp->id }}" name="active" type="checkbox" value="1" {{ $zp->active ? 'checked' : '' }}></td>
                    <td class="px-4 py-2 text-right whitespace-nowrap">
                        <button form="zp-{{ $zp->id }}" type="submit" class="text-xs font-medium text-orange-600 hover:underline mr-3">Save</button>
                        <form action="{{ route('admin.zip-prices.destroy', $zp) }}" method="POST" class="inline" onsubmit="return confirm('Remove this ZIP range?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-stone-400">No ZIP ranges yet. Add one above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
