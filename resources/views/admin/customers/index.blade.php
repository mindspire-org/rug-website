@extends('layouts.admin')
@section('title', 'Customers')

@section('admin-content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-serif text-2xl font-bold text-stone-900">Customers</h1>
</div>
<form method="GET" class="flex gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…" class="form-input w-72">
    <button type="submit" class="btn-dark text-sm px-4 py-2.5">Search</button>
</form>
<div class="bg-white border border-stone-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Name</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Email</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Joined</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @forelse($customers as $customer)
            <tr class="hover:bg-stone-50">
                <td class="px-4 py-3 font-medium text-stone-900">{{ $customer->name }}</td>
                <td class="px-4 py-3 text-stone-600">{{ $customer->email }}</td>
                <td class="px-4 py-3 text-stone-500 text-xs">{{ $customer->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs text-stone-500 hover:text-stone-900">View →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-10 text-center text-stone-400">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $customers->links() }}</div>
@endsection
