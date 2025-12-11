@extends('layouts.app')

@section('content')

    {{-- Selector de evento --}}
    @if(isset($events) && $events->count() > 1)
        <div class="mb-4 flex items-center gap-3">
            <label for="event_switcher" class="text-sm text-gray-600">
                Estás editando:
            </label>
            <select id="event_switcher"
                    class="form-control max-w-xs"
                    onchange="if (this.value) window.location.href = this.value;">
                @foreach($events as $ev)
                    <option value="{{ route('evento.edit', $ev) }}"
                            @selected($ev->id === $event->id)>
                        {{ $ev->display_title }}
                        @if($ev->wedding_date)
                            ({{ \Illuminate\Support\Carbon::parse($ev->wedding_date)->format('d/m/Y') }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    @endif


    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Editar Evento: {{ $event->groom_name }} & {{ $event->bride_name }}
    </h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong>¡Ups!</strong> Hubo algunos problemas con tus datos.
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif


    {{-- Título del evento --}}
    <div class="mb-4">
        <label class="form-label">Título del evento</label>
        <input type="text"
            name="event_title"
            class="form-control"
            value="{{ old('event_title', $event->event_title ?? $event->display_title) }}"
            placeholder="Ej. Boda de Mauro y Andrea, Cumpleaños de Sofía...">
    </div>


    <form action="{{ route('evento.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tipo de evento --}}
        <input type="hidden" name="event_type" value="{{ $event->event_type }}">

        <div class="mb-4">
            <label class="form-label">Tipo de evento</label>
            <select class="form-control" disabled>
                <option value="wedding"  @selected($event->event_type == 'wedding')>Boda</option>
                <option value="birthday" @selected($event->event_type == 'birthday')>Cumpleaños</option>
                <option value="xv"       @selected($event->event_type == 'xv')>XV Años</option>
                <option value="baby_shower" @selected($event->event_type == 'baby_shower')>Baby Shower</option>
                <option value="corporate"   @selected($event->event_type == 'corporate')>Evento Corporativo</option>
                <option value="other"       @selected($event->event_type == 'other')>Otro</option>
            </select>
        </div>

        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-md border border-gray-100 space-y-8">

            <fieldset>
                <legend class="text-xl font-semibold text-gray-900">Información del Evento</legend>

                {{-- Si es boda, mostramos Novio/Novia --}}
                @if($event->is_wedding)
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label">Nombre del Novio</label>
                            <input type="text" name="groom_name" class="form-control"
                                value="{{ old('groom_name', $event->groom_name) }}">
                        </div>
                        <div>
                            <label class="form-label">Nombre de la Novia</label>
                            <input type="text" name="bride_name" class="form-control"
                                value="{{ old('bride_name', $event->bride_name) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Fecha de la boda</label>
                        <input type="date" name="wedding_date" class="form-control"
                            value="{{ old('wedding_date', $event->wedding_date?->format('Y-m-d')) }}">
                    </div>
                @else
                    {{-- Para otros eventos, solo "Anfitrión(es)" --}}
                    <div class="mb-4">
                        <label class="form-label">Anfitrión(es)</label>
                        <input type="text" name="host_names" class="form-control"
                            value="{{ old('host_names', $event->host_names) }}"
                            placeholder="Ej. Sofía López, Familia Ceballos, Empresa XYZ...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Fecha del evento</label>
                        <input type="date" name="wedding_date" class="form-control"
                            value="{{ old('wedding_date', \Illuminate\Support\Carbon::parse($event->wedding_date)->format('Y-m-d')) }}">
                        {{-- Usamos wedding_date como fecha genérica por ahora --}}
                    </div>
                @endif


                <div class="mt-6">
                    <label for="custom_url_slug" class="block text-sm font-medium text-gray-700">URL Personalizada</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                            festlink.events/e/
                        </span>
                        <input type="text" name="custom_url_slug" id="custom_url_slug" value="{{ old('custom_url_slug', $event->custom_url_slug) }}" required
                               placeholder="ej: juanylupe"
                               class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('custom_url_slug') ring-2 ring-red-500 @enderror">
                    </div>
                    @error('custom_url_slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="mt-2 text-sm text-gray-500">Solo letras minúsculas, números y guiones (ej. 'boda-ana-y-carlos').</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="wedding_date" class="block text-sm font-medium text-gray-700">Fecha de la Boda</label>
                    <input type="date" name="wedding_date" id="wedding_date" value="{{ old('wedding_date', $event->wedding_date) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('wedding_date') ring-2 ring-red-500 @enderror">
                    @error('wedding_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-medium text-gray-900">Ceremonia</legend>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="ceremony_time" class="block text-sm font-medium text-gray-700">Hora</label>
                        <input type="time" name="ceremony_time" id="ceremony_time" value="{{ old('ceremony_time', $event->ceremony_time) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_time') ring-2 ring-red-500 @enderror">
                        @error('ceremony_time') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ceremony_venue_name" class="block text-sm font-medium text-gray-700">Lugar (Nombre)</label>
                        <input type="text" name="ceremony_venue_name" id="ceremony_venue_name" value="{{ old('ceremony_venue_name', $event->ceremony_venue_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_venue_name') ring-2 ring-red-500 @enderror">
                        @error('ceremony_venue_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ceremony_venue_address" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="ceremony_venue_address" id="ceremony_venue_address" value="{{ old('ceremony_venue_address', $event->ceremony_venue_address) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_venue_address') ring-2 ring-red-500 @enderror">
                        @error('ceremony_venue_address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ceremony_maps_link" class="block text-sm font-medium text-gray-700">Link de Google Maps</label>
                        <input type="url" name="ceremony_maps_link" id="ceremony_maps_link" value="{{ old('ceremony_maps_link', $event->ceremony_maps_link) }}"
                               placeholder="https://goo.gl/maps/..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_maps_link') ring-2 ring-red-500 @enderror">
                        @error('ceremony_maps_link') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-lg font-medium text-gray-900">Recepción</legend>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="reception_time" class="block text-sm font-medium text-gray-700">Hora</label>
                        <input type="time" name="reception_time" id="reception_time" value="{{ old('reception_time', $event->reception_time) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_time') ring-2 ring-red-500 @enderror">
                        @error('reception_time') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="reception_venue_name" class="block text-sm font-medium text-gray-700">Lugar (Nombre)</label>
                        <input type="text" name="reception_venue_name" id="reception_venue_name" value="{{ old('reception_venue_name', $event->reception_venue_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_venue_name') ring-2 ring-red-500 @enderror">
                        @error('reception_venue_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="reception_venue_address" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="reception_venue_address" id="reception_venue_address" value="{{ old('reception_venue_address', $event->reception_venue_address) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_venue_address') ring-2 ring-red-500 @enderror">
                        @error('reception_venue_address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="reception_maps_link" class="block text-sm font-medium text-gray-700">Link de Google Maps</label>
                        <input type="url" name="reception_maps_link" id="reception_maps_link" value="{{ old('reception_maps_link', $event->reception_maps_link) }}"
                               placeholder="https://goo.gl/maps/..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_maps_link') ring-2 ring-red-500 @enderror">
                        @error('reception_maps_link') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-xl font-semibold text-gray-900">Diseño y Personalización</legend>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Selecciona una Plantilla</label>
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                        @forelse ($templates as $template)
                            <div class="h-full">
                            {{-- IMPORTANTE: quitar sr-only y usar hidden --}}
                            <input
                                type="radio"
                                name="template_id"
                                id="template-{{ $template->id }}"
                                value="{{ $template->id }}"
                                class="hidden peer"
                                {{ old('template_id', $event->template_id) == $template->id ? 'checked' : '' }}
                            />

                            <label
                                for="template-{{ $template->id }}"
                                class="relative border rounded-lg cursor-pointer flex flex-col h-full
                                    border-gray-300 peer-checked:border-purple-600
                                    peer-checked:ring-2 peer-checked:ring-purple-500 focus:outline-none"
                            >
                                <div class="h-32 bg-gray-100 rounded-t-lg flex items-center justify-center">
                                <span class="text-gray-400 text-xs">Vista Previa</span>
                                </div>
                                <div class="p-2 text-center flex-grow flex flex-col justify-center">
                                <span class="text-sm font-medium text-gray-900">{{ $template->name }}</span>
                                @if($template->is_premium)
                                    <span class="block text-xs text-purple-600 font-bold">PREMIUM</span>
                                @else
                                    <span class="block text-xs text-gray-500">GRATIS</span>
                                @endif
                                </div>
                            </label>
                            </div>
                        @empty
                            <p class="text-sm text-red-600 col-span-full">No se encontraron plantillas. (Verifica la base de datos).</p>
                        @endforelse
                        </div>

                    @error('template_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Color Principal (Títulos, Botones)</label>
                        <input type="color" name="primary_color" id="primary_color" 
                               value="{{ old('primary_color', $event->primary_color) }}"
                               class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                        @error('primary_color') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700">Color Secundario (Detalles, Bordes)</label>
                        <input type="color" name="secondary_color" id="secondary_color" 
                               value="{{ old('secondary_color', $event->secondary_color) }}"
                               class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                        @error('secondary_color') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="cover_photo" class="block text-sm font-medium text-gray-700">Foto de Portada</label>
                    <input type="file" name="cover_photo" id="cover_photo"
                           class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-sm text-gray-500">Sube la foto principal de la pareja. (JPG, PNG. Max 5MB)</p>
                    @error('cover_photo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                    @if ($event->cover_photo_url)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">Portada Actual:</p>
                            <img src="{{ asset('storage/' . $event->cover_photo_url) }}" alt="Portada" class="w-48 h-auto rounded-md shadow-sm">
                        </div>
                    @endif
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6">
                <legend class="text-xl font-semibold text-gray-900">Contenido de la Invitación</legend>
                
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="welcome_message" class="block text-sm font-medium text-gray-700">Mensaje de Bienvenida</label>
                        <textarea name="welcome_message" id="welcome_message" rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('welcome_message', $event->welcome_message) }}</textarea>
                    </div>
                    <div>
                        <label for="dress_code" class="block text-sm font-medium text-gray-700">Código de Vestimenta</label>
                        <select name="dress_code" id="dress_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="Formal" @selected(old('dress_code', $event->dress_code) == 'Formal')>Formal</option>
                            <option value="Etiqueta" @selected(old('dress_code', $event->dress_code) == 'Etiqueta')>Etiqueta</option>
                            <option value="Semi-Formal" @selected(old('dress_code', $event->dress_code) == 'Semi-Formal')>Semi-Formal</option>
                            <option value="Casual Elegante" @selected(old('dress_code', $event->dress_code) == 'Casual Elegante')>Casual Elegante</option>
                            <option value="Playa" @selected(old('dress_code', $event->dress_code) == 'Playa')>Playa</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bride_parents" class="block text-sm font-medium text-gray-700">Padres de la Novia (uno por línea)</label>
                        <textarea name="bride_parents" id="bride_parents" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('bride_parents', $event->bride_parents) }}</textarea>
                    </div>
                    <div>
                        <label for="groom_parents" class="block text-sm font-medium text-gray-700">Padres del Novio (uno por línea)</label>
                        <textarea name="groom_parents" id="groom_parents" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('groom_parents', $event->groom_parents) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bride_story" class="block text-sm font-medium text-gray-700">Frase o Historia de la Novia</label>
                        <textarea name="bride_story" id="bride_story" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('bride_story', $event->bride_story) }}</textarea>
                    </div>
                    <div>
                        <label for="groom_story" class="block text-sm font-medium text-gray-700">Frase o Historia del Novio</label>
                        <textarea name="groom_story" id="groom_story" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('groom_story', $event->groom_story) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="music_url" class="block text-sm font-medium text-gray-700">Música de Fondo (ID de Video de YouTube)</label>
                        <input type="text" name="music_url" id="music_url" value="{{ old('music_url', $event->music_url) }}"
                               placeholder="dQw4w9WgXcQ"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="hashtag" class="block text-sm font-medium text-gray-700">Hashtag del Evento</label>
                        <input type="text" name="hashtag" id="hashtag" value="{{ old('hashtag', $event->hashtag) }}"
                               placeholder="#BodaJuanYLupe"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
            </fieldset>

            <div class="pt-6 text-right border-t border-gray-200">
                <a href="{{ route('home') }}" class="mr-3 rounded-md border border-gray-300 bg-white py-2 px-6 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Cancelar
                </a>
                <a href="{{ route('invitation.show', $event->custom_url_slug) }}" 
                   target="_blank" 
                   class="mr-3 rounded-md border border-purple-300 bg-purple-50 py-2 px-6 text-sm font-medium text-purple-700 shadow-sm hover:bg-purple-100">
                    Previsualizar
                </a>
                <button type="submit"
                        class="rounded-full gradient-primary px-6 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-lg transition transform hover:scale-105">
                    Guardar Cambios Principales
                </button>
            </div>
        </div>
    </form>
    <div class="mt-8 bg-white p-6 md:p-8 rounded-2xl shadow-md border border-gray-100">
        <fieldset>
            <legend class="text-xl font-semibold text-gray-900">Galería de Fotos</legend>
            
            <form action="{{ route('photo.store', $event) }}" method="POST" enctype="multipart/form-data" class="mt-6 p-4 bg-gray-50 rounded-md border">
                @csrf
                <label for="gallery_photos" class="block text-sm font-medium text-gray-700">Añadir nuevas fotos a la galería</label>
                <input type="file" name="gallery_photos[]" id="gallery_photos" multiple
                       class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                <p class="mt-1 text-sm text-gray-500">Puedes seleccionar varias imágenes a la vez.</p>
                <button type="submit" class="mt-2 gradient-primary text-white px-4 py-2 text-sm rounded-full font-semibold transition">
                    Subir Fotos
                </button>
            </form>

            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-800 mb-2">Fotos Actuales</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse ($event->eventPhotos as $photo)
                        <div class="relative group">
                            <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto de galería" class="h-32 w-full object-cover rounded-md shadow-sm">
                            <form action="{{ route('photo.destroy', $photo) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                        onclick="return confirm('¿Seguro que quieres eliminar esta foto?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 col-span-full">No hay fotos en la galería.</p>
                    @endforelse
                </div>
            </div>
        </fieldset>
    </div>
    <div class="mt-8 bg-white p-6 md:p-8 rounded-2xl shadow-md border border-gray-100">
        <fieldset>
            <legend class="text-xl font-semibold text-gray-900">Itinerario</legend>

            <form action="{{ route('itinerary.store', $event) }}" method="POST" class="mt-6 p-4 bg-gray-50 rounded-md border flex items-end gap-4">
                @csrf
                <div class="flex-grow">
                    <label for="itinerary_time" class="block text-sm font-medium text-gray-700">Hora</label>
                    <input type="time" name="time" id="itinerary_time" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div class="flex-grow-[3]">
                    <label for="itinerary_activity" class="block text-sm font-medium text-gray-700">Actividad</label>
                    <input type="text" name="activity" id="itinerary_activity" placeholder="Ej. Cena" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <button type="submit" class="gradient-primary text-white px-4 py-2 text-sm rounded-full font-semibold transition h-10">
                    Añadir
                </button>
            </form>

            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-800 mb-2">Itinerario Actual</h4>
                <ul class="divide-y divide-gray-200 border rounded-md">
                    @forelse ($event->itineraryItems->sortBy('time') as $item)
                        <li class="p-3 flex justify-between items-center">
                            <div>
                                <span class="text-sm font-bold text-purple-700">{{ date('h:i A', strtotime($item->time)) }}</span>
                                <span class="ml-4 text-sm text-gray-800">{{ $item->activity }}</span>
                            </div>
                            
                            <form action="{{ route('itinerary.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-500 hover:text-red-700"
                                        onclick="return confirm('¿Seguro que quieres eliminar este item?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="p-3 text-center text-sm text-gray-500">No hay items en el itinerario.</li>
                    @endforelse
                </ul>
            </div>
        </fieldset>
    </div>
    @endsection