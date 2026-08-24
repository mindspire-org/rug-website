<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'sku', 'description', 'details', 'care_instructions',
        'price', 'sale_price', 'category_id', 'stock', 'featured',
        'is_bestseller', 'is_new_arrival', 'status', 'material',
        'origin', 'dimensions', 'style', 'refined_color',
        'construction', 'room_type', 'availability', 'size_category', 'use_type',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('approved', true);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function dimensionPrices()
    {
        return $this->hasMany(ProductDimensionPrice::class)->orderBy('sort_order');
    }

    public function defaultDimension()
    {
        return $this->hasOne(ProductDimensionPrice::class)->where('is_default', true);
    }

    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Display name = product name + its specific swatch colour, but only when the
     * colour isn't already part of the name. This reads as "Aster Grove — Blue"
     * for base-named products, and simply "Bowery Light Grey" for products that
     * already carry the colour in their name. refined_color is deliberately ignored
     * here because it holds the filter bucket (e.g. "Neutrals"), not the real colour.
     */
    public function getDisplayNameAttribute()
    {
        // Strip a trailing SKU code from the name (e.g. "Haven HV-134" -> "Haven",
        // "Boho Plus 172505" -> "Boho Plus", "Lilah LL - 750" -> "Lilah").
        $name = preg_replace('/\s+[A-Z]{2,3}\s*-?\s*\d{2,5}$/', '', $this->name);
        $name = preg_replace('/\s+\d{4,6}$/', '', $name);
        $name = trim($name, " -\t");
        if ($name === '') {
            $name = $this->name;
        }

        $colour = $this->relationLoaded('colors')
            ? optional($this->colors->first())->color_name
            : optional($this->colors()->first())->color_name;

        if (empty($colour) || stripos($name, $colour) !== false) {
            return $name;
        }

        return $name . ' — ' . $colour;
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->effective_price, 0);
    }

    public function getPrimaryImageUrlAttribute()
    {
        $img = $this->primaryImage;
        if ($img) {
            return route('media.show', ['path' => $img->path]);
        }

        // Try any available image
        $first = $this->images->first();
        if ($first) {
            return route('media.show', ['path' => $first->path]);
        }

        // Neutral branded placeholder (1.5 KB SVG). Deliberately NOT the cover
        // photo — showing a styled room shot for a product with no photography
        // misrepresents the product and ships 1.6 MB per card.
        return asset('images/placeholder-rug.svg');
    }

    /**
     * Check if image file actually exists on disk.
     * Tries public/storage/ first (web-facing) then storage/app/public/ (symlink target).
     */
    private function imageFileExists($path)
    {
        if (empty($path)) return false;

        // 1. Check public/storage/ — the path the web server actually serves
        $publicPath = public_path('storage/' . $path);
        if (file_exists($publicPath) && is_file($publicPath) && filesize($publicPath) > 100) {
            return true;
        }

        // 2. Check storage/app/public/ — Laravel's default storage location
        $storagePath = storage_path('app/public/' . $path);
        if (file_exists($storagePath) && is_file($storagePath) && filesize($storagePath) > 100) {
            return true;
        }

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeBestsellers($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeNewArrivals($query)
    {
        return $query->where('is_new_arrival', true);
    }

    /**
     * Get filter values associated with this product
     */
    public function filterValues()
    {
        return $this->belongsToMany(ProductFilterValue::class, 'product_filter_value_product')
            ->withPivot('product_filter_attribute_id')
            ->withTimestamps();
    }
}
