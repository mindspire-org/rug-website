<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    protected $fillable = [
        'type', 'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'state', 'zip', 'message', 'meta',
    ];

    protected $casts = ['meta' => 'array'];
}
