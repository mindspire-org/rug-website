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

    /**
     * Best-effort estimate for a ZIP that falls outside every configured range.
     * Returns the price of the numerically closest range so the customer always
     * gets a real figure (marked "estimated") instead of a dead "no rate" message.
     */
    public static function nearest(?string $zip): ?float
    {
        if (!$zip) {
            return null;
        }
        $z = (int) preg_replace('/\D/', '', substr($zip, 0, 5));
        if ($z <= 0) {
            return null;
        }

        $best = null;
        $bestDist = PHP_INT_MAX;
        foreach (static::where('active', true)->get() as $r) {
            $start = (int) $r->zip_start;
            $end   = (int) $r->zip_end;
            if ($z >= $start && $z <= $end) {
                return (float) $r->price;
            }
            $dist = min(abs($z - $start), abs($z - $end));
            if ($dist < $bestDist) {
                $bestDist = $dist;
                $best = (float) $r->price;
            }
        }

        return $best;
    }
}
