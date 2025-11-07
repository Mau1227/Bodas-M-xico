<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // 1. Array Fillable
    protected $fillable = [
        'user_id', 'groom_name', 'bride_name', 'wedding_date', 'ceremony_time',
        'ceremony_venue_name', 'ceremony_venue_address', 'ceremony_maps_link',
        'reception_venue_name', 'reception_venue_address', 'reception_maps_link',
        'reception_time', 'welcome_message', 'dress_code', 'additional_info',
        'custom_url_slug', 'template_id', 'primary_color', 'secondary_color',
        'cover_photo_url', 'music_url', 'hashtag', 'status',
    ];

    // 2. Relaciones
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function guests() {
        return $this->hasMany(Guest::class);
    }

    public function eventPhotos() {
        return $this->hasMany(EventPhoto::class);
    }
    
    // ...Aquí pones las demás: itinerary(), registryLinks(), etc.
}