@extends('layouts.trade')
@section('title', 'Projects')

@section('trade-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Projects</h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Manage your client projects and rug selections</p>
    </div>
    <a href="{{ route('trade.portal.projects.create') }}" class="flex items-center gap-2 text-white rounded"
            style="background:#121212; padding:10px 18px; font-size:14px; font-weight:500; text-decoration:none;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        New Project
    </a>
</div>

{{-- Search + filter --}}
<div class="flex gap-3 mb-5">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search projects or clients..."
               class="w-full focus:outline-none pl-9 pr-4 py-2.5 bg-white border border-stone-200 rounded text-sm">
    </div>
    <select class="focus:outline-none bg-white border border-stone-200 rounded px-4 py-2.5 text-sm" style="min-width:80px;">
        <option>All</option>
        <option>Active</option>
        <option>Archived</option>
        <option>Completed</option>
    </select>
</div>

@php
$statusColors = [
    'active'    => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
    'archived'  => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
    'completed' => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
];
@endphp

{{-- Table --}}
<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    @if($projects->count())
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PROJECT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CLIENT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ROOM</th>
                <th class="px-4 py-3 text-center" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUGS</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">VALUE</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $p)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        <span style="font-size:14px; font-weight:500; color:#121212;">{{ $p->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p->client_name }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p->room ?? '—' }}</td>
                <td class="px-4 py-3 text-center" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p->rugs_count }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$p->status] ?? $statusColors['active'] }}">
                        {{ ucfirst($p->status) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($p->total_value) }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('trade.portal.projects.edit', $p) }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-stone-900 hover:border-stone-300 transition-colors" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('trade.portal.projects.destroy', $p) }}" method="POST" onsubmit="return confirm('Delete this project?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-stone-200 text-stone-500 hover:text-red-600 hover:border-red-200 transition-colors" title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-10 h-10 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No projects yet. Start by creating your first project.</p>
    </div>
    @endif
</div>

@endsection
