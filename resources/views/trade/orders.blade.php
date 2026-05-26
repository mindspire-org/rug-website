@extends('layouts.trade')
@section('title', 'Orders')

@section('trade-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Orders</h1>
        <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Track your rug orders and production status</p>
    </div>
    <button class="flex items-center gap-2 text-white rounded"
            style="background:#121212; padding:10px 18px; font-size:14px; font-weight:500;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        New Order
    </button>
</div>

<div class="flex gap-3 mb-5">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="1.5"/><path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search orders..." class="w-full focus:outline-none pl-9 pr-4 py-2.5 bg-white border border-stone-200 rounded text-sm">
    </div>
    <select class="focus:outline-none bg-white border border-stone-200 rounded px-4 py-2.5 text-sm" style="min-width:80px;">
        <option>All</option>
        <option>In Production</option>
        <option>Loom Scheduled</option>
        <option>Quality Check</option>
        <option>Shipped</option>
        <option>Delivered</option>
    </select>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ORDER</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUG</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TYPE</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-4 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ETA</th>
                <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php
            $orders = [
                ['num'=>'ORD-2048','rug'=>'Tabriz Heritage 9×12',    'type'=>'Standard','status'=>'In Production',   'eta'=>'Apr 15, 2026','total'=>5400],
                ['num'=>'ORD-2047','rug'=>'Custom Sultanabad 10×14', 'type'=>'Custom',  'status'=>'Loom Scheduled',  'eta'=>'May 1, 2026', 'total'=>8200],
                ['num'=>'ORD-2045','rug'=>'Agra Imperial 12×15',     'type'=>'Custom',  'status'=>'Quality Check',   'eta'=>'Mar 20, 2026','total'=>12600],
                ['num'=>'ORD-2047','rug'=>'Custom Sultanabad 10×14', 'type'=>'Custom',  'status'=>'Loom Scheduled',  'eta'=>'May 1, 2026', 'total'=>8200],
                ['num'=>'ORD-2044','rug'=>'Ziegler Modern 6×9',      'type'=>'Standard','status'=>'Delivered',       'eta'=>'Delivered',   'total'=>3200],
                ['num'=>'ORD-2046','rug'=>'Oushak Revival 8×10',     'type'=>'Standard','status'=>'Shipped',         'eta'=>'Delivered',   'total'=>4100],
                ['num'=>'ORD-2046','rug'=>'Oushak Revival 8×10',     'type'=>'Standard','status'=>'Shipped',         'eta'=>'Delivered',   'total'=>4100],
                ['num'=>'ORD-2044','rug'=>'Ziegler Modern 6×9',      'type'=>'Standard','status'=>'Delivered',       'eta'=>'Delivered',   'total'=>3200],
                ['num'=>'ORD-2048','rug'=>'Tabriz Heritage 9×12',    'type'=>'Standard','status'=>'In Production',   'eta'=>'Apr 15, 2026','total'=>5400],
                ['num'=>'ORD-2046','rug'=>'Oushak Revival 8×10',     'type'=>'Standard','status'=>'Shipped',         'eta'=>'Delivered',   'total'=>4100],
                ['num'=>'ORD-2047','rug'=>'Custom Sultanabad 10×14', 'type'=>'Custom',  'status'=>'Loom Scheduled',  'eta'=>'May 1, 2026', 'total'=>8200],
            ];
            $statusColors = [
                'In Production'  => 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;',
                'Loom Scheduled' => 'color:#7c3aed; background:#ede9fe; border:1px solid #ddd6fe;',
                'Quality Check'  => 'color:#db2777; background:#fce7f3; border:1px solid #fbcfe8;',
                'Shipped'        => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
                'Delivered'      => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
            ];
            @endphp
            @foreach($orders as $o)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>
                        </svg>
                        <span style="font-size:14px; font-weight:500; color:#121212;">{{ $o['num'] }}</span>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $o['rug'] }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $o['type'] }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$o['status']] }}">
                        {{ $o['status'] }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right" style="font-size:13px; color:rgba(18,18,18,0.6);">{{ $o['eta'] }}</td>
                <td class="px-5 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($o['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
