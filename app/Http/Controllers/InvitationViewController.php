<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class InvitationViewController extends Controller
{
    /**
     * Muestra la página de la invitación pública.
     */
    public function show($slug)
    {
        // 1. Busca el evento por su URL única. Si no lo encuentra, falla (error 404).
        $event = Event::where('custom_url_slug', $slug)// Opcional: solo muestra eventos publicados
                      ->firstOrFail();

        // 2. Obtiene el nombre del archivo de la plantilla (ej. 'plantillas.romantica-floral')
        //    (Asumimos que la columna 'view_file' en tu tabla 'templates' está llena)
        $templateView = $event->template->view_file; // <- 'plantillas.romantica-floral'

        // 3. Muestra esa vista y le pasa la variable $event
        return view($templateView, [
            'event' => $event
        ]);
    }
}