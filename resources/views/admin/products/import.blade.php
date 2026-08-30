@extends('layouts.admin')
@section('title', 'Import Products')

@section('admin-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Bulk Import Products</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Upload a CSV file to import products in bulk</p>
    </div>
    <a href="{{ route('admin.products.import.template') }}"
       class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 text-xs font-medium text-stone-700 hover:bg-stone-50 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Download Template
    </a>
</div>

<div class="bg-white border border-stone-200 rounded-lg p-6 max-w-2xl mb-6">
    <form action="{{ route('admin.products.import.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">CSV File</label>
            <input type="file" name="csv_file" accept=".csv,.txt" required
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500">
            @error('csv_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6 p-4 rounded-lg" style="background:#f8fafc;">
            <p style="font-size:12px; font-weight:600; color:#0f172a; margin-bottom:8px;">Required Columns</p>
            <p style="font-size:12px; color:#64748b; line-height:1.6;">
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">name</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">price</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">category_id</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">material</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">origin</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">style</code>
                <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">stock</code>
            </p>
            <p style="font-size:12px; color:#64748b; margin-top:8px;">Optional: <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">slug</code> <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">description</code> <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">sale_price</code> <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; font-size:11px;">image_url</code></p>
        </div>

        <div class="mb-6">
            <p style="font-size:12px; font-weight:600; color:#0f172a; margin-bottom:4px;">Available Categories</p>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                <span style="font-size:11px; background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:4px;">{{ $cat->id }} — {{ $cat->name }}</span>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90 transition-opacity" style="background:#0f172a;">
                Import Products
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium border border-stone-200 hover:bg-stone-50 transition-colors" style="color:#374151;">
                Cancel
            </a>
        </div>
    </form>
</div>

@if(session('import_errors'))
<div class="bg-white border border-red-200 rounded-lg p-5 max-w-2xl">
    <p style="font-size:13px; font-weight:600; color:#b91c1c; margin-bottom:8px;">Import Errors</p>
    <ul class="space-y-1">
        @foreach(session('import_errors') as $err)
        <li style="font-size:12px; color:#991b1b;">{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

@endsection
