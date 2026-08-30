@extends('layouts.admin')
@section('title', 'Customers')

@section('admin-content')
<div class="flex items-center justify-between mb-5">
    <div>
        <h1 style="font-size:20px; font-weight:700; color:#0f172a;">Customers</h1>
        <p style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ $customers->total() }} registered accounts</p>
    </div>
</div>

<form method="GET" class="flex flex-wrap gap-2 mb-5">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…"
           style="padding:8px 12px; font-size:13px; border:1.5px solid #e2e8f0; border-radius:6px; outline:none; min-width:240px;"
           onfocus="this.style.borderColor='#E8651A'" onblur="this.style.borderColor='#e2e8f0'">
    <button type="submit"
            style="padding:8px 16px; font-size:13px; font-weight:600; background:#0f172a; color:#fff; border-radius:6px; border:none; cursor:pointer;">
        Search
    </button>
    @if(request('search'))
    <a href="{{ route('admin.customers.index') }}"
       style="padding:8px 16px; font-size:13px; font-weight:500; background:#fff; color:#64748b; border-radius:6px; border:1.5px solid #e2e8f0; text-decoration:none; display:flex; align-items:center;">
        Clear
    </a>
    @endif
</form>

<div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
    <table class="w-full">
        <thead style="background:#f8fafc; border-bottom:1px solid #f1f5f9;">
            <tr>
                <th class="text-left px-5 py-3" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Customer</th>
                <th class="text-left px-5 py-3 hidden sm:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Email</th>
                <th class="text-left px-5 py-3 hidden md:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Orders</th>
                <th class="text-left px-5 py-3 hidden md:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Role</th>
                <th class="text-left px-5 py-3 hidden lg:table-cell" style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Joined</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr class="border-t border-stone-50 hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                             style="background:#0f172a;">
                            <span style="font-size:12px; font-weight:700; color:#fff;">{{ strtoupper(substr($customer->name,0,1)) }}</span>
                        </div>
                        <p style="font-size:13px; font-weight:600; color:#0f172a;">{{ $customer->name }}</p>
                    </div>
                </td>
                <td class="px-5 py-3.5 hidden sm:table-cell" style="font-size:13px; color:#374151;">{{ $customer->email }}</td>
                <td class="px-5 py-3.5 hidden md:table-cell">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full"
                          style="background:#eff6ff; font-size:11px; font-weight:600; color:#1d4ed8;">
                        {{ $customer->orders_count ?? 0 }} orders
                    </span>
                </td>
                <td class="px-5 py-3.5 hidden md:table-cell">
                    @php $badge = $customer->roleBadge(); @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full"
                          style="background:{{ $badge['bg'] }}; font-size:11px; font-weight:600; color:{{ $badge['color'] }};">
                        {{ $badge['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3.5 hidden lg:table-cell" style="font-size:12px; color:#64748b;">{{ $customer->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-3.5 text-right">
                    <a href="{{ route('admin.customers.show', $customer) }}"
                       style="font-size:12px; color:#E8651A; font-weight:500; text-decoration:none;" class="hover:underline">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-12 text-center" style="font-size:13px; color:#94a3b8;">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-5">{{ $customers->links() }}</div>
@endsection
