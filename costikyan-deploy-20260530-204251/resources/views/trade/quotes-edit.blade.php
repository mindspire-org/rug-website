@extends('layouts.trade')
@section('title', 'Edit Quote')

@section('trade-content')

<div class="mb-6">
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Edit Quote</h1>
    <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">{{ $quote->quote_number }}</p>
</div>

<div class="bg-white border border-stone-200 rounded-lg p-6 max-w-2xl">
    <form action="{{ route('trade.portal.quotes.update', $quote) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Project</label>
            <select name="project_id" required class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500 bg-white">
                @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ old('project_id', $quote->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }} — {{ $project->client_name }}</option>
                @endforeach
            </select>
            @error('project_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Quote Number</label>
            <input type="text" name="quote_number" value="{{ old('quote_number', $quote->quote_number) }}" required
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500">
            @error('quote_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Number of Items</label>
            <input type="number" name="items_count" value="{{ old('items_count', $quote->items_count) }}" required min="1"
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500">
            @error('items_count')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Total ($)</label>
            <input type="number" step="0.01" name="total" value="{{ old('total', $quote->total) }}" required min="0"
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500">
            @error('total')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Status</label>
            <select name="status" class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500 bg-white">
                <option value="draft" {{ $quote->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $quote->status === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="approved" {{ $quote->status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="expired" {{ $quote->status === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90 transition-opacity" style="background:#121212;">
                Save Changes
            </button>
            <a href="{{ route('trade.portal.quotes') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium border border-stone-200 hover:bg-stone-50 transition-colors" style="color:#374151;">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
