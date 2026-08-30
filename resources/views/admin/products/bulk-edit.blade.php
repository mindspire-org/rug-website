@extends('layouts.admin')
@section('title', 'Bulk Edit Products')

@section('admin-content')

<div class="mb-6">
    <h1 style="font-family:'Lusitana',serif; font-size:28px; font-weight:700; color:#0f172a;">Bulk Edit Products</h1>
    <p style="font-size:13px; color:#64748b; margin-top:3px;">Editing {{ count($products) }} products</p>
</div>

<div class="bg-white rounded-xl border border-stone-200 p-6">
    <form action="{{ route('admin.products.bulk-update') }}" method="POST">
        @csrf @method('PUT')
        
        {{-- Hidden product IDs --}}
        @foreach($products as $product)
        <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
        @endforeach
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            
            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">Status</label>
                <select name="status" 
                        class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <option value="">-- Don't Change --</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="draft">Draft</option>
                </select>
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current status</p>
            </div>
            
            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">Category</label>
                <select name="category_id" 
                        class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <option value="">-- Don't Change --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current category</p>
            </div>
            
            {{-- Stock Quantity --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">Stock Quantity</label>
                <input type="number" name="stock" min="0" placeholder="-- Don't Change --"
                       class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current stock quantity</p>
            </div>
            
            {{-- Signature Designs --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">Signature Designs</label>
                <select name="featured"
                        class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <option value="">-- Don't Change --</option>
                    <option value="1">Yes - Add to Signature</option>
                    <option value="0">No - Remove from Signature</option>
                </select>
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current signature status</p>
            </div>
            
            {{-- Best Sellers --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">Best Sellers</label>
                <select name="is_bestseller"
                        class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <option value="">-- Don't Change --</option>
                    <option value="1">Yes - Add to Best Sellers</option>
                    <option value="0">No - Remove from Best Sellers</option>
                </select>
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current bestseller status</p>
            </div>
            
            {{-- New Arrivals --}}
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-2">New Arrivals</label>
                <select name="is_new_arrival"
                        class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <option value="">-- Don't Change --</option>
                    <option value="1">Yes - Add to New Arrivals</option>
                    <option value="0">No - Remove from New Arrivals</option>
                </select>
                <p class="text-xs text-stone-500 mt-1">Leave blank to keep current new arrival status</p>
            </div>
        </div>
        
        {{-- Price Adjustment --}}
        <div class="border-t border-stone-200 pt-6 mb-6">
            <h3 class="text-sm font-semibold text-stone-800 mb-4">Price Adjustment (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Adjustment Amount</label>
                    <input type="number" name="price_adjustment" step="0.01" placeholder="0.00"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                    <p class="text-xs text-stone-500 mt-1">Enter positive or negative amount</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-2">Adjustment Type</label>
                    <select name="price_adjustment_type" 
                            class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400">
                        <option value="fixed">Fixed Amount ($)</option>
                        <option value="percent">Percentage (%)</option>
                    </select>
                    <p class="text-xs text-stone-500 mt-1">Choose how to apply the adjustment</p>
                </div>
            </div>
            <div class="mt-3 p-3 bg-amber-50 rounded-lg">
                <p class="text-xs text-amber-800">
                    <strong>Examples:</strong><br>
                    Fixed: Enter "10" to add $10 to each product price<br>
                    Fixed: Enter "-5" to subtract $5 from each product price<br>
                    Percent: Enter "10" to increase prices by 10%<br>
                    Percent: Enter "-10" to decrease prices by 10%
                </p>
            </div>
        </div>
        
        {{-- Selected Products List --}}
        <div class="border-t border-stone-200 pt-6 mb-6">
            <h3 class="text-sm font-semibold text-stone-800 mb-4">Selected Products</h3>
            <div class="max-h-60 overflow-y-auto border border-stone-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-medium text-stone-600">Product</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-stone-600">Current Price</th>
                            <th class="text-left px-4 py-2 text-xs font-medium text-stone-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach($products as $product)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $product->primary_image_url }}" alt="" class="w-8 h-8 rounded object-cover">
                                    <span class="font-medium text-stone-700">{{ Str::limit($product->name, 40) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-stone-600">
                                @if($product->sale_price)
                                <span class="text-red-600">${{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-stone-400 line-through text-xs">${{ number_format($product->price, 2) }}</span>
                                @else
                                ${{ number_format($product->price, 2) }}
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-600' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pt-6 border-t border-stone-200">
            <a href="{{ route('admin.products.index') }}" 
               class="px-5 py-2.5 text-sm font-medium text-stone-600 hover:text-stone-900 transition-colors">
                Cancel
            </a>
            <div class="flex items-center gap-3">
                <span class="text-sm text-stone-500">{{ count($products) }} products will be updated</span>
                <button type="submit" 
                        class="px-6 py-2.5 bg-amber-600 text-white rounded-lg font-medium hover:bg-amber-700 transition-colors"
                        onclick="return confirm('Update {{ count($products) }} products?')">
                    Update Products
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
