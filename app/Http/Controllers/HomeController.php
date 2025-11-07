<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importa el helper de Autenticación
use App\Models\Event; // Importa tu modelo de Evento

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
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

        // 3. Envía los eventos a la vista 'home'
        return view('home', [
            'events' => $events
        ]);
    }
}