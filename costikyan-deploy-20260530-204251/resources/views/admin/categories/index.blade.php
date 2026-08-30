@extends('layouts.admin')
@section('title', 'Categories')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn-dark text-sm px-4 py-2">+ Add Category</a>
</div>
<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Name</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Parent</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($categories as $category)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium text-stone-900">{{ $category->name }}</td>
                <td class="px-4 py-3 text-stone-500">{{ $category->parent?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs text-stone-500 hover:text-stone-900">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-stone-400">No categories yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $categories->links() }}</div>
@endsection
