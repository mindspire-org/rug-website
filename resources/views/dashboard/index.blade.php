@extends('layouts.dashboard')
@section('title', 'My Account')

@section('dashboard-content')

<div class="mb-5">
    <h1 style="font-size:20px; font-weight:700; color:#121212; font-family:'Inter',sans-serif;">Welcome back, {{ Auth::user()->name }}</h1>
    <p style="font-size:13px; color:#6b7280; margin-top:2px;">Here's an overview of your account.</p>
</div>

@php
$stats = [
    [
        'label' => 'My Orders',
        'value' => $ordersCount,
        'route' => 'dashboard.orders',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>',
    ],
    [
        'label' => 'Pending Orders',
        'value' => $pendingOrders,
        'route' => 'dashboard.orders',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
    ],
    [
        'label' => 'Delivered Orders',
        'value' => $completedOrders,
        'route' => 'dashboard.orders',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
    ],
    [
        'label' => 'Wishlist Items',
        'value' => $wishlistCount,
        'route' => 'wishlist.index',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/>',
    ],
    [
        'label' => 'Items in Cart',
        'value' => $activeCartItems,
        'route' => null,
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>',
    ],
    [
        'label' => 'Total Spent',
        'value' => '$' . number_format($totalSpent, 0),
        'route' => null,
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>',
    ],
];
@endphp

{{-- ── 6 customer stat cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4 mb-5">
    @foreach($stats as $stat)
    @php $tag = $stat['route'] ? 'a' : 'div'; @endphp
    <{{ $tag }} @if($stat['route']) href="{{ route($stat['route']) }}" @endif
        class="block bg-white border border-stone-200 rounded-lg p-4 lg:p-5 {{ $stat['route'] ? 'hover:border-stone-300 transition-colors' : '' }}" style="min-height:100px;">
        <div class="flex items-start justify-between mb-3">
            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $stat['icon'] !!}
            </svg>
        </div>
        <p style="font-size:12px; color:#6b7280; margin-bottom:4px;">{{ $stat['label'] }}</p>
        <p style="font-size:22px; font-weight:700; color:#121212; font-family:'Inter',sans-serif; line-height:1;">
            {{ $stat['value'] }}
        </p>
    </{{ $tag }}>
    @endforeach
</div>

{{-- ── Bottom row: Order activity chart + Recent Orders ── --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4">

    {{-- Your order activity --}}
    <div class="bg-white border border-stone-200 rounded-lg p-5"
         x-data="interactionChart({{ Js::from($dailyData) }}, {{ Js::from($monthlyData) }})">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 gap-3">
            <h2 style="font-size:16px; font-weight:700; color:#121212; font-family:'Inter',sans-serif;">Your Order Activity</h2>
            <div class="flex items-center border border-stone-200 rounded-full overflow-hidden self-start sm:self-auto" style="font-size:12px;">
                <button @click="mode='daily'"
                        :class="mode==='daily' ? 'bg-stone-900 text-white' : 'text-stone-500 hover:bg-stone-50'"
                        class="px-4 py-1.5 transition-colors font-medium">DAILY</button>
                <button @click="mode='monthly'"
                        :class="mode==='monthly' ? 'bg-stone-900 text-white' : 'text-stone-500 hover:bg-stone-50'"
                        class="px-4 py-1.5 transition-colors font-medium">MONTHLY</button>
            </div>
        </div>

        <div class="flex items-end gap-2 w-full" style="height:180px;">
            <template x-for="(bar, i) in currentData" :key="i">
                <div class="flex flex-col items-center flex-1 gap-1 h-full justify-end">
                    <div class="w-full rounded-sm transition-all duration-500"
                         :style="`height: ${maxVal > 0 ? Math.max(4, (bar.value / maxVal) * 160) : 8}px; background:${maxVal > 0 ? '#E8651A' : '#e5e7eb'};`"></div>
                    <span style="font-size:10px; color:#9ca3af;" x-text="bar.label"></span>
                </div>
            </template>
        </div>
        <p x-show="maxVal === 0" style="font-size:12px; color:#9ca3af; text-align:center; margin-top:12px;">No orders in this period yet.</p>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white border border-stone-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 style="font-size:16px; font-weight:700; color:#121212; font-family:'Inter',sans-serif;">Recent Orders</h2>
            <a href="{{ route('dashboard.orders') }}" style="font-size:12px; font-weight:500; color:#E8651A;">View all</a>
        </div>

        <div class="space-y-3">
            @forelse($recentOrders as $order)
            @php
                $statusColors = [
                    'pending' => ['#fef9c3', '#854d0e'], 'processing' => ['#dbeafe', '#1e40af'],
                    'shipped' => ['#e0e7ff', '#3730a3'], 'delivered' => ['#dcfce7', '#15803d'],
                    'cancelled' => ['#fee2e2', '#b91c1c'],
                ];
                [$bg, $fg] = $statusColors[$order->status] ?? ['#f3f4f6', '#374151'];
            @endphp
            <a href="{{ route('dashboard.orders') }}" class="flex items-center justify-between gap-2 pb-3 {{ !$loop->last ? 'border-b border-stone-100' : '' }}">
                <div class="min-w-0">
                    <p style="font-size:13px; font-weight:600; color:#121212;" class="truncate">{{ $order->order_number }}</p>
                    <p style="font-size:11px; color:#9ca3af;">{{ $order->created_at->format('M j, Y') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p style="font-size:13px; font-weight:600; color:#121212;">${{ number_format($order->total, 0) }}</p>
                    <span class="inline-block px-2 py-0.5 rounded-full" style="font-size:9px; font-weight:600; background:{{ $bg }}; color:{{ $fg }};">{{ ucfirst($order->status) }}</span>
                </div>
            </a>
            @empty
            <div class="text-center py-8">
                <p style="font-size:13px; color:#9ca3af; margin-bottom:12px;">You haven't placed any orders yet.</p>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center text-white px-5 hover:opacity-90"
                   style="background:#E8651A; height:38px; border-radius:3px; font-size:13px; font-weight:500;">Start shopping</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('interactionChart', (dailyData, monthlyData) => ({
        mode: 'daily',
        dailyData: dailyData,
        monthlyData: monthlyData,
        get currentData() {
            return this.mode === 'daily' ? this.dailyData : this.monthlyData;
        },
        get maxVal() {
            return Math.max(...this.currentData.map(d => d.value), 0);
        },
    }));
});
</script>
@endpush

@endsection
