<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order', 'uses_left', 'expires_at', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->uses_left !== null && $this->uses_left <= 0) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) return 0;
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min($this->value, $subtotal);
    }
}
