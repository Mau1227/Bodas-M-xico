<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'photo_url',
        'display_order',
    ];

    // Relación: Una foto pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}