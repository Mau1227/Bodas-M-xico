<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    // 1. Array Fillable
    protected $fillable = [
        'event_id', 'full_name', 'email', 'phone', 'max_companions',
        'invitation_token', 'status', 'confirmed_companions',
        'dietary_restrictions', 'message_to_couple', 'confirmed_at',
        'invitation_sent_at',
    ];

    // 2. Relaciones
    public function event() {
        return $this->belongsTo(Event::class);
    }
}