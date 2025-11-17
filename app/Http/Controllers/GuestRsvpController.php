<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestRsvpController extends Controller
{
    /**
     * Muestra la invitación para un invitado REAL (link con token).
     * GET /{slug}/i/{token}
     */
    public function show(string $slug, string $token)
    {
        // 1. Buscar el evento por slug
        $event = Event::where('custom_url_slug', $slug)->firstOrFail();

        // 2. Buscar el invitado por token y amarrado al evento
        $guest = Guest::where('invitation_token', $token)
            ->where('event_id', $event->id)
            ->firstOrFail();

        // 3. Saber si ya había respondido antes
        $alreadyConfirmed = $guest->status !== 'pending'
            || $guest->confirmed_companions > 0
            || !empty($guest->dietary_restrictions)
            || !empty($guest->message_to_couple);

        // 4. Total de pases (invitado + acompañantes)
        $totalPases = 1 + (int) $guest->max_companions;

        // 5. Vista según la plantilla elegida
        $view = $event->template->view_file;   // ej. 'templates.romantica-floral'

        return view($view, [
            'event'            => $event,
            'guest'            => $guest,
            'alreadyConfirmed' => $alreadyConfirmed,
            'isPreview'        => false,   // 👈 aquí ya NO es preview
            'totalPases'       => $totalPases,
        ]);
    }

    /**
     * Procesa el formulario de RSVP de un invitado.
     * POST /{slug}/i/{token}
     */
    public function submit(Request $request, string $slug, string $token)
    {
        $event = Event::where('custom_url_slug', $slug)->firstOrFail();

        $guest = Guest::where('invitation_token', $token)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $data = $request->validate([
            'status'                => 'required|in:confirmed,declined',
            'confirmed_companions'  => 'nullable|integer|min:0|max:' . $guest->max_companions,
            'dietary_restrictions'  => 'nullable|string|max:1000',
            'message_to_couple'     => 'nullable|string|max:1000',
        ]);

        // Si dice que no va, dejamos acompañantes en 0
        $guest->status               = $data['status'];
        $guest->confirmed_companions = $data['status'] === 'confirmed'
            ? ($data['confirmed_companions'] ?? 0)
            : 0;
        $guest->dietary_restrictions = $data['dietary_restrictions'] ?? null;
        $guest->message_to_couple    = $data['message_to_couple'] ?? null;
        $guest->confirmed_at         = now();
        $guest->save();

        $mensaje = $data['status'] === 'confirmed'
            ? '¡Gracias por confirmar! Te esperamos con mucha ilusión 💕'
            : 'Gracias por avisarnos. Lamentamos que no puedas asistir 🕊️';

        // Redirigimos otra vez a la misma invitación (misma plantilla)
        return redirect()
            ->route('rsvp.show', ['slug' => $slug, 'token' => $token])
            ->with('rsvp_status', $mensaje);
    }
}
