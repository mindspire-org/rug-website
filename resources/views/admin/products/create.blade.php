@extends('layouts.admin')
@section('title', 'Add Product')

@section('admin-content')
<div class="flex items-center gap-3 mb-8">
    <a href="{{ route('admin.products.index') }}" class="text-stone-400 hover:text-stone-900">←</a>
    <h1 class="font-serif text-2xl font-bold text-stone-900">Add Product</h1>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="max-w-3xl">
    @csrf
    @include('admin.products._form')
    <div class="flex gap-3 mt-8">
        <button type="submit" class="btn-dark px-8">Create Product</button>
        <a href="{{ route('admin.products.index') }}" class="btn-outline-dark px-6">Cancel</a>
    </div>
</form>
@endsection
