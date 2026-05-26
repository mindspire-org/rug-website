@extends('layouts.admin')
@section('title', 'Products')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn-dark text-sm px-4 py-2">+ Add Product</a>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products…" class="form-input w-64">
    <select name="category" class="form-input w-48">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-dark text-sm px-4 py-2.5">Filter</button>
    <a href="{{ route('admin.products.index') }}" class="btn-outline-dark text-sm px-4 py-2.5">Clear</a>
</form>

<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Product</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Category</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Stock</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($products as $product)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-stone-100 flex-shrink-0 overflow-hidden rounded">
                            <img src="{{ $product->primary_image_url }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-medium text-stone-900">{{ Str::limit($product->name, 35) }}</p>
                            <p class="text-xs text-stone-400">{{ $product->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-stone-600">{{ $product->category?->name ?? '—' }}</td>
                <td class="px-4 py-3 font-medium">${{ number_format($product->price, 0) }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $product->stock }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500' }}">{{ $product->status }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-xs text-stone-500 hover:text-stone-900">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-stone-400">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
