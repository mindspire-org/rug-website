<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleRequest extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'rug_name', 'color', 'status', 'tracking_number',
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
