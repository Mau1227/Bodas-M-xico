<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistryLink extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'store_name',
        'registry_number',
        'url',
        'display_order',
    ];

    // Relación: Un link de registro pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}