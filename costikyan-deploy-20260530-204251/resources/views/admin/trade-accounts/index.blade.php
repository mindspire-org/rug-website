@extends('layouts.admin')
@section('title', 'Trade Accounts')

@section('admin-content')

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 style="font-family:'Lusitana',serif; font-size:22px; font-weight:700; color:#0f172a;">Trade Accounts</h1>
        <p style="font-size:13px; color:#64748b; margin-top:4px;">Manage trade professional accounts and discounts</p>
    </div>
</div>

<div class="bg-white border border-stone-200 rounded-lg overflow-hidden">
    <table class="w-full">
        <thead>
            <tr style="border-bottom:1px solid rgba(18,18,18,0.08); background:#fafafa;">
                <th class="px-5 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">NAME</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">EMAIL</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">COMPANY</th>
                <th class="px-4 py-3 text-center" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">DISCOUNT</th>
                <th class="px-4 py-3 text-left" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">STATUS</th>
                <th class="px-5 py-3 text-right" style="font-size:11px; font-weight:600; letter-spacing:0.08em; color:rgba(18,18,18,0.45);">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accounts as $user)
            <tr style="border-bottom:1px solid rgba(18,18,18,0.06);" class="hover:bg-stone-50 transition-colors">
                <td class="px-5 py-3">
                    <p style="font-size:14px; font-weight:500; color:#121212;">{{ $user->name }}</p>
                    <p style="font-size:12px; color:rgba(18,18,18,0.45);">Joined {{ $user->created_at->format('M Y') }}</p>
                </td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $user->email }}</td>
                <td class="px-4 py-3" style="font-size:14px; color:rgba(18,18,18,0.7);">{{ $user->company_name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span style="font-size:14px; font-weight:600; color:#B8860B;">{{ $user->trade_discount ?? 0 }}%</span>
                </td>
                <td class="px-4 py-3">
                    @php $badge = $user->roleBadge(); @endphp
                    <span style="font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; background:{{ $badge['bg'] }}; color:{{ $badge['color'] }};">
                        {{ $badge['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <form action="{{ route('admin.trade-accounts.update', $user) }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="number" name="trade_discount" value="{{ $user->trade_discount ?? 0 }}" min="0" max="100"
                               class="w-14 px-1 py-1 text-xs border border-stone-200 rounded text-center"
                               style="color:#121212;">
                        <select name="role" class="text-xs border border-stone-200 rounded px-2 py-1 bg-white" style="color:#121212;">
                            <option value="trade" {{ $user->role === 'trade' ? 'selected' : '' }}>Trade</option>
                            <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Customer</option>
                        </select>
                        <button type="submit" class="text-xs px-2 py-1 rounded border border-stone-200 hover:bg-stone-50" style="color:#121212;">Save</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($accounts->isEmpty())
    <div class="px-6 py-12 text-center">
        <p style="font-size:14px; color:rgba(18,18,18,0.55);">No trade accounts found.</p>
    </div>
    @endif
</div>

@endsection
