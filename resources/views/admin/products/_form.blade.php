<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="md:col-span-2">
        <label class="form-label">Product Name *</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-input" required>
        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="form-label">Price (USD) *</label>
        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" class="form-input" required>
    </div>
    <div>
        <label class="form-label">Sale Price (optional)</label>
        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0" class="form-input">
    </div>
    <div>
        <label class="form-label">Category</label>
        <select name="category_id" class="form-input">
            <option value="">— None —</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" class="form-input">
    </div>
    <div>
        <label class="form-label">Material</label>
        <input type="text" name="material" value="{{ old('material', $product->material ?? '') }}" class="form-input" placeholder="e.g. Wool, Silk, Nylon">
    </div>
    <div>
        <label class="form-label">Origin</label>
        <input type="text" name="origin" value="{{ old('origin', $product->origin ?? '') }}" class="form-input" placeholder="e.g. India, Turkey">
    </div>
    <div>
        <label class="form-label">Dimensions</label>
        <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions ?? '') }}" class="form-input" placeholder="e.g. 8' × 10'">
    </div>
    <div>
        <label class="form-label">Style</label>
        <input type="text" name="style" value="{{ old('style', $product->style ?? '') }}" class="form-input" placeholder="e.g. Traditional, Modern">
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input">
            <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="draft" {{ old('status', $product->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-input resize-none">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Details</label>
        <textarea name="details" rows="3" class="form-input resize-none">{{ old('details', $product->details ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Care Instructions</label>
        <textarea name="care_instructions" rows="2" class="form-input resize-none">{{ old('care_instructions', $product->care_instructions ?? '') }}</textarea>
    </div>
    <div class="md:col-span-2 flex flex-wrap gap-6">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="featured" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }} class="rounded">
            <span class="text-sm text-stone-700">Featured</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_bestseller" {{ old('is_bestseller', $product->is_bestseller ?? false) ? 'checked' : '' }} class="rounded">
            <span class="text-sm text-stone-700">Best Seller</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_new_arrival" {{ old('is_new_arrival', $product->is_new_arrival ?? false) ? 'checked' : '' }} class="rounded">
            <span class="text-sm text-stone-700">New Arrival</span>
        </label>
    </div>
    <div class="md:col-span-2">
        <label class="form-label">Images</label>
        <input type="file" name="images[]" multiple accept="image/*" class="block w-full text-sm text-stone-600 file:mr-4 file:py-2 file:px-4 file:border file:border-stone-300 file:text-sm file:bg-stone-50 hover:file:bg-stone-100">
        @if(isset($product) && $product->images->count())
        <div class="flex flex-wrap gap-2 mt-3">
            @foreach($product->images as $img)
            <img src="{{ $img->url }}" class="w-16 h-16 object-cover rounded border border-stone-200">
            @endforeach
        </div>
        @endif
    </div>

    {{-- Color swatches --}}
    <div class="md:col-span-2" x-data="{ colors: {{ json_encode(isset($product) ? $product->colors->map(fn($c)=>['name'=>$c->color_name,'hex'=>$c->color_hex])->toArray() : [['name'=>'','hex'=>'#000000']]) }} }">
        <label class="form-label mb-2">Colors</label>
        <template x-for="(color, i) in colors" :key="i">
            <div class="flex items-center gap-3 mb-2">
                <input type="color" x-model="color.hex" :name="'color_hexes['+i+']'" class="w-10 h-10 cursor-pointer border border-stone-200 rounded">
                <input type="text" x-model="color.name" :name="'color_names['+i+']'" placeholder="Color name" class="form-input flex-1 py-2">
                <button type="button" @click="colors.splice(i,1)" class="text-red-400 hover:text-red-600 text-sm">✕</button>
            </div>
        </template>
        <button type="button" @click="colors.push({name:'',hex:'#999999'})" class="text-xs text-stone-500 hover:text-stone-900 underline mt-1">+ Add color</button>
    </div>
</div>
