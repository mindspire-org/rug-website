@extends('layouts.trade')
@section('title', 'Sample Requests')

@section('trade-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Sample Requests</h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Request and track material samples</p>
    </div>
    <button class="flex items-center gap-2 text-white rounded"
            style="background:#121212; padding:10px 18px; font-size:14px; font-weight:500;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        Request Sample
    </button>
</div>

<div class="flex gap-3 mb-5">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search requests..." class="w-full focus:outline-none pl-9 pr-4 py-2.5 bg-white border border-stone-200 rounded text-sm">
    </div>
    <select class="focus:outline-none bg-white border border-stone-200 rounded px-4 py-2.5 text-sm" style="min-width:80px;">
        <option>All</option>
        <option>Pending</option>
        <option>Approved</option>
        <option>Shipped</option>
        <option>Delivered</option>
    </select>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">SAMPLE</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUG</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">COLOR</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TRACKING</th>
            </tr>
        </thead>
        <tbody>
            @php
            $samples = [
                ['num'=>'S-401','date'=>'Feb 28, 2026','rug'=>'Tabriz Heritage',   'color'=>'Ivory / Gold',    'status'=>'Shipped',   'tracking'=>'1Z999AA10123456784'],
                ['num'=>'S-400','date'=>'Feb 27, 2026','rug'=>'Sultanabad Classic','color'=>'Midnight Blue',   'status'=>'Approved',  'tracking'=>'–'],
                ['num'=>'S-401','date'=>'Feb 28, 2026','rug'=>'Tabriz Heritage',   'color'=>'Ivory / Gold',    'status'=>'Shipped',   'tracking'=>'1Z999AA10123456784'],
                ['num'=>'S-398','date'=>'Feb 20, 2026','rug'=>'Agra Imperial',     'color'=>'Rust / Ivory',    'status'=>'Delivered', 'tracking'=>'1Z999AA10123456782'],
                ['num'=>'S-400','date'=>'Feb 27, 2026','rug'=>'Sultanabad Classic','color'=>'Midnight Blue',   'status'=>'Approved',  'tracking'=>'–'],
                ['num'=>'S-399','date'=>'Feb 25, 2026','rug'=>'Oushak Revival',    'color'=>'Sage / Cream',    'status'=>'Pending',   'tracking'=>'–'],
                ['num'=>'S-400','date'=>'Feb 27, 2026','rug'=>'Sultanabad Classic','color'=>'Midnight Blue',   'status'=>'Approved',  'tracking'=>'–'],
                ['num'=>'S-399','date'=>'Feb 25, 2026','rug'=>'Oushak Revival',    'color'=>'Sage / Cream',    'status'=>'Pending',   'tracking'=>'–'],
                ['num'=>'S-399','date'=>'Feb 25, 2026','rug'=>'Oushak Revival',    'color'=>'Sage / Cream',    'status'=>'Pending',   'tracking'=>'–'],
                ['num'=>'S-398','date'=>'Feb 20, 2026','rug'=>'Agra Imperial',     'color'=>'Rust / Ivory',    'status'=>'Delivered', 'tracking'=>'1Z999AA10123456782'],
            ];
            $statusColors = [
                'Pending'   => 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;',
                'Approved'  => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
                'Shipped'   => 'color:#7c3aed; background:#ede9fe; border:1px solid #ddd6fe;',
                'Delivered' => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
            ];
            @endphp
            @foreach($samples as $s)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="1.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3"/>
                        </svg>
                        <div>
                            <p style="font-size:14px; font-weight:500; color:#121212;">{{ $s['num'] }}</p>
                            <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $s['date'] }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $s['rug'] }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $s['color'] }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$s['status']] }}">
                        {{ $s['status'] }}
                    </span>
                </td>
                <td class="px-5 py-3" style="font-size:13px; color:rgba(18,18,18,0.55); font-family:monospace;">{{ $s['tracking'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
