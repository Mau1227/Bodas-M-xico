<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'question',
        'answer',
        'display_order',
    ];

    // Relación: Una FAQ pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}