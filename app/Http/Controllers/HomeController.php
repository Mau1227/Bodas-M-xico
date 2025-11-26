<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importa el helper de Autenticación
use App\Models\Event; // Importa tu modelo de Evento

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // Protege este controlador
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // 1. Obtiene el ID del usuario que está logueado
        $userId = Auth::id();

        // 2. Busca en la base de datos TODOS los eventos que
        //    pertenezcan a este usuario (usando la relación que creamos)
        $events = Event::where('user_id', $userId)->get();
        $event = $events->first();

        $totalInvitados = $confirmados = $pendientes = $noAsisten = $totalPersonas = 0;
        if ($event) {
            $guests = $event->guests;   // relación Event -> guests

            $totalInvitados = $guests->count();
            $confirmados    = $guests->where('status', 'confirmed')->count();
            $pendientes     = $guests->where('status', 'pending')->count();
            $noAsisten      = $guests->where('status', 'declined')->count();

            // invitado + acompañantes confirmados
            $totalPersonas  = $guests->sum(function ($g) {
                return 1 + (int) $g->confirmed_companions;
            });
        }

        $guestMessages = $event->guests()
        ->whereNotNull('message_to_couple')
        ->where('message_to_couple', '!=', '')
        ->orderByDesc('updated_at')
        ->get(['id', 'full_name', 'status', 'message_to_couple', 'updated_at']);

        // 3. Envía los eventos a la vista 'home'
        return view('home', [
            'events'         => $events,
            'event'          => $event,
            'totalInvitados' => $totalInvitados,
            'confirmados'    => $confirmados,
            'pendientes'     => $pendientes,
            'noAsisten'      => $noAsisten,
            'totalPersonas'  => $totalPersonas,
            'guestMessages'  => $guestMessages,
        ]);
    }
}