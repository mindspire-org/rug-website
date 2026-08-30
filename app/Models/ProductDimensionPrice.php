<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDimensionPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'label',
        'width',
        'length',
        'shape',
        'price',
        'sale_price',
        'stock',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'width' => 'decimal:2',
        'length' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->effective_price, 0);
    }

    public function getDimensionDisplayAttribute()
    {
        if ($this->label) {
            return $this->label;
        }
        $parts = [];
        if ($this->width) $parts[] = $this->width . "'";
        if ($this->length) $parts[] = $this->length . "'";
        if ($this->shape) $parts[] = '(' . ucfirst($this->shape) . ')';
        return implode(' x ', array_filter($parts)) ?: 'Standard';
    }
}
