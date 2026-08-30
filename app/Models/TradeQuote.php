<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeQuote extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'quote_number', 'status', 'items_count', 'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(TradeProject::class, 'project_id');
    }
}
