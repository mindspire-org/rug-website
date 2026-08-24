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

    // Served through the /media/{path} route rather than asset('storage/...') so
    // images still resolve when the public/storage symlink is missing (shared hosting).
    public function getRoomPhotoUrlAttribute()
    {
        return $this->room_photo_path ? route('media.show', ['path' => $this->room_photo_path]) : null;
    }

    public function getGeneratedImageUrlAttribute()
    {
        return $this->generated_image_path ? route('media.show', ['path' => $this->generated_image_path]) : null;
    }
}
