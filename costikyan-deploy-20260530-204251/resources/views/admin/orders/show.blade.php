@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number)

@section('admin-content')
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.orders.index') }}" class="text-stone-400 hover:text-stone-900">←</a>
    <h1 class="font-serif text-2xl font-bold text-stone-900">Order {{ $order->order_number }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Status update --}}
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-4">Update Status</h2>
        <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex gap-3">
            @csrf @method('PATCH')
            <select name="status" class="form-input flex-1">
                @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-dark text-sm px-4">Update</button>
        </form>
    </div>

    {{-- Customer info --}}
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-3">Customer</h2>
        @if($order->user)
        <p class="text-sm font-medium">{{ $order->user->name }}</p>
        <p class="text-xs text-stone-500">{{ $order->user->email }}</p>
        @else
        <p class="text-sm text-stone-400">Guest order</p>
        @endif
    </div>

    {{-- Shipping address --}}
    <div class="bg-white border border-stone-200 rounded p-5">
        <h2 class="font-semibold text-stone-900 mb-3">Shipping Address</h2>
        @php $addr = $order->shipping_address; @endphp
        <p class="text-sm">{{ $addr['full_name'] ?? '' }}</p>
        <p class="text-sm text-stone-600">{{ $addr['line1'] ?? '' }}</p>
        <p class="text-sm text-stone-600">{{ ($addr['city'] ?? '') . ', ' . ($addr['state'] ?? '') . ' ' . ($addr['zip'] ?? '') }}</p>
    </div>
</div>

{{-- Items --}}
<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <div class="px-5 py-4 border-b border-stone-100 font-semibold text-stone-900">Order Items</div>
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-100">
            <tr>
                <th class="text-left px-5 py-2.5 text-xs text-stone-500 uppercase">Product</th>
                <th class="text-left px-5 py-2.5 text-xs text-stone-500 uppercase">Size / Color</th>
                <th class="text-center px-5 py-2.5 text-xs text-stone-500 uppercase">Qty</th>
                <th class="text-right px-5 py-2.5 text-xs text-stone-500 uppercase">Price</th>
                <th class="text-right px-5 py-2.5 text-xs text-stone-500 uppercase">Line Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-50">
            @foreach($order->items as $item)
            <tr>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        @if($item->product)
                        <div class="w-10 h-10 bg-stone-100 overflow-hidden rounded flex-shrink-0">
                            <img src="{{ $item->product->primary_image_url }}" class="w-full h-full object-cover">
                        </div>
                        @endif
                        <span class="font-medium text-stone-900">{{ $item->product_name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3 text-stone-500 text-xs">{{ $item->size }} {{ $item->color }}</td>
                <td class="px-5 py-3 text-center">{{ $item->quantity }}</td>
                <td class="px-5 py-3 text-right">${{ number_format($item->price, 0) }}</td>
                <td class="px-5 py-3 text-right font-semibold">${{ number_format($item->line_total, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="border-t border-stone-200 bg-stone-50">
            <tr><td colspan="4" class="px-5 py-2 text-right text-xs text-stone-500">Subtotal</td><td class="px-5 py-2 text-right text-sm">${{ number_format($order->subtotal, 0) }}</td></tr>
            @if($order->discount > 0)<tr><td colspan="4" class="px-5 py-2 text-right text-xs text-green-600">Discount</td><td class="px-5 py-2 text-right text-sm text-green-600">−${{ number_format($order->discount, 0) }}</td></tr>@endif
            <tr><td colspan="4" class="px-5 py-2 text-right text-xs text-stone-500">Shipping</td><td class="px-5 py-2 text-right text-sm">{{ $order->shipping > 0 ? '$'.number_format($order->shipping,0) : 'Free' }}</td></tr>
            <tr><td colspan="4" class="px-5 py-2 text-right text-xs text-stone-500">Tax</td><td class="px-5 py-2 text-right text-sm">${{ number_format($order->tax, 0) }}</td></tr>
            <tr class="font-bold"><td colspan="4" class="px-5 py-3 text-right">Total</td><td class="px-5 py-3 text-right">${{ number_format($order->total, 0) }}</td></tr>
        </tfoot>
    </table>
</div>
@endsection
