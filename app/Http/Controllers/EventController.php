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

    // Paso 1: pantalla con tipos de evento
    public function chooseType()
    {
        return view('events.choose-type');
    }

    // Paso 2: formulario según el tipo elegido
    public function create(string $type)
    {
        $validTypes = ['wedding', 'birthday', 'xv', 'baby_shower', 'corporate', 'other'];

        if (! in_array($type, $validTypes)) {
            abort(404);
        }

        return view('events.create', [
            'eventType' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $validTypes = ['wedding', 'birthday', 'xv', 'baby_shower', 'corporate', 'other'];

        $data = $request->validate([
            'event_type'   => ['required', Rule::in($validTypes)],
            'event_title'  => ['nullable', 'string', 'max:255'],

            'groom_name'   => ['required_if:event_type,wedding', 'nullable', 'string', 'max:255'],
            'bride_name'   => ['required_if:event_type,wedding', 'nullable', 'string', 'max:255'],

            'host_names'   => ['required_unless:event_type,wedding', 'nullable', 'string', 'max:255'],

            'custom_url_slug' => ['required', 'string', 'max:255', 'unique:events,custom_url_slug'],

            // La usamos como “fecha del evento” en general
            'wedding_date' => ['required', 'date'],

            'ceremony_time'          => ['required'],
            'ceremony_venue_name'    => ['required', 'string'],
            'ceremony_venue_address' => ['required', 'string'],
            'ceremony_maps_link'     => ['nullable', 'string'],

            'reception_time'          => ['required'],
            'reception_venue_name'    => ['required', 'string'],
            'reception_venue_address' => ['required', 'string'],
            'reception_maps_link'     => ['nullable', 'string'],
        ]);

        $data['user_id'] = auth()->id();

        $event = Event::create($data);

        return redirect()
            ->route('evento.edit', $event)
            ->with('success', 'Evento creado correctamente.');
    }

    public function edit(Event $event)
    {
        $user = auth()->user();
        $events = $user->events()->orderBy('wedding_date', 'asc')->get();

        // ¡Seguridad!
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // 1. Obtiene todas las plantillas activas de la base de datos
        $templates = Template::where('is_active', true)->get();

        // 2. Pasa el evento Y las plantillas a la vista
        return view('events.edit', [
            'event' => $event,
            'events'    => $events,
            'templates' => $templates
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

            'event_type'   => ['required', Rule::in(['wedding','birthday','xv','baby_shower','corporate','other'])],
            'event_title'  => ['nullable','string','max:255'],
            'host_names'   => ['required_unless:event_type,wedding','nullable','string','max:255'],

            // Info Básica
            'groom_name'   => ['required_if:event_type,wedding','nullable','string','max:255'],
            'bride_name'   => ['required_if:event_type,wedding','nullable','string','max:255'],
            'custom_url_slug' => ['required', 'string', 'alpha_dash', Rule::unique('events')->ignore($event->id)],
            'wedding_date' => ['required','date'],

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


    public function index()
    {
        $events = auth()->user()
            ->events()
            ->orderBy('wedding_date', 'asc')
            ->get();

        return view('events.index', compact('events'));
    }
}