<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'full_name',
        'email',
        'phone',
        'max_companions',
        'invitation_token',
        'status',
        'confirmed_companions',
        'dietary_restrictions',
        'message_to_couple',
        'confirmed_at',
        'invitation_sent_at',
    ];

    protected $casts = [
        'confirmed_at'      => 'datetime',
        'invitation_sent_at'=> 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // 🔗 URL única de invitación para este invitado
    public function getInvitationUrlAttribute()
    {
        $event = $this->event;

        if (! $event || empty($event->custom_url_slug) || empty($this->invitation_token)) {
            return null;
        }

        return route('rsvp.show', [
            'slug'  => $event->custom_url_slug,
            'token' => $this->invitation_token,
        ]);
    }

}
