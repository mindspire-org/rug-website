<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- ══════════════════════════════════════════
         LEFT COLUMN — Main Info
      ══════════════════════════════════════════ --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Section: Basic Info --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-5">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Product Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;"
                           placeholder="e.g. Persian Heritage Wool Rug">
                    @error('name')<p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all resize-none"
                              style="color:#0f172a;"
                              placeholder="Describe the rug's design, texture, and story…">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Details</label>
                        <textarea name="details" rows="3"
                                  class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all resize-none"
                                  style="color:#0f172a;"
                                  placeholder="Construction, knot count, pile height…">{{ old('details', $product->details ?? '') }}</textarea>
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Care Instructions</label>
                        <textarea name="care_instructions" rows="3"
                                  class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all resize-none"
                                  style="color:#0f172a;"
                                  placeholder="Cleaning and maintenance guidance…">{{ old('care_instructions', $product->care_instructions ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Pricing --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-5">Pricing</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Price (USD) <span style="color:#ef4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">$</span>
                        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" step="0.01" min="0" required
                               class="w-full pl-7 pr-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                               style="color:#0f172a;"
                               placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Sale Price</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">$</span>
                        <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price ?? '') }}" step="0.01" min="0"
                               class="w-full pl-7 pr-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                               style="color:#0f172a;"
                               placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Stock Quantity</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;">
                </div>
            </div>
        </div>

        {{-- Section: Attributes --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-5">Attributes</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Category</label>
                    <select name="category_id"
                            class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all bg-white"
                            style="color:#0f172a;">
                        <option value="">— None —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Status</label>
                    <select name="status"
                            class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all bg-white"
                            style="color:#0f172a;">
                        <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ old('status', $product->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Material</label>
                    <input type="text" name="material" value="{{ old('material', $product->material ?? '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;"
                           placeholder="e.g. Wool, Silk">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Origin</label>
                    <input type="text" name="origin" value="{{ old('origin', $product->origin ?? '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;"
                           placeholder="e.g. India, Turkey">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Dimensions</label>
                    <input type="text" name="dimensions" value="{{ old('dimensions', $product->dimensions ?? '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;"
                           placeholder="e.g. 8' × 10'">
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Style</label>
                    <input type="text" name="style" value="{{ old('style', $product->style ?? '') }}"
                           class="w-full px-4 py-2.5 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100 transition-all"
                           style="color:#0f172a;"
                           placeholder="e.g. Traditional, Modern">
                </div>
            </div>
        </div>

        {{-- Section: Visibility Flags --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-4">Visibility</h3>
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-lg border border-stone-200 hover:border-amber-300 transition-colors"
                       :class="document.querySelector('input[name=featured]').checked ? 'border-amber-400 bg-amber-50' : ''">
                    <input type="checkbox" name="featured" {{ old('featured', $product->featured ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-stone-300 text-amber-500 focus:ring-amber-400"
                           onchange="this.closest('label').style.borderColor = this.checked ? '#fbbf24' : '#e5e7eb'; this.closest('label').style.background = this.checked ? '#fffbeb' : '';">
                    <span style="font-size:13px; font-weight:500; color:#374151;">Featured</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-lg border border-stone-200 hover:border-amber-300 transition-colors"
                       :class="document.querySelector('input[name=is_bestseller]').checked ? 'border-amber-400 bg-amber-50' : ''">
                    <input type="checkbox" name="is_bestseller" {{ old('is_bestseller', $product->is_bestseller ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-stone-300 text-amber-500 focus:ring-amber-400"
                           onchange="this.closest('label').style.borderColor = this.checked ? '#fbbf24' : '#e5e7eb'; this.closest('label').style.background = this.checked ? '#fffbeb' : '';">
                    <span style="font-size:13px; font-weight:500; color:#374151;">Best Seller</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-lg border border-stone-200 hover:border-amber-300 transition-colors"
                       :class="document.querySelector('input[name=is_new_arrival]').checked ? 'border-amber-400 bg-amber-50' : ''">
                    <input type="checkbox" name="is_new_arrival" {{ old('is_new_arrival', $product->is_new_arrival ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-stone-300 text-amber-500 focus:ring-amber-400"
                           onchange="this.closest('label').style.borderColor = this.checked ? '#fbbf24' : '#e5e7eb'; this.closest('label').style.background = this.checked ? '#fffbeb' : '';">
                    <span style="font-size:13px; font-weight:500; color:#374151;">New Arrival</span>
                </label>
            </div>
        </div>

        {{-- Section: Colors --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6"
             x-data="{ colors: {{ json_encode(isset($product) ? $product->colors->map(fn($c)=>['name'=>$c->color_name,'hex'=>$c->color_hex])->toArray() : [['name'=>'','hex'=>'#000000']]) }} }">
            <div class="flex items-center justify-between mb-4">
                <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;">Color Swatches</h3>
                <button type="button" @click="colors.push({name:'',hex:'#999999'})"
                        class="inline-flex items-center gap-1 text-sm font-medium transition-colors hover:opacity-80"
                        style="color:#E8651A;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Color
                </button>
            </div>
            <div class="space-y-3">
                <template x-for="(color, i) in colors" :key="i">
                    <div class="flex items-center gap-3">
                        <input type="color" x-model="color.hex" :name="'color_hexes['+i+']'"
                               class="w-10 h-10 cursor-pointer border border-stone-200 rounded-lg overflow-hidden flex-shrink-0"
                               style="padding:0;">
                        <input type="text" x-model="color.name" :name="'color_names['+i+']'" placeholder="Color name"
                               class="flex-1 px-4 py-2 text-sm border border-stone-200 rounded-lg focus:outline-none focus:border-amber-400 transition-all"
                               style="color:#0f172a;">
                        <button type="button" @click="colors.splice(i,1)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         RIGHT COLUMN — Media & Images
      ══════════════════════════════════════════ --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Image Upload --}}
        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h3 style="font-family:'Lusitana',serif; font-size:16px; font-weight:700; color:#0f172a;" class="mb-4">Product Images</h3>

            {{-- Upload dropzone --}}
            <div class="relative">
                <input type="file" name="images[]" id="productImages" multiple accept="image/*"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                       onchange="previewImages(this)">
                <div class="border-2 border-dashed border-stone-200 rounded-xl p-8 text-center hover:border-amber-400 hover:bg-amber-50/30 transition-all"
                     id="dropzone">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center" style="background:#fef3c7;">
                        <svg class="w-6 h-6" style="color:#E8651A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p style="font-size:13px; font-weight:500; color:#374151;">Click or drag images here</p>
                    <p style="font-size:12px; color:#9ca3af; margin-top:4px;">PNG, JPG, WEBP up to 5MB each</p>
                </div>
            </div>
            @error('images')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            @error('images.*')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror

            {{-- New file preview --}}
            <div id="newImagePreview" class="grid grid-cols-3 gap-2 mt-4"></div>

            {{-- Existing images --}}
            @if(isset($product) && $product->images->count())
            <div class="mt-6">
                <p style="font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;" class="mb-3">Current Images</p>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($product->images as $img)
                    <div class="relative group rounded-lg overflow-hidden border border-stone-200" style="aspect-ratio:1/1;">
                        <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover">
                        @if($img->is_primary)
                        <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background:#E8651A; color:#fff;">Primary</div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            @if(!$img->is_primary)
                            <a href="{{ route('admin.products.images.primary', [$product, $img]) }}"
                               class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-stone-700 hover:text-amber-600 transition-colors"
                               title="Set as primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </a>
                            @endif
                            <a href="{{ route('admin.products.images.destroy', [$product, $img]) }}"
                               onclick="return confirm('Delete this image?')"
                               class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-stone-700 hover:text-red-500 transition-colors"
                               title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function previewImages(input) {
    const container = document.getElementById('newImagePreview');
    container.innerHTML = '';
    if (input.files && input.files.length > 0) {
        for (let file of input.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative rounded-lg overflow-hidden border border-stone-200';
                div.style.aspectRatio = '1/1';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
        document.getElementById('dropzone').classList.add('border-amber-400', 'bg-amber-50/30');
    }
}
</script>
