<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'hotel_name',
        'distance',
        'phone',
        'maps_url',
        'website_url',
        'display_order',
    ];

    // Relación: Un hotel pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}