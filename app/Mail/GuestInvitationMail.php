<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;

    public function __construct(Guest $guest)
    {
        $this->guest = $guest;
    }

    public function build()
    {
        $event = $this->guest->event;

        return $this->subject("Invitación a la boda de {$event->title}")
            ->view('emails.guests.invitation')
            ->with([
                'guest' => $this->guest,
                'event' => $event,
                'url'   => $this->guest->invitation_url,
            ]);
    }
}
