<?php

namespace App\Http\Controllers;

use App\Models\Event;        // <-- Importa Event
use App\Models\EventPhoto;    // <-- Importa EventPhoto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;       // <-- Importa Auth
use Illuminate\Support\Facades\Storage;  // <-- Importa Storage


class EventPhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Guarda nuevas fotos de galería.
     */
    public function store(Request $request, Event $event)
    {
        // 1. Seguridad: Verifica que el usuario sea el dueño del evento
        if ($event->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Valida los archivos
        $request->validate([
            'gallery_photos.*' => 'required|image|max:5120', // 5MB max por foto
        ]);

        // 3. Sube y guarda cada foto
        if ($request->hasFile('gallery_photos')) {
            foreach ($request->file('gallery_photos') as $file) {
                
                // Sube el archivo a 'storage/app/public/gallery'
                $path = $file->store('gallery', 'public');

                // Crea el registro en la base de datos
                $event->eventPhotos()->create([
                    'photo_url' => $path
                ]);
            }
        }

        // 4. Redirige de vuelta a la página de edición
        return back()->with('success', '¡Fotos de galería actualizadas!');
    }

    /**
     * Elimina una foto de la galería.
     */
    public function destroy(EventPhoto $photo)
    {
        // 1. Seguridad: Verifica que el usuario sea el dueño de la foto
        if ($photo->event->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. Borra el archivo del disco
        Storage::disk('public')->delete($photo->photo_url);

        // 3. Borra el registro de la base de datos
        $photo->delete();

        // 4. Redirige de vuelta
        return back()->with('success', 'Foto eliminada.');
    }
}