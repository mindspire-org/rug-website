<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'size', 'color', 'quantity', 'price', 'is_sample', 'custom_width', 'custom_length'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_sample' => 'boolean',
        'custom_width' => 'decimal:2',
        'custom_length' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getLineTotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
