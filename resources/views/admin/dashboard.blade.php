@extends('layouts.admin')
@section('title', 'Dashboard')

@section('admin-content')

{{-- ═══════════════ DATE TOOLBAR + EXPORT ═══════════════ --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6" id="dashboard-toolbar">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:#0f172a;">Dashboard</h1>
        <p style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        {{-- Date Range Picker --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                </svg>
                <input type="text" id="dateRange" name="date_range"
                       class="pl-8 pr-3 py-1.5 text-sm border border-stone-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 w-52"
                       placeholder="Select date range..."
                       value="{{ $start->format('Y-m-d') }} to {{ $end->format('Y-m-d') }}">
                <input type="hidden" name="date_start" id="date_start" value="{{ $start->format('Y-m-d') }}">
                <input type="hidden" name="date_end" id="date_end" value="{{ $end->format('Y-m-d') }}">
            </div>
            <button type="submit"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors hover:opacity-90"
                    style="background:#0f172a;">
                Apply
            </button>
        </form>

        {{-- Quick presets --}}
        <div class="flex items-center gap-1.5">
            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1.5 rounded-md text-xs font-medium border border-stone-200 text-stone-600 hover:bg-stone-50 transition-colors {{ request('date_start') ? '' : 'bg-stone-100' }}">30 Days</a>
            <a href="{{ route('admin.dashboard', ['date_start' => now()->subDays(6)->format('Y-m-d'), 'date_end' => now()->format('Y-m-d')]) }}" class="px-2.5 py-1.5 rounded-md text-xs font-medium border border-stone-200 text-stone-600 hover:bg-stone-50 transition-colors">7 Days</a>
            <a href="{{ route('admin.dashboard', ['date_start' => now()->startOfMonth()->format('Y-m-d'), 'date_end' => now()->endOfMonth()->format('Y-m-d')]) }}" class="px-2.5 py-1.5 rounded-md text-xs font-medium border border-stone-200 text-stone-600 hover:bg-stone-50 transition-colors">This Month</a>
        </div>

        {{-- Export buttons --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.export.csv', request()->only('date_start','date_end')) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 text-xs font-medium text-stone-700 hover:bg-stone-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                CSV
            </a>
            <button onclick="window.print()"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 text-xs font-medium text-stone-700 hover:bg-stone-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 0 0 2-2V9.414a1 1 0 0 0-.293-.707l-5.414-5.414A1 1 0 0 0 12.586 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2z"/></svg>
                PDF
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════ KPI CARDS ═══════════════ --}}
@php
$kpis = [
    ['label'=>'Total Revenue',  'value'=>'$'.number_format($stats['total_revenue'],0), 'sub'=>'Paid orders in range',  'color'=>'#16a34a', 'bg'=>'#f0fdf4', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>'],
    ['label'=>'Total Orders',   'value'=>number_format($stats['total_orders']),           'sub'=>$stats['pending_orders'].' pending',      'color'=>'#2563eb', 'bg'=>'#eff6ff', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>'],
    ['label'=>'Products',       'value'=>number_format($stats['total_products']),         'sub'=>$stats['low_stock'].' low stock',         'color'=>'#7c3aed', 'bg'=>'#f5f3ff', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
    ['label'=>'Customers',      'value'=>number_format($stats['total_customers']),         'sub'=>$stats['repeat_customers'].' repeat',    'color'=>'#d97706', 'bg'=>'#fffbeb', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>'],
    ['label'=>'Avg Order Value','value'=>'$'.number_format($stats['avg_order_value'],0), 'sub'=>'Per order','color'=>'#0891b2','bg'=>'#ecfeff','icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v10m-6 0a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2m0 0V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v14a2 2 0 0 0-2 2h-2a2 2 0 0 0-2-2z"/>'],
    ['label'=>'Delivered',      'value'=>number_format($stats['delivered_orders'] ?? 0),  'sub'=>'Successfully delivered',                 'color'=>'#E8651A', 'bg'=>'#fff7ed', 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>'],
];
$engagement = [
    ['label'=>'Estimates Saved','value'=>number_format($stats['saved_estimates']),'sub'=>'In date range','color'=>'#4f46e5','bg'=>'#eef2ff','route'=>'admin.submissions.estimates'],
    ['label'=>'Visualizations', 'value'=>number_format($stats['visualizations']), 'sub'=>'AI room previews','color'=>'#0d9488','bg'=>'#f0fdfa','route'=>'admin.submissions.visualizations'],
    ['label'=>'Sample Requests','value'=>number_format($stats['sample_requests']),'sub'=>'Material samples','color'=>'#be185d','bg'=>'#fff1f2','route'=>'admin.submissions.samples'],
    ['label'=>'Wishlist Items', 'value'=>number_format($stats['wishlist_items']), 'sub'=>'Saved products','color'=>'#ca8a04','bg'=>'#fefce8','route'=>'shop.index'],
];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($kpis as $kpi)
    <div class="bg-white rounded-xl border border-stone-200 p-4 hover:shadow-sm transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $kpi['bg'] }};">
                <svg class="w-4 h-4" fill="none" stroke="{{ $kpi['color'] }}" viewBox="0 0 24 24">{!! $kpi['icon'] !!}</svg>
            </div>
        </div>
        <p style="font-size:20px; font-weight:700; color:#0f172a; line-height:1;">{{ $kpi['value'] }}</p>
        <p style="font-size:12px; font-weight:500; color:#374151; margin-top:3px;">{{ $kpi['label'] }}</p>
        <p style="font-size:11px; color:#9ca3af; margin-top:1px;">{{ $kpi['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- ═══════════════ ENGAGEMENT CARDS ═══════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($engagement as $e)
    <a href="{{ route($e['route']) }}" class="block bg-white rounded-xl border border-stone-200 p-4 hover:shadow-sm transition-shadow" style="text-decoration:none;">
        <div class="flex items-center justify-between mb-2">
            <span style="font-size:11px; font-weight:600; color:{{ $e['color'] }}; letter-spacing:0.05em; text-transform:uppercase;">{{ $e['label'] }}</span>
        </div>
        <p style="font-size:20px; font-weight:700; color:#0f172a; line-height:1;">{{ $e['value'] }}</p>
        <p style="font-size:11px; color:#9ca3af; margin-top:2px;">{{ $e['sub'] }}</p>
    </a>
    @endforeach
</div>

{{-- ═══════════════ CHARTS ROW ═══════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    {{-- Revenue Line Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-stone-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 style="font-size:14px; font-weight:600; color:#0f172a;">Revenue Trend</h2>
            <span style="font-size:11px; color:#94a3b8;">{{ $start->format('M d') }} – {{ $end->format('M d, Y') }}</span>
        </div>
        <div class="relative" style="height:260px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Orders by Status Doughnut --}}
    <div class="bg-white rounded-xl border border-stone-200 p-5">
        <h2 style="font-size:14px; font-weight:600; color:#0f172a; margin-bottom:16px;">Orders by Status</h2>
        <div class="relative flex items-center justify-center" style="height:220px;">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4">
            @foreach(['Pending'=>'#f97316','Processing'=>'#3b82f6','Shipped'=>'#22c55e','Delivered'=>'#16a34a','Cancelled'=>'#ef4444'] as $label => $color)
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $color }};"></span>
                <span style="font-size:11px; color:#64748b;">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ═══════════════ SECOND CHART ROW + TABLE ═══════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr_380px] gap-5 mb-6">

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 style="font-size:14px; font-weight:600; color:#0f172a;">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" style="font-size:12px; color:#E8651A;" class="hover:underline">View all →</a>
        </div>
        <div class="overflow-x-auto">
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
                        <td class="px-5 py-3 hidden md:table-cell" style="font-size:12px; color:#64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <span style="font-size:13px; font-weight:600; color:#0f172a;">${{ number_format($order->total,0) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @php
                            $sc = match($order->status) {
                                'pending'    => ['bg'=>'#fff7ed','color'=>'#c2410c','dot'=>'#f97316'],
                                'processing' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','dot'=>'#3b82f6'],
                                'shipped'    => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#22c55e'],
                                'delivered'  => ['bg'=>'#f0fdf4','color'=>'#15803d','dot'=>'#16a34a'],
                                'cancelled'  => ['bg'=>'#fef2f2','color'=>'#dc2626','dot'=>'#ef4444'],
                                default      => ['bg'=>'#f1f5f9','color'=>'#475569','dot'=>'#94a3b8'],
                            };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full" style="background:{{ $sc['bg'] }}; font-size:11px; font-weight:600; color:{{ $sc['color'] }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $sc['dot'] }};"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" style="font-size:12px; color:#E8651A;" class="hover:underline">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center" style="font-size:13px; color:#94a3b8;">No orders in selected date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right sidebar: Top Selling + Wishlisted + Quick Actions --}}
    <div class="flex flex-col gap-5">

        {{-- Top Selling Bar Chart --}}
        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <h2 style="font-size:14px; font-weight:600; color:#0f172a; margin-bottom:12px;">Top Selling Products</h2>
            <div class="relative" style="height:200px;">
                <canvas id="topSellingChart"></canvas>
            </div>
        </div>

        {{-- Top Wishlisted --}}
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
            <a href="{{ route('admin.products.index') }}" style="font-size:12px; color:#d97706; font-weight:600;" class="hover:underline mt-2 inline-block">Review products →</a>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
{{-- Flatpickr Date Range --}}
flatpickr('#dateRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    defaultDate: ['{{ $start->format('Y-m-d') }}', '{{ $end->format('Y-m-d') }}'],
    onChange: function(selectedDates) {
        if (selectedDates.length === 2) {
            document.getElementById('date_start').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
            document.getElementById('date_end').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
        }
    }
});

{{-- Revenue Line Chart --}}
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueChart['labels']) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($revenueChart['data']) !!},
            borderColor: '#E8651A',
            backgroundColor: 'rgba(232,101,26,0.08)',
            borderWidth: 2.5,
            pointRadius: 3,
            pointBackgroundColor: '#E8651A',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
            y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, color: '#94a3b8', callback: v => '$' + v.toLocaleString() } }
        },
        interaction: { intersect: false, mode: 'index' }
    }
});

{{-- Orders Status Doughnut --}}
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($ordersStatusChart['labels']) !!},
        datasets: [{
            data: {!! json_encode($ordersStatusChart['data']) !!},
            backgroundColor: ['#f97316', '#3b82f6', '#22c55e', '#16a34a', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed }
            }
        }
    }
});

{{-- Top Selling Horizontal Bar --}}
new Chart(document.getElementById('topSellingChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($topSellingChart['labels']) !!},
        datasets: [{
            label: 'Qty Sold',
            data: {!! json_encode($topSellingChart['data']) !!},
            backgroundColor: '#0f172a',
            borderRadius: 4,
            barThickness: 18
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
            y: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748b' } }
        }
    }
});
</script>
@endpush
