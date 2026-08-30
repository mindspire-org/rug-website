<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all values for this filter attribute
     */
    public function values()
    {
        return $this->hasMany(ProductFilterValue::class);
    }

    /**
     * Get products that have this filter attribute
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter_value_product')
            ->withPivot('product_filter_value_id')
            ->withTimestamps();
    }
}
