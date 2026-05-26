@extends('layouts.trade')
@section('title', 'Account')

@section('trade-content')

<div class="mb-6">
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#121212;">Account</h1>
    <p style="font-size:14px; color:rgba(18,18,18,0.55); margin-top:4px;">Manage your profile and preferences</p>
</div>

<div class="max-w-[720px] space-y-5">

    {{-- Profile Information --}}
    <div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 style="font-size:16px; font-weight:600; color:#121212;">Profile Information</h2>
        </div>
        @php
        $fields = [
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
             'label'=>'NAME',   'value'=>Auth::user()->name],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21H5a2 2 0 0 1-2-2V7l4-4h10l4 4v12a2 2 0 0 1-2 2zM9 21V12h6v9"/>',
             'label'=>'COMPANY','value'=>'Studio Interiors'],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/>',
             'label'=>'EMAIL',  'value'=>Auth::user()->email],
            ['icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .948.684l1.498 4.493a1 1 0 0 1-.502 1.21l-2.257 1.13a11.042 11.042 0 0 0 5.516 5.516l1.13-2.257a1 1 0 0 1 1.21-.502l4.493 1.498a1 1 0 0 1 .684.949V19a2 2 0 0 1-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
             'label'=>'PHONE',  'value'=>'(212) 555-0147'],
        ];
        @endphp
        @foreach($fields as $f)
        <div class="px-6 py-4 flex items-center gap-4" style="border-bottom:1px solid rgba(18,18,18,0.06);">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
            <div class="flex-1 min-w-0">
                <p style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);" class="mb-0.5">{{ $f['label'] }}</p>
                <p style="font-size:14px; color:#121212;">{{ $f['value'] }}</p>
            </div>
            <button style="font-size:13px; font-weight:500; color:#B8860B;" class="hover:underline flex-shrink-0">Edit</button>
        </div>
        @endforeach
    </div>

    {{-- Saved Addresses --}}
    <div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 style="font-size:16px; font-weight:600; color:#121212;">Saved Addresses</h2>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                </svg>
                <div>
                    <p style="font-size:14px; font-weight:500; color:#121212;" class="mb-1">Primary Studio</p>
                    <p style="font-size:14px; color:rgba(18,18,18,0.65); line-height:1.6;">
                        142 East 71st Street, Suite 4B<br>
                        New York, NY 10021
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Discount Tier --}}
    <div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
        <div class="px-6 py-5 flex items-center gap-4">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
            <div>
                <p style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);" class="mb-1">DISCOUNT TIER</p>
                <p style="font-family:'Lusitana',serif; font-size:18px; font-weight:700; color:#121212;">Gold — 25% Off MSRP</p>
            </div>
        </div>
    </div>

</div>

@endsection
