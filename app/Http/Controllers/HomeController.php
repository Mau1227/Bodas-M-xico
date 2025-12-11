<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Carbon\Carbon; // 🔥 Importante: Necesario para las fechas

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userId = Auth::id();

        // Obtenemos los eventos del usuario
        $events = Event::where('user_id', $userId)->get();
        $event = $events->first(); // Tomamos el primer evento activo

        // Inicializamos variables en 0 por si no hay evento
        $totalInvitados = $confirmados = $pendientes = $noAsisten = $totalPersonas = 0;
        $adultos = $ninos = $alergicos = 0;
        $daysLeft = 0;
        $guestMessages = collect(); // Colección vacía por defecto

        if ($event) {
            $guests = $event->guests; // Obtenemos todos los invitados

            // --- 1. Estadísticas Básicas ---
            $totalInvitados = $guests->count();
            $confirmados    = $guests->where('status', 'confirmed')->count();
            $pendientes     = $guests->where('status', 'pending')->count();
            $noAsisten      = $guests->where('status', 'declined')->count();

            // Total de cabezas (Invitado principal + Acompañantes) solo de confirmados
            $totalPersonas = $guests->where('status', 'confirmed')->sum(function ($g) {
                return 1 + (int) $g->confirmed_companions;
            });

            // --- 2. Lógica Extra para el Dashboard ---
            
            // Días restantes para la boda
            if ($event->wedding_date) {
                // 'false' permite números negativos si la fecha ya pasó
                $daysLeft = (int) now()->diffInDays(Carbon::parse($event->wedding_date), false);
                // Si quieres que el día del evento cuente como 1, puedes redondear hacia arriba o ajustar
                if ($daysLeft < 0) $daysLeft = 0; // Opcional: para que no salga negativo
            }

            // Filtrar solo los confirmados para las siguientes métricas
            $confirmedGuests = $guests->where('status', 'confirmed');

            // Conteo de Niños vs Adultos 
            // (Nota: Esto asume que tienes una columna 'is_child' en la BD)
            $ninos = $confirmedGuests->where('is_child', true)->count();
            
            // Para adultos, restamos los niños al total de personas confirmadas
            // (Asumimos que los acompañantes suelen ser adultos, o ajusta según tu lógica)
            $adultos = $totalPersonas - $ninos; 

            // Conteo de Alergias / Restricciones
            // (Nota: Asume columna 'dietary_requirements')
            $alergicos = $confirmedGuests->filter(function ($g) {
                return !empty($g->dietary_requirements);
            })->count();

            // --- 3. Mensajes de invitados ---
            $guestMessages = $event->guests()
                ->whereNotNull('message_to_couple')
                ->where('message_to_couple', '!=', '')
                ->orderByDesc('updated_at')
                ->take(5) // 🔥 Limitamos a los últimos 5 para no saturar
                ->get(['id', 'full_name', 'status', 'message_to_couple', 'updated_at']);
        }

        return view('home', [
            'events'         => $events,
            'event'          => $event,
            'totalInvitados' => $totalInvitados,
            'confirmados'    => $confirmados,
            'pendientes'     => $pendientes,
            'noAsisten'      => $noAsisten,
            'totalPersonas'  => $totalPersonas,
            'guestMessages'  => $guestMessages,
            // Variables Nuevas
            'daysLeft'       => $daysLeft,
            'adultos'        => $adultos,
            'ninos'          => $ninos,
            'alergicos'      => $alergicos,
        ]);
    }
}