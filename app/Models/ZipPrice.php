<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZipPrice extends Model
{
    protected $fillable = ['label', 'zip_start', 'zip_end', 'price', 'active'];

    protected $casts = [
        'price'  => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Find the shipping price for a given ZIP code by matching it against the
     * configured ranges. Numeric-prefix comparison (handles ZIP+4 too).
     * Returns null when no active range matches.
     */
    public static function lookup(?string $zip): ?float
    {
        if (!$zip) {
            return null;
        }
        $z = (int) preg_replace('/\D/', '', substr($zip, 0, 5));
        if ($z <= 0) {
            return null;
        }

        $match = static::where('active', true)
            ->whereRaw('CAST(zip_start AS UNSIGNED) <= ?', [$z])
            ->whereRaw('CAST(zip_end AS UNSIGNED) >= ?', [$z])
            ->orderByRaw('(CAST(zip_end AS UNSIGNED) - CAST(zip_start AS UNSIGNED))')
            ->first();

        return $match ? (float) $match->price : null;
    }
}
