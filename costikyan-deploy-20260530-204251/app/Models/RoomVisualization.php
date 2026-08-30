<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomVisualization extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'room_photo_path', 'generated_image_path',
        'prompt', 'status', 'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getRoomPhotoUrlAttribute()
    {
        return $this->room_photo_path ? asset('storage/' . $this->room_photo_path) : null;
    }

    public function getGeneratedImageUrlAttribute()
    {
        return $this->generated_image_path ? asset('storage/' . $this->generated_image_path) : null;
    }
}
