@extends('layouts.trade')
@section('title', 'New Project')

@section('trade-content')

<div class="mb-6">
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">New Project</h1>
    <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Create a new client project</p>
</div>

<div class="bg-white border border-stone-200 rounded-lg p-6 max-w-2xl">
    <form action="{{ route('trade.portal.projects.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Project Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500"
                   placeholder="e.g. Park Avenue Residence">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Client Name</label>
            <input type="text" name="client_name" value="{{ old('client_name') }}" required
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500"
                   placeholder="e.g. Sarah Mitchell">
            @error('client_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-stone-700 mb-1.5">Room (optional)</label>
            <input type="text" name="room" value="{{ old('room') }}"
                   class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-500"
                   placeholder="e.g. Living Room">
            @error('room')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2.5 rounded-lg text-white text-sm font-medium hover:opacity-90 transition-opacity" style="background:#121212;">
                Create Project
            </button>
            <a href="{{ route('trade.portal.projects') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium border border-stone-200 hover:bg-stone-50 transition-colors" style="color:#374151;">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
