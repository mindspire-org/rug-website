<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    /** Delivery method prices (must match the cart UI). */
    public const DELIVERY_PRICES = ['whiteglove' => 250, 'ups' => 500, 'pickup' => 50];

    /** Add-on service prices (must match the cart UI). */
    public const ADDON_PRICES = ['protector' => 120, 'padding' => 190, 'spot' => 19.99];

    public static function deliveryCost(?string $method): float
    {
        return (float) (self::DELIVERY_PRICES[$method] ?? 0);
    }

    public static function addonsCost($addons): float
    {
        $sum = 0;
        foreach ((array) $addons as $a) {
            $sum += self::ADDON_PRICES[$a] ?? 0;
        }
        return (float) $sum;
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }

    public function getCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}
