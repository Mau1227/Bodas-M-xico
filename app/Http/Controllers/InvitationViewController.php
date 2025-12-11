<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Template;

class InvitationViewController extends Controller
{
    /**
     * Muestra la página de la invitación pública.
     */
    // app/Http/Controllers/EventPreviewController.php

    public function show(string $slug)
    {
        $event = Event::where('custom_url_slug', $slug)->firstOrFail();
        // Invitado “falso” para la vista previa
        $dummyGuest = new Guest([
            'full_name'       => 'Nombre del invitado',
            'email'           => 'invitado@ejemplo.com',
            'phone'           => '999 999 9999',
            'max_companions'  => 1,      // → 2 pases en total
            'status'          => 'pending',
            'dietary_restrictions' => null,
            'message_to_couple'    => null,
        ]);

        $view = $event->template->view_file;
        $totalPases = 1 + $dummyGuest->max_companions;

        return view($view, [
            'event'            => $event,
            'guest'            => $dummyGuest,
            'alreadyConfirmed' => false,
            'isPreview'        => true,   // 👈 bandera importante
            'totalPases'       => $totalPases,
        ]);
    }

    public function previewTemplate(string $slug, Template $template)
    {
        $event = Event::where('custom_url_slug', $slug)->firstOrFail();

        // NO guardamos, solo modificamos en memoria
        $event->template_id = $template->id;
        $event->setRelation('template', $template);

        $dummyGuest = new Guest([
            'full_name'       => 'Nombre del invitado',
            'email'           => 'invitado@ejemplo.com',
            'phone'           => '999 999 9999',
            'max_companions'  => 1,
            'status'          => 'pending',
            'dietary_restrictions' => null,
            'message_to_couple'    => null,
        ]);

        $view = $template->view_file;
        $totalPases = 1 + $dummyGuest->max_companions;

        return view($view, [
            'event'            => $event,
            'guest'            => $dummyGuest,
            'alreadyConfirmed' => false,
            'isPreview'        => true,
            'totalPases'       => $totalPases,
        ]);
    }
    
}
