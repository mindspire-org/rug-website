@extends('layouts.admin')
@section('title', 'Add Category')

@section('admin-content')
<div class="flex items-center gap-3 mb-8">
    <a href="{{ route('admin.categories.index') }}" class="text-stone-400 hover:text-stone-900">←</a>
    <h1 class="font-serif text-2xl font-bold">Add Category</h1>
</div>
<form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="max-w-lg space-y-5">
    @csrf
    <div>
        <label class="form-label">Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
    </div>
    <div>
        <label class="form-label">Parent Category</label>
        <select name="parent_id" class="form-input">
            <option value="">— Top Level —</option>
            @foreach($parents as $p)
            <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-input resize-none">{{ old('description') }}</textarea>
    </div>
    <div>
        <label class="form-label">Image</label>
        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 file:border file:border-stone-300 file:text-sm file:bg-stone-50">
    </div>
    <div>
        <label class="form-label">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-input w-24">
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" checked class="rounded">
        <span class="text-sm text-stone-700">Active</span>
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn-dark px-8">Create Category</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-outline-dark px-6">Cancel</a>
    </div>
</form>
@endsection
