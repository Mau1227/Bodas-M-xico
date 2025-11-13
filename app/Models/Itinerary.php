<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itinerary';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'time',
        'activity',
        'icon',
        'display_order',
    ];

    // Relación: Un item del itinerario pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}