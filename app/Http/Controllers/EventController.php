<?php

namespace App\Http\Controllers;

use App\Models\Event; // <-- Importa el modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Importa Auth
use Illuminate\Support\Str; // <-- Importa Str para crear el 'slug'

class EventController extends Controller
{
    /**
     * Asegura que solo usuarios autenticados accedan a este controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     * (Esta es la función para la ruta GET /evento/crear)
     */
    public function create()
    {
        // Simplemente retorna la vista que CREAREMOS en el siguiente paso
        return view('events.create');
    }

    /**
     * Guarda el nuevo evento en la base de datos.
     * (Esta es la función para la ruta POST /evento)
     */
    public function store(Request $request)
    {
        // 1. Definimos los mensajes de error en español
        $messages = [
            'required' => 'Este campo es obligatorio.',
            'string' => 'Este campo debe ser texto.',
            'date' => 'Por favor, introduce una fecha válida.',
            'url' => 'Por favor, introduce una URL válida (ej. https://...)',
            'unique' => 'Esta URL ya está en uso. Prueba con otra.',
            'alpha_dash' => 'La URL solo puede contener letras, números y guiones (-).',
        ];

        // 2. Validación
        // Guardamos el resultado de validate() directamente en $data.
        // Si la validación falla, Laravel redirige automáticamente.
        $data = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'custom_url_slug' => 'required|string|unique:events|alpha_dash',
            'wedding_date' => 'required|date',
            'ceremony_time' => 'required',
            'ceremony_venue_name' => 'required|string',
            'ceremony_venue_address' => 'required|string',
            'reception_time' => 'required',
            'reception_venue_name' => 'required|string',
            'reception_venue_address' => 'required|string',
            'ceremony_maps_link' => 'nullable|url',
            'reception_maps_link' => 'nullable|url',
            'welcome_message' => 'nullable|string',
            'dress_code' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ], $messages); // <-- ¡Aquí pasamos los mensajes!

        // 3. (¡YA NO NECESITAMOS LA LÍNEA QUE FALLABA!)
        // $data = $request->validated(); // <-- BORRA ESTA LÍNEA

        // 4. Añade el ID del usuario logueado
        $data['user_id'] = Auth::id();

        // 5. Crea el evento en la base de datos
        Event::create($data);

        // 6. Redirige al usuario de vuelta al dashboard con un mensaje
        return redirect()->route('home')->with('success', '¡Evento creado exitosamente!');
    }

    public function edit(Event $event)
    {
        // ¡Seguridad! Asegura que el usuario solo pueda editar SUS eventos
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        return view('events.edit', [
            'event' => $event // Pasa los datos del evento a la vista
        ]);
    }

    public function update(Request $request, Event $event)
    {
        // ¡Seguridad!
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // 1. Definimos los mensajes de error en español
        $messages = [
            'required' => 'Este campo es obligatorio.',
            'unique' => 'Esta URL ya está en uso. Prueba con otra.',
            // ... (puedes añadir más si quieres)
        ];

        // 2. Validación (casi igual a store, pero con una regla 'unique' especial)
        $data = $request->validate([
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'custom_url_slug' => [
                'required',
                'string',
                'alpha_dash',
                // Le decimos a Laravel que ignore el ID de este evento
                // al comprobar si la URL es única
                Rule::unique('events')->ignore($event->id),
            ],
            'wedding_date' => 'required|date',
            'ceremony_time' => 'required',
            'ceremony_venue_name' => 'required|string',
            'ceremony_venue_address' => 'required|string',
            'reception_time' => 'required',
            'reception_venue_name' => 'required|string',
            'reception_venue_address' => 'required|string',
            'ceremony_maps_link' => 'nullable|url',
            'reception_maps_link' => 'nullable|url',
            'welcome_message' => 'nullable|string',
            'dress_code' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ], $messages);

        // 3. Actualiza el evento en la base de datos
        $event->update($data);

        // 4. Redirige al dashboard con un mensaje
        return redirect()->route('home')->with('success', '¡Evento actualizado exitosamente!');
    }

    public function destroy(Event $event)
    {
        // ¡Seguridad!
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // 1. Elimina el evento
        $event->delete();

        // 2. Redirige al dashboard con un mensaje
        return redirect()->route('home')->with('success', '¡Evento eliminado exitosamente!');
    }
}