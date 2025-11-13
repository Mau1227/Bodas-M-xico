<?php

namespace App\Http\Controllers;

use App\Models\Event; // <-- Importa el modelo
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Importa Auth
use Illuminate\Support\Str; // <-- Importa Str para crear el 'slug'
use Illuminate\Validation\Rule;

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
        // ¡Seguridad!
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // --- ESTA ES LA PARTE IMPORTANTE ---
        // 1. Obtiene todas las plantillas activas de la base de datos
        $templates = Template::where('is_active', true)->get();

        // 2. Pasa el evento Y las plantillas a la vista
        return view('events.edit', [
            'event' => $event,
            'templates' => $templates // <-- ¡ASEGÚRATE DE QUE ESTA LÍNEA EXISTA!
        ]);
    }

public function update(Request $request, Event $event)
    {
        // ¡Seguridad!
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // 1. Mensajes de error en español
        $messages = [
            'required' => 'Este campo es obligatorio.',
            'unique' => 'Esta URL ya está en uso. Prueba con otra.',
            'cover_photo.image' => 'El archivo debe ser una imagen.',
            'cover_photo.max' => 'La imagen no puede pesar más de 5MB.',
            'url' => 'Debe ser una URL válida (ej. https://...)',
        ];

        // 2. Validación (CON LOS CAMPOS NUEVOS AÑADIDOS)
        $data = $request->validate([
            // Info Básica
            'groom_name' => 'required|string|max:255',
            'bride_name' => 'required|string|max:255',
            'custom_url_slug' => ['required', 'string', 'alpha_dash', Rule::unique('events')->ignore($event->id)],
            'wedding_date' => 'required|date',

            // Ceremonia
            'ceremony_time' => 'required',
            'ceremony_venue_name' => 'required|string',
            'ceremony_venue_address' => 'required|string',
            'ceremony_maps_link' => 'nullable|url',
            
            // Recepción
            'reception_time' => 'required',
            'reception_venue_name' => 'required|string',
            'reception_venue_address' => 'required|string',
            'reception_maps_link' => 'nullable|url',

            // Diseño (Paso 3)
            'template_id' => 'required|integer|exists:templates,id',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'cover_photo' => 'nullable|image|max:5120', // max 5MB

            // Contenido de Invitación (Campos Faltantes)
            'welcome_message' => 'nullable|string',
            'dress_code' => 'nullable|string',
            'bride_parents' => 'nullable|string',
            'groom_parents' => 'nullable|string',
            'bride_story' => 'nullable|string',
            'groom_story' => 'nullable|string',
            'music_url' => 'nullable|string|max:255',
            'hashtag' => 'nullable|string|max:100',

        ], $messages);

        
        // 3. Lógica para subir la foto de portada
        if ($request->hasFile('cover_photo')) {
            $path = $request->file('cover_photo')->store('covers', 'public');
            $data['cover_photo_url'] = $path;
            // (Opcional: Borrar la foto antigua si existe)
        }

        // 4. Actualiza el evento en la base de datos
        $event->update($data);

        // 5. Redirige al dashboard con un mensaje
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