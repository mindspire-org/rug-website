@extends('layouts.trade')
@section('title', 'Projects')

@section('trade-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Projects</h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Manage your client projects and rug selections</p>
    </div>
    <button class="flex items-center gap-2 text-white rounded"
            style="background:#121212; padding:10px 18px; font-size:14px; font-weight:500;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        New Project
    </button>
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

{{-- Table --}}
<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
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
            @php
            $projects = [
                ['name'=>'Sultanabad Grand Series',   'client'=>'James Park',    'room'=>'Master Suite', 'rugs'=>2, 'status'=>'Active',    'value'=>9200],
                ['name'=>'Tabriz Heritage Collection','client'=>'Sarah Mitchell','room'=>'Living Room',  'rugs'=>4, 'status'=>'Active',    'value'=>18400],
                ['name'=>'Sultanabad Grand Series',   'client'=>'James Park',    'room'=>'Master Suite', 'rugs'=>2, 'status'=>'Active',    'value'=>9200],
                ['name'=>'Tabriz Heritage Collection','client'=>'James Park',    'room'=>'Master Suite', 'rugs'=>2, 'status'=>'Archived',  'value'=>9200],
                ['name'=>'Agra Imperial Runner',      'client'=>'The Whites',    'room'=>'Multiple',     'rugs'=>6, 'status'=>'Active',    'value'=>32100],
                ['name'=>'Ziegler Modern Series',     'client'=>'James Park',    'room'=>'Master Suite', 'rugs'=>2, 'status'=>'Archived',  'value'=>9200],
                ['name'=>'Agra Imperial Runner',      'client'=>'The Whites',    'room'=>'Multiple',     'rugs'=>6, 'status'=>'Active',    'value'=>32100],
                ['name'=>'Sultanabad Grand Series',   'client'=>'James Park',    'room'=>'Master Suite', 'rugs'=>2, 'status'=>'Active',    'value'=>9200],
                ['name'=>'Tabriz Heritage Collection','client'=>'Elena Rossi',   'room'=>'Dining Room',  'rugs'=>1, 'status'=>'Completed', 'value'=>5800],
                ['name'=>'Agra Imperial Runner',      'client'=>'The Whites',    'room'=>'Multiple',     'rugs'=>6, 'status'=>'Active',    'value'=>32100],
                ['name'=>'Heriz Medallion Collection','client'=>'Robert Chen',   'room'=>'Library',      'rugs'=>3, 'status'=>'Active',    'value'=>14600],
            ];
            $statusColors = [
                'Active'    => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
                'Archived'  => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
                'Completed' => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
            ];
            @endphp
            @foreach($projects as $p)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        <span style="font-size:14px; font-weight:500; color:#121212;">{{ $p['name'] }}</span>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p['client'] }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p['room'] }}</td>
                <td class="px-4 py-3 text-center" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p['rugs'] }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$p['status']] }}">
                        {{ $p['status'] }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($p['value']) }}</td>
                <td class="px-4 py-3 text-right">
                    <button class="text-stone-400 hover:text-stone-700" style="font-size:18px; line-height:1;">···</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
