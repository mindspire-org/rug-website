<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedEstimate extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'size', 'color', 'finish',
        'add_ons', 'delivery_method', 'estimated_price', 'notes',
    ];

    protected $casts = [
        'add_ons' => 'array',
        'estimated_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
