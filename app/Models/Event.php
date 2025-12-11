<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'cover_photo_url', 'music_url', 'hashtag', 'status', 'event_type',
        'event_title', 'host_names'
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

    public function itineraryItems() {
        return $this->hasMany(Itinerary::class);
    }

    public function template() {
        return $this->belongsTo(Template::class);
    }

    protected function isWedding(): Attribute
    {
        return Attribute::get(fn () => $this->event_type === 'wedding' || is_null($this->event_type));
    }

    protected function displayTitle(): Attribute
    {
        return Attribute::get(function () {
            if ($this->event_title) {
                return $this->event_title;
            }

            // Fallback para bodas
            if ($this->is_wedding) {
                return trim($this->groom_name . ' & ' . $this->bride_name);
            }

            return 'Mi evento';
        });
    }

    protected function displayHosts(): Attribute
    {
        return Attribute::get(function () {
            if ($this->is_wedding) {
                return trim($this->groom_name . ' & ' . $this->bride_name);
            }

            return $this->host_names ?: $this->display_title;
        });
    }

    public function mainDateTime(): Carbon
{
    $rawDate  = (string) $this->wedding_date;
    $onlyDate = substr($rawDate, 0, 10);

    try {
        $baseDate = Carbon::createFromFormat('Y-m-d', $onlyDate);
    } catch (\Exception $e) {
        $baseDate = Carbon::today();
    }

    if (!empty($this->ceremony_time)) {
        return $baseDate->copy()->setTimeFromTimeString($this->ceremony_time);
    }

    if (!empty($this->reception_time)) {
        return $baseDate->copy()->setTimeFromTimeString($this->reception_time);
    }

    return $baseDate->startOfDay();
}

    protected $casts = [
    'wedding_date' => 'date',
    ];


    
    // ...Aquí pones las demás: itinerary(), registryLinks(), etc.
}