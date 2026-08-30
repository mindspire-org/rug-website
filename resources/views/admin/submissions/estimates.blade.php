@extends('layouts.admin')
@section('title', 'Saved Estimates')

@section('admin-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Saved Estimates</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Customer estimate submissions</p>
    </div>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">DATE</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">CUSTOMER</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">PRODUCT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">SIZE</th>
                <th class="px-4 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ESTIMATE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estimates as $e)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3" style="font-size:13px; color:rgba(18,18,18,0.55);">{{ $e->created_at->format('M j, Y') }}</td>
                <td class="px-4 py-3">
                    <p style="font-size:14px; font-weight:500; color:#121212;">{{ $e->user->name ?? 'Guest' }}</p>
                    <p style="font-size:12px; color:rgba(18,18,18,0.45);">{{ $e->user->email ?? '—' }}</p>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $e->product->name ?? '—' }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $e->size ?? '—' }}</td>
                <td class="px-4 py-3 text-right" style="font-size:14px; font-weight:500; color:#121212;">${{ number_format($e->estimated_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-5 py-3 border-t border-stone-100">{{ $estimates->links() }}</div>
    @if($estimates->isEmpty())
    <div class="px-6 py-12 text-center">
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No saved estimates yet.</p>
    </div>
    @endif
</div>

@endsection
