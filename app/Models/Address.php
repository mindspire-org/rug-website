<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'type', 'full_name', 'phone', 'line1', 'line2', 'city', 'state', 'zip', 'country', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAttribute()
    {
        return "{$this->line1}" . ($this->line2 ? ", {$this->line2}" : '') . ", {$this->city}, {$this->state} {$this->zip}, {$this->country}";
    }
}
