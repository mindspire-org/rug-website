<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeProject extends Model
{
    protected $fillable = [
        'user_id', 'name', 'client_name', 'room', 'status', 'rugs_count', 'total_value',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quotes()
    {
        return $this->hasMany(TradeQuote::class, 'project_id');
    }
}
