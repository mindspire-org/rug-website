@extends('layouts.admin')
@section('title', 'Orders')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Orders</h1>
</div>
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order number…" class="form-input w-56">
    <select name="status" class="form-input w-44">
        <option value="">All Statuses</option>
        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-dark text-sm px-4 py-2.5">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn-outline-dark text-sm px-4 py-2.5">Clear</a>
</form>
<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Order</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Customer</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Date</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Total</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($orders as $order)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-4 py-3 font-medium text-stone-900">{{ $order->order_number }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $order->user?->name ?? 'Guest' }}</td>
                <td class="px-4 py-3 text-stone-500 text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3 font-semibold">${{ number_format($order->total, 0) }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $order->status_badge }} capitalize">{{ $order->status }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-stone-500 hover:text-stone-900">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-10 text-center text-stone-400">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
