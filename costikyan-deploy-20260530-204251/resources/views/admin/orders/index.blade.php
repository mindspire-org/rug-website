@extends('layouts.admin')
@section('title', 'Orders')

@section('admin-content')
{{-- Page header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:#0f172a;">Orders</h1>
        <p style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ $orders->total() }} total orders</p>
    </div>
    <a href="{{ route('admin.orders.index') }}?export=1"
       class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-semibold border border-stone-300 text-stone-700 hover:bg-stone-100 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-2 mb-5">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order number…"
           style="padding:8px 12px; font-size:13px; border:1.5px solid #e2e8f0; border-radius:6px; outline:none; min-width:200px;"
           onfocus="this.style.borderColor='#E8651A'" onblur="this.style.borderColor='#e2e8f0'">
    <select name="status"
            style="padding:8px 12px; font-size:13px; border:1.5px solid #e2e8f0; border-radius:6px; outline:none; background:#fff; cursor:pointer;">
        <option value="">All Statuses</option>
        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit"
            style="padding:8px 16px; font-size:13px; font-weight:600; background:#0f172a; color:#fff; border-radius:6px; border:none; cursor:pointer;">
        Filter
    </button>
    <a href="{{ route('admin.orders.index') }}"
       style="padding:8px 16px; font-size:13px; font-weight:500; background:#fff; color:#64748b; border-radius:6px; border:1.5px solid #e2e8f0; text-decoration:none; display:flex; align-items:center;">
        Clear
    </a>
</form>

<div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
    <table class="w-full">
        <thead style="background:#f8fafc; border-bottom:1px solid #f1f5f9;">
            <tr>
                <th class="text-left px-5 py-3" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Order</th>
                <th class="text-left px-5 py-3" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Customer</th>
                <th class="text-left px-5 py-3 hidden md:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Date</th>
                <th class="text-left px-5 py-3" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Total</th>
                <th class="text-left px-5 py-3" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @php
            $statusColors = [
                'pending'    => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316'],
                'processing' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#3b82f6'],
                'shipped'    => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#22c55e'],
                'delivered'  => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#16a34a'],
                'cancelled'  => ['bg'=>'#fef2f2','color'=>'#dc2626','dot'=>'#ef4444'],
            ];
            @endphp
            @forelse($orders as $order)
            @php $sc = $statusColors[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','dot'=>'#94a3b8']; @endphp
            <tr class="border-t border-stone-50 hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <span style="font-size:13px; font-weight:600; color:#0f172a;">{{ $order->order_number }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <p style="font-size:13px; color:#374151;">{{ $order->user?->name ?? 'Guest' }}</p>
                    <p style="font-size:11px; color:#9ca3af;">{{ $order->user?->email ?? '' }}</p>
                </td>
                <td class="px-5 py-3.5 hidden md:table-cell" style="font-size:12px; color:#64748b;">
                    {{ $order->created_at->format('M d, Y') }}
                </td>
                <td class="px-5 py-3.5">
                    <span style="font-size:13px; font-weight:700; color:#0f172a;">${{ number_format($order->total, 0) }}</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full"
                          style="background:{{ $sc['bg'] }}; font-size:11px; font-weight:600; color:{{ $sc['color'] }};">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $sc['dot'] }};"></span>
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}"
                       style="font-size:12px; color:#E8651A; font-weight:500; text-decoration:none;" class="hover:underline">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center" style="font-size:13px; color:#94a3b8;">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $orders->links() }}</div>
@endsection
