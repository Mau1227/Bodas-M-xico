<?php

namespace App\Http\Controllers;

use App\Models\Event;       // <-- Importa Event
use App\Models\Itinerary;   // <-- Importa Itinerary
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Importa Auth

class ItineraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Guarda un nuevo item del itinerario.
     */
    public function store(Request $request, Event $event)
    {
        // 1. Seguridad
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Validación
        $data = $request->validate([
            'time' => 'required',
            'activity' => 'required|string|max:255',
        ]);

        // 3. Creación
        $event->itineraryItems()->create($data);

        // 4. Redirección
        return back()->with('success', 'Item del itinerario añadido.');
    }

    /**
     * Elimina un item del itinerario.
     */
    public function destroy(Itinerary $itinerary)
    {
        // 1. Seguridad
        if ($itinerary->event->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Eliminación
        $itinerary->delete();

        // 3. Redirección
        return back()->with('success', 'Item del itinerario eliminado.');
    }
}