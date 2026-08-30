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

@php
$statusColors = [
    'pending'     => 'color:#b45309; background:#fef3c7; border:1px solid #fde68a;',
    'processing' => 'color:#7c3aed; background:#ede9fe; border:1px solid #ddd6fe;',
    'shipped'   => 'color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe;',
    'delivered' => 'color:#15803d; background:#dcfce7; border:1px solid #bbf7d0;',
    'cancelled' => 'color:#c2410c; background:#ffedd5; border:1px solid #fed7aa;',
];
$statusLabels = [
    'pending'     => 'Pending',
    'processing'  => 'Processing',
    'shipped'     => 'Shipped',
    'delivered'   => 'Delivered',
    'cancelled'   => 'Cancelled',
];
@endphp

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    @if($orders->count())
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ORDER</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ITEMS</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-4 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">DATE</th>
                <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>
                        </svg>
                        <span style="font-size:14px; font-weight:500; color:#121212;">{{ $o->order_number }}</span>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $o->items->count() }} item{{ $o->items->count() !== 1 ? 's' : '' }}</td>
                <td class="px-4 py-3">
                    <span style="font-size:12px; font-weight:500; padding:3px 10px; border-radius:20px; {{ $statusColors[$o->status] ?? $statusColors['pending'] }}">
                        {{ $statusLabels[$o->status] ?? ucfirst($o->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right" style="font-size:13px; color:rgba(18,18,18,0.6);">{{ $o->created_at->format('M j, Y') }}</td>
                <td class="px-5 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($o->total, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="px-6 py-12 text-center">
        <svg class="w-10 h-10 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>
        </svg>
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No orders yet. Place your first trade order.</p>
    </div>
    @endif
</div>

@endsection
