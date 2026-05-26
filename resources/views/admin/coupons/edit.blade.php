@extends('layouts.admin')
@section('title', 'Edit Coupon')

@section('admin-content')
<div class="flex items-center gap-3 mb-8">
    <a href="{{ route('admin.coupons.index') }}" class="text-stone-400 hover:text-stone-900">←</a>
    <h1 class="font-serif text-2xl font-bold">Edit: {{ $coupon->code }}</h1>
</div>
<form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="max-w-lg space-y-5">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Type *</label>
            <select name="type" class="form-input">
                <option value="percentage" {{ $coupon->type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed ($)</option>
            </select>
        </div>
        <div>
            <label class="form-label">Value *</label>
            <input type="number" name="value" value="{{ old('value', $coupon->value) }}" step="0.01" min="0" class="form-input" required>
        </div>
    </div>
    <div>
        <label class="form-label">Min Order Amount ($)</label>
        <input type="number" name="min_order" value="{{ old('min_order', $coupon->min_order) }}" step="0.01" min="0" class="form-input">
    </div>
    <div>
        <label class="form-label">Uses Left</label>
        <input type="number" name="uses_left" value="{{ old('uses_left', $coupon->uses_left) }}" min="0" class="form-input">
    </div>
    <div>
        <label class="form-label">Expires At</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="form-input">
    </div>
    <label class="flex items-center gap-2">
        <input type="checkbox" name="is_active" {{ $coupon->is_active ? 'checked' : '' }} class="rounded">
        <span class="text-sm text-stone-700">Active</span>
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn-dark px-8">Save Changes</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn-outline-dark px-6">Cancel</a>
    </div>
</form>
@endsection
