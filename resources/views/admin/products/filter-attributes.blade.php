@extends('layouts.admin')
@section('title', 'Product Filter Attributes')

@section('admin-content')

<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#0f172a;">Product Filter Attributes</h1>
            <p style="font-size:13px; color:#64748b; margin-top:3px;">Manage custom filters for products</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-stone-700 font-medium text-sm transition-all hover:bg-stone-100 border border-stone-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Products
        </a>
    </div>
</div>

{{-- Create New Attribute --}}
<div class="bg-white rounded-xl border border-stone-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-stone-800 mb-4">Create New Filter Attribute</h2>
    <form action="{{ route('admin.product-filters.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-2">Name (internal)</label>
            <input type="text" name="name" required placeholder="e.g., pile_height"
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400"
                   pattern="[a-z0-9_]+" title="Only lowercase letters, numbers, and underscores">
            <p class="text-xs text-stone-500 mt-1">e.g., pile_height, weave_type</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-2">Display Name</label>
            <input type="text" name="display_name" required placeholder="e.g., Pile Height"
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
            <p class="text-xs text-stone-500 mt-1">e.g., Pile Height, Weave Type</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-2">Type</label>
            <select name="type" required
                    class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                <option value="select">Single Select</option>
                <option value="multiselect">Multi Select</option>
                <option value="text">Text Input</option>
                <option value="number">Number Input</option>
            </select>
        </div>
        <button type="submit" 
                class="px-5 py-2.5 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition-colors">
            Create Attribute
        </button>
    </form>
</div>

{{-- Existing Attributes --}}
<div class="space-y-4">
    @forelse($attributes as $attribute)
    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="p-6 border-b border-stone-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-stone-800">{{ $attribute->display_name }}</h3>
                    <p class="text-sm text-stone-500">Name: {{ $attribute->name }} | Type: {{ $attribute->type }} | Values: {{ $attribute->values->count() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $attribute->is_active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-600' }}">
                        {{ $attribute->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Values List --}}
        <div class="p-6">
            <h4 class="text-sm font-semibold text-stone-700 mb-3">Filter Values</h4>
            
            @if($attribute->values->count() > 0)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($attribute->values as $value)
                <span class="px-3 py-1.5 bg-stone-100 text-stone-700 rounded-lg text-sm">
                    {{ $value->display_value }}
                    <span class="text-stone-400 text-xs">({{ $value->value }})</span>
                </span>
                @endforeach
            </div>
            @else
            <p class="text-sm text-stone-500 mb-4">No values added yet.</p>
            @endif
            
            {{-- Add Value Form --}}
            <form action="{{ route('admin.product-filters.store-value', $attribute) }}" method="POST" class="flex gap-3 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-xs font-medium text-stone-600 mb-1">Value (internal)</label>
                    <input type="text" name="value" required placeholder="e.g., high"
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-stone-600 mb-1">Display Value</label>
                    <input type="text" name="display_value" required placeholder="e.g., High Pile"
                           class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                </div>
                <button type="submit" 
                        class="px-4 py-2 bg-stone-800 text-white rounded-lg text-sm font-medium hover:bg-stone-900 transition-colors">
                    Add Value
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-stone-200 p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center bg-stone-100">
            <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
        </div>
        <p class="text-stone-500">No filter attributes created yet.</p>
        <p class="text-sm text-stone-400 mt-1">Create your first filter attribute above.</p>
    </div>
    @endforelse
</div>

{{-- Help Section --}}
<div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
    <h3 class="text-sm font-semibold text-blue-900 mb-2">How Filter Attributes Work</h3>
    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
        <li><strong>Filter Attributes</strong> are custom properties you can assign to products (e.g., Pile Height, Weave Type, Material Grade)</li>
        <li><strong>Filter Values</strong> are the specific options within each attribute (e.g., "High", "Low", "Medium" for Pile Height)</li>
        <li>Once created, these filters will appear in the product filter sidebar on your shop page</li>
        <li>You can assign multiple filter values to each product when editing or creating products</li>
        <li>Filter attributes make it easier for customers to find products that match their specific requirements</li>
    </ul>
</div>

@endsection
