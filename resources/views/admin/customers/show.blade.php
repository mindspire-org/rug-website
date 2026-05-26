@extends('layouts.admin')
@section('title', $user->name)

@section('admin-content')
<div class="flex items-center gap-3 mb-8">
    <a href="{{ route('admin.customers.index') }}" class="text-stone-400 hover:text-stone-900">←</a>
    <h1 class="font-serif text-2xl font-bold">{{ $user->name }}</h1>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-3">Contact</h2>
        <p class="text-sm text-stone-700">{{ $user->email }}</p>
        <p class="text-sm text-stone-500 mt-1">{{ $user->phone ?? '—' }}</p>
        <p class="text-xs text-stone-400 mt-2">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-3">Orders</h2>
        <p class="text-3xl font-bold text-stone-900">{{ $user->orders->count() }}</p>
        <p class="text-xs text-stone-500 mt-1">Total orders placed</p>
    </div>
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-3">Lifetime Value</h2>
        <p class="text-3xl font-bold text-green-600">${{ number_format($user->orders->where('payment_status','paid')->sum('total'), 0) }}</p>
        <p class="text-xs text-stone-500 mt-1">Total paid orders</p>
    </div>
</div>

<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <div class="px-5 py-4 border-b border-stone-100 font-semibold text-stone-900">Order History</div>
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="text-left px-5 py-2.5 text-xs text-stone-500 uppercase">Order</th>
                <th class="text-left px-5 py-2.5 text-xs text-stone-500 uppercase">Date</th>
                <th class="text-left px-5 py-2.5 text-xs text-stone-500 uppercase">Status</th>
                <th class="text-right px-5 py-2.5 text-xs text-stone-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @forelse($user->orders as $order)
            <tr>
                <td class="px-5 py-3 font-medium text-stone-900">
                    <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->order_number }}</a>
                </td>
                <td class="px-5 py-3 text-stone-500 text-xs">{{ $order->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-3"><span class="badge {{ $order->status_badge }} capitalize">{{ $order->status }}</span></td>
                <td class="px-5 py-3 text-right font-semibold">${{ number_format($order->total, 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-stone-400">No orders.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
