@extends('layouts.admin')
@section('title', 'Dashboard')

@section('admin-content')

{{-- Page heading --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:#0f172a;">Dashboard</h1>
        <p style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ now()->format('l, F j, Y') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.products.create') }}"
           class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-semibold text-white transition-colors hover:opacity-90"
           style="background:#0f172a;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-semibold border border-stone-300 text-stone-700 hover:bg-stone-100 transition-colors">
            View Orders
        </a>
    </div>
</div>

{{-- ── 6 KPI Stat Cards ── --}}
@php
$kpis = [
    [
        'label'   => 'Total Revenue',
        'value'   => '$'.number_format($stats['total_revenue'],0),
        'sub'     => 'All paid orders',
        'color'   => '#16a34a',
        'bg'      => '#f0fdf4',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
    ],
    [
        'label'   => 'Total Orders',
        'value'   => number_format($stats['total_orders']),
        'sub'     => $stats['pending_orders'].' pending',
        'color'   => '#2563eb',
        'bg'      => '#eff6ff',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>',
    ],
    [
        'label'   => 'Products',
        'value'   => number_format($stats['total_products']),
        'sub'     => $stats['low_stock'].' low stock',
        'color'   => '#7c3aed',
        'bg'      => '#f5f3ff',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
    ],
    [
        'label'   => 'Customers',
        'value'   => number_format($stats['total_customers']),
        'sub'     => 'Registered accounts',
        'color'   => '#d97706',
        'bg'      => '#fffbeb',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>',
    ],
    [
        'label'   => 'Avg Order Value',
        'value'   => '$'.number_format($stats['total_orders'] > 0 ? $stats['total_revenue'] / $stats['total_orders'] : 0, 0),
        'sub'     => 'Per order',
        'color'   => '#0891b2',
        'bg'      => '#ecfeff',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 0-2 2h-2a2 2 0 0 0-2-2z"/>',
    ],
    [
        'label'   => 'Delivered Orders',
        'value'   => number_format($stats['delivered_orders'] ?? 0),
        'sub'     => 'Successfully delivered',
        'color'   => '#E8651A',
        'bg'      => '#fff7ed',
        'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>',
    ],
];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($kpis as $kpi)
    <div class="bg-white rounded-xl border border-stone-200 p-4 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:{{ $kpi['bg'] }};">
                <svg class="w-4 h-4" fill="none" stroke="{{ $kpi['color'] }}" viewBox="0 0 24 24">{!! $kpi['icon'] !!}</svg>
            </div>
        </div>
        <p style="font-size:20px; font-weight:700; color:#0f172a; line-height:1;">{{ $kpi['value'] }}</p>
        <p style="font-size:12px; font-weight:500; color:#374151; margin-top:3px;">{{ $kpi['label'] }}</p>
        <p style="font-size:11px; color:#9ca3af; margin-top:1px;">{{ $kpi['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Row: Recent Orders + Sidebar panels ── --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-5">

    {{-- Recent Orders table --}}
    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 style="font-size:14px; font-weight:600; color:#0f172a;">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}"
               style="font-size:12px; color:#E8651A; text-decoration:none;" class="hover:underline">
                View all →
            </a>
        </div>
        <table class="w-full text-sm">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="text-left px-5 py-2.5" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Order</th>
                    <th class="text-left px-5 py-2.5 hidden sm:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Customer</th>
                    <th class="text-left px-5 py-2.5 hidden md:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Date</th>
                    <th class="text-left px-5 py-2.5" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Total</th>
                    <th class="text-left px-5 py-2.5" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Status</th>
                    <th class="px-5 py-2.5"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr class="border-t border-stone-50 hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3">
                        <p style="font-size:13px; font-weight:600; color:#0f172a;">{{ $order->order_number }}</p>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell">
                        <p style="font-size:13px; color:#374151;">{{ $order->user?->name ?? 'Guest' }}</p>
                        <p style="font-size:11px; color:#9ca3af;">{{ $order->user?->email ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3 hidden md:table-cell" style="font-size:12px; color:#64748b;">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-5 py-3">
                        <span style="font-size:13px; font-weight:600; color:#0f172a;">${{ number_format($order->total,0) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @php
                        $statusColors = [
                            'pending'    => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316'],
                            'processing' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#3b82f6'],
                            'shipped'    => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#22c55e'],
                            'delivered'  => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#16a34a'],
                            'cancelled'  => ['bg'=>'#fef2f2','color'=>'#dc2626','dot'=>'#ef4444'],
                        ];
                        $sc = $statusColors[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','dot'=>'#94a3b8'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full"
                              style="background:{{ $sc['bg'] }}; font-size:11px; font-weight:600; color:{{ $sc['color'] }};">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $sc['dot'] }};"></span>
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           style="font-size:12px; color:#E8651A; text-decoration:none;" class="hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center" style="font-size:13px; color:#94a3b8;">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Right sidebar --}}
    <div class="flex flex-col gap-5">

        {{-- Quick actions --}}
        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <h3 style="font-size:13px; font-weight:600; color:#0f172a; margin-bottom:12px;">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.products.create') }}"
                   class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg border border-stone-200 hover:bg-stone-50 transition-colors">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background:#f5f3ff;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="#7c3aed" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span style="font-size:12px; font-weight:500; color:#374151;">New Product</span>
                </a>
                <a href="{{ route('admin.orders.index') }}?status=pending"
                   class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg border border-stone-200 hover:bg-stone-50 transition-colors">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background:#fff7ed;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </div>
                    <span style="font-size:12px; font-weight:500; color:#374151;">Pending Orders</span>
                    @if($stats['pending_orders'] > 0)
                    <span class="ml-auto px-1.5 py-0.5 rounded-full text-white" style="font-size:10px; font-weight:700; background:#f97316;">{{ $stats['pending_orders'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg border border-stone-200 hover:bg-stone-50 transition-colors">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background:#fffbeb;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197"/></svg>
                    </div>
                    <span style="font-size:12px; font-weight:500; color:#374151;">All Customers</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                   class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg border border-stone-200 hover:bg-stone-50 transition-colors">
                    <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background:#ecfeff;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="#0891b2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                    <span style="font-size:12px; font-weight:500; color:#374151;">Manage Coupons</span>
                </a>
            </div>
        </div>

        {{-- Top wishlisted --}}
        <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-100">
                <h3 style="font-size:13px; font-weight:600; color:#0f172a;">Top Wishlisted</h3>
            </div>
            <div class="divide-y divide-stone-50">
                @forelse($topProducts as $product)
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md overflow-hidden bg-stone-100 flex-shrink-0">
                        <img src="{{ $product->primary_image_url }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p style="font-size:12px; font-weight:500; color:#374151;" class="truncate">{{ $product->name }}</p>
                    </div>
                    <span style="font-size:11px; color:#9ca3af; font-weight:500; white-space:nowrap;">{{ $product->wishlists_count }} saves</span>
                </div>
                @empty
                <p class="px-5 py-4 text-center" style="font-size:12px; color:#9ca3af;">No data yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Low stock alert --}}
        @if($stats['low_stock'] > 0)
        <div class="rounded-xl border p-4" style="background:#fffbeb; border-color:#fcd34d;">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="#d97706" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p style="font-size:13px; font-weight:600; color:#92400e;">Low Stock Warning</p>
            </div>
            <p style="font-size:12px; color:#78350f;">{{ $stats['low_stock'] }} product(s) have fewer than 5 units remaining.</p>
            <a href="{{ route('admin.products.index') }}" style="font-size:12px; color:#d97706; font-weight:600; text-decoration:none;" class="hover:underline mt-2 inline-block">Review products →</a>
        </div>
        @endif

    </div>
</div>

@endsection
