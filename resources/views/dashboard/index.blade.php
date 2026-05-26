@extends('layouts.dashboard')
@section('title', 'Engagement Data')

@section('dashboard-content')

@php
$stats = [
    [
        'label' => 'Active Customer Requests',
        'value' => '+' . $ordersCount,
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
    [
        'label' => 'Active Customer Requests',
        'value' => '+' . $wishlistCount,
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
    [
        'label' => 'Active Customer Requests',
        'value' => '+' . $pendingOrders,
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
    [
        'label' => 'Active Customer Requests',
        'value' => '+' . $completedOrders,
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
    [
        'label' => 'Active Customer Requests',
        'value' => '+' . $activeCartItems,
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
    [
        'label' => 'Active Customer Requests',
        'value' => '$' . number_format($totalSpent, 0),
        'change' => '+12%',
        'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
    ],
];
@endphp

{{-- ── 6 Stat cards 3×2 ── --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    @foreach($stats as $stat)
    <div class="bg-white border border-stone-200 rounded-lg p-5" style="min-height:120px;">
        <div class="flex items-start justify-between mb-4">
            <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $stat['icon'] !!}
            </svg>
            <span style="font-size:12px; font-weight:500; color:#16a34a;">{{ $stat['change'] }}</span>
        </div>
        <p style="font-size:13px; color:#6b7280; margin-bottom:6px;">{{ $stat['label'] }}</p>
        <p style="font-size:26px; font-weight:700; color:#121212; font-family:'Inter',sans-serif; line-height:1;">
            {{ $stat['value'] }}
        </p>
    </div>
    @endforeach
</div>

{{-- ── Bottom row: Chart + Top Collections ── --}}
<div class="grid grid-cols-[1fr_300px] gap-4">

    {{-- Customer Interaction Volume chart --}}
    <div class="bg-white border border-stone-200 rounded-lg p-5"
         x-data="interactionChart(@json($dailyData), @json($monthlyData))">

        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 style="font-size:18px; font-weight:700; color:#121212; font-family:'Inter',sans-serif;">Customer Interaction Volume</h2>
            </div>
            <div class="flex items-center border border-stone-200 rounded-full overflow-hidden" style="font-size:12px;">
                <button @click="mode='daily'"
                        :class="mode==='daily' ? 'bg-stone-900 text-white' : 'text-stone-500 hover:bg-stone-50'"
                        class="px-4 py-1.5 transition-colors font-medium">
                    DAILY
                </button>
                <button @click="mode='monthly'"
                        :class="mode==='monthly' ? 'bg-stone-900 text-white' : 'text-stone-500 hover:bg-stone-50'"
                        class="px-4 py-1.5 transition-colors font-medium">
                    MONTHLY
                </button>
            </div>
        </div>

        {{-- Bar chart --}}
        <div class="flex items-end gap-2 w-full" style="height:180px;">
            <template x-for="(bar, i) in currentData" :key="i">
                <div class="flex flex-col items-center flex-1 gap-1 h-full justify-end">
                    <div class="w-full rounded-sm transition-all duration-500"
                         :style="`height: ${maxVal > 0 ? Math.max(4, (bar.value / maxVal) * 160) : 8}px; background:#e5e7eb;`">
                    </div>
                    <span style="font-size:10px; color:#9ca3af;" x-text="bar.label"></span>
                </div>
            </template>
            {{-- If all zeros, show placeholder bars --}}
            <template x-if="maxVal === 0">
                <template x-for="i in 7" :key="i">
                    <div class="flex flex-col items-center flex-1 gap-1 h-full justify-end">
                        <div class="w-full rounded-sm" style="height:32px; background:#f3f4f6;"></div>
                    </div>
                </template>
            </template>
        </div>
    </div>

    {{-- Top Viewed Collections --}}
    <div class="bg-white border border-stone-200 rounded-lg p-5">
        <h2 style="font-size:18px; font-weight:700; color:#121212; font-family:'Inter',sans-serif;" class="mb-1">
            Top Viewed Collections
        </h2>
        <p style="font-size:12px; color:#9ca3af;" class="mb-5">Direct product page views</p>

        <div class="space-y-4">
            @forelse($topProducts as $product)
            @php
                $views = max(1, $product->order_items_count);
                $maxViews = $topProducts->max('order_items_count') ?: 1;
                $pct = ($views / $maxViews) * 100;
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span style="font-size:13px; color:#374151;">{{ $product->name }}</span>
                    <span style="font-size:12px; color:#9ca3af; font-weight:500;">
                        {{ $views >= 1000 ? number_format($views/1000, 1).'K' : $views }} VIEWS
                    </span>
                </div>
                <div class="w-full rounded-full" style="height:3px; background:#f3f4f6;">
                    <div class="rounded-full" style="height:3px; background:#111827; width:{{ $pct }}%;"></div>
                </div>
            </div>
            @empty
            @for($i = 0; $i < 5; $i++)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span style="font-size:13px; color:#374151;">Heritage Vintage</span>
                    <span style="font-size:12px; color:#9ca3af; font-weight:500;">1.2K VIEWS</span>
                </div>
                <div class="w-full rounded-full" style="height:3px; background:#f3f4f6;">
                    <div class="rounded-full" style="height:3px; background:#111827; width:65%;"></div>
                </div>
            </div>
            @endfor
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
