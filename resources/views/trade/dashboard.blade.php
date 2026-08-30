@extends('layouts.trade')
@section('title', 'Dashboard')

@section('trade-content')

{{-- Heading --}}
<div class="mb-8">
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Welcome back</h1>
    <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Here's an overview of your trade activity</p>
</div>

{{-- ── Stat cards ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $statCards = [
        ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',  'label'=>'Active Projects',     'value'=>$stats['active_projects']],
        ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 8h6M9 16h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>', 'label'=>'Pending Quotes',      'value'=>$stats['pending_quotes']],
        ['icon'=>'<circle cx="12" cy="12" r="9" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3"/>',                                     'label'=>'Samples in Progress',  'value'=>$stats['samples_progress']],
        ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>',                                                           'label'=>'Orders in Production', 'value'=>$stats['orders_production']],
    ];
    @endphp
    @foreach($statCards as $s)
    <div class="bg-white border border-stone-200 rounded-lg p-5 flex flex-col gap-3">
        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['icon'] !!}</svg>
        <div class="flex items-end justify-between">
            <span style="font-size:13px; color:rgba(18,18,18,0.55);">{{ $s['label'] }}</span>
            <span style="font-family:'Lusitana',serif; font-size:32px; font-weight:700; color:#121212; line-height:1;">{{ $s['value'] }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Account Tier banner ── --}}
<div class="bg-white border border-stone-200 rounded-lg px-6 py-5 flex items-center justify-between mb-6">
    <div>
        <p style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:rgba(18,18,18,0.45);" class="mb-1">ACCOUNT TIER</p>
        <p style="font-family:'Lusitana',serif; font-size:20px; font-weight:700; color:#121212;">{{ Auth::user()->company_name ?? 'Trade Partner' }}</p>
    </div>
    <div class="text-right">
        <p style="font-size:11px; font-weight:600; letter-spacing:0.1em; color:rgba(18,18,18,0.45);" class="mb-1">YOUR DISCOUNT</p>
        <p style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#B8860B;">{{ $discount }}% off MSRP</p>
    </div>
</div>

{{-- ── Quick Actions ── --}}
<div class="mb-8">
    <h2 style="font-size:16px; font-weight:600; color:#121212;" class="mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @php
        $actions = [
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 5v14M5 12h14"/>',                                                                               'label'=>'Create New Project', 'href'=>route('trade.portal.projects')],
            ['icon'=>'<circle cx="12" cy="12" r="9" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3"/>',                                 'label'=>'Request Sample',     'href'=>route('trade.portal.samples')],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 8h6M9 16h4M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>', 'label'=>'Start New Quote',    'href'=>route('trade.portal.quotes')],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4z"/>',                                                       'label'=>'Order Rug',          'href'=>route('trade.portal.orders')],
        ];
        @endphp
        @foreach($actions as $a)
        <a href="{{ $a['href'] }}"
           class="bg-white border border-stone-200 rounded-lg px-4 py-3 flex items-center gap-3 hover:border-stone-400 transition-colors">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $a['icon'] !!}</svg>
            <span style="font-size:14px; color:#121212;">{{ $a['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- ── Recent Projects ── --}}
<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
        <h2 style="font-size:16px; font-weight:600; color:#121212;">Recent Projects</h2>
        <a href="{{ route('trade.portal.projects') }}" style="font-size:13px; color:#121212;">View All →</a>
    </div>
    @if($recentProjects->count())
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08);">
                <th class="px-6 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PROJECT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CLIENT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">RUGS</th>
                <th class="px-6 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">UPDATED</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentProjects as $p)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        <span style="font-size:14px; font-weight:500; color:#121212;">{{ $p->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p->client_name }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $p->rugs_count }}</td>
                <td class="px-6 py-3 text-right" style="font-size:13px; color:rgba(18,18,18,0.45);">{{ $p->updated_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="px-6 py-8 text-center">
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No projects yet. <a href="{{ route('trade.portal.projects') }}" style="color:#B8860B; font-weight:500;">Create your first project →</a></p>
    </div>
    @endif
</div>

@endsection
