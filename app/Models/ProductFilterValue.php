<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_filter_attribute_id',
        'value',
        'display_value',
    ];

    /**
     * Get the filter attribute this value belongs to
     */
    public function attribute()
    {
        return $this->belongsTo(ProductFilterAttribute::class, 'product_filter_attribute_id');
    }

    /**
     * Get products that have this filter value
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_filter_value_product')
            ->withTimestamps();
    }
}
