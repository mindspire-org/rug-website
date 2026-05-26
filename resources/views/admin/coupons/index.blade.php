@extends('layouts.admin')
@section('title', 'Coupons')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Coupons</h1>
    <a href="{{ route('admin.coupons.create') }}" class="btn-dark text-sm px-4 py-2">+ Add Coupon</a>
</div>
<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Code</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Type</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Value</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Uses Left</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Expires</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($coupons as $coupon)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-mono font-semibold text-stone-900">{{ $coupon->code }}</td>
                <td class="px-4 py-3 text-stone-600 capitalize">{{ $coupon->type }}</td>
                <td class="px-4 py-3 font-medium">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '$'.number_format($coupon->value,0) }}</td>
                <td class="px-4 py-3 text-stone-500">{{ $coupon->uses_left ?? '∞' }}</td>
                <td class="px-4 py-3 text-stone-500 text-xs">{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : '—' }}</td>
                <td class="px-4 py-3">
                    <span class="badge {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs text-stone-500 hover:text-stone-900">Edit</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-10 text-center text-stone-400">No coupons yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $coupons->links() }}</div>
@endsection
