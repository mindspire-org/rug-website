@extends('layouts.trade')
@section('title', 'Quotes')

@section('trade-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Quotes</h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Generate and manage trade quotes</p>
    </div>
    <button class="flex items-center gap-2 text-white rounded"
            style="background:#121212; padding:10px 18px; font-size:14px; font-weight:500;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        New Quote
    </button>
</div>

<div class="flex gap-3 mb-5">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search quotes..." class="w-full focus:outline-none pl-9 pr-4 py-2.5 bg-white border border-stone-200 rounded text-sm">
    </div>
    <select class="focus:outline-none bg-white border border-stone-200 rounded px-4 py-2.5 text-sm" style="min-width:80px;">
        <option>All</option>
        <option>Draft</option>
        <option>Sent</option>
        <option>Approved</option>
        <option>Expired</option>
    </select>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">QUOTE #</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PROJECT</th>
                <th class="px-4 py-3 text-center" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ITEMS</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
            $quotes = [
                ['num'=>'Q-1024','date'=>'Mar 1, 2026', 'project'=>'Tabriz Heritage 9×12',      'items'=>4, 'status'=>'Draft',    'total'=>18400],
                ['num'=>'Q-1023','date'=>'Feb 28, 2026','project'=>'Sultanabad Classic 10×14',   'items'=>2, 'status'=>'Sent',     'total'=>9200],
                ['num'=>'Q-1023','date'=>'Feb 28, 2026','project'=>'Oushak Revival 8×10',        'items'=>2, 'status'=>'Sent',     'total'=>9200],
                ['num'=>'Q-1021','date'=>'Feb 10, 2026','project'=>'Tabriz Heritage 9×12',       'items'=>1, 'status'=>'Expired',  'total'=>5800],
                ['num'=>'Q-1023','date'=>'Feb 28, 2026','project'=>'Agra Imperial Runner 3×12',  'items'=>2, 'status'=>'Sent',     'total'=>9200],
                ['num'=>'Q-1022','date'=>'Feb 25, 2026','project'=>'Sultanabad Classic 10×14',   'items'=>6, 'status'=>'Approved', 'total'=>32100],
                ['num'=>'Q-1022','date'=>'Feb 25, 2026','project'=>'Sultanabad Classic 10×14',   'items'=>6, 'status'=>'Approved', 'total'=>32100],
                ['num'=>'Q-1021','date'=>'Feb 10, 2026','project'=>'Tabriz Heritage 9×12',       'items'=>1, 'status'=>'Expired',  'total'=>5800],
                ['num'=>'Q-1022','date'=>'Feb 25, 2026','project'=>'Sultanabad Classic 10×14',   'items'=>6, 'status'=>'Approved', 'total'=>32100],
                ['num'=>'Q-1023','date'=>'Feb 28, 2026','project'=>'Tabriz Heritage 9×12',       'items'=>2, 'status'=>'Sent',     'total'=>9200],
            ];
            $statusColors = [
                'Draft'    => 'color:#57534e; background:#f5f5f4; border:1px solid #d6d3d1;',
                'Sent'     => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
                'Approved' => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
                'Expired'  => 'color:#c2410c; background:#ffedd5; border:1px solid #fed7aa;',
            ];
            @endphp
            @foreach($quotes as $q)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 8h6M9 16h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                        </svg>
                        <div>
                            <p style="font-size:14px; font-weight:500; color:#121212;">{{ $q['num'] }}</p>
                            <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $q['date'] }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $q['project'] }}</td>
                <td class="px-4 py-3 text-center" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $q['items'] }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$q['status']] }}">
                        {{ $q['status'] }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($q['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
