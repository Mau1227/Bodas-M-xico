{{-- resources/views/rsvp/show.blade.php --}}
@php
    // Garantizar que esta variable exista
    $isPreview = $isPreview ?? false;

    // Cálculo de pases (1 invitado + max companions)
    $totalPases = 1 + ($guest->max_companions ?? 0);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Invitación de {{ $event->bride_name }} & {{ $event->groom_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col items-center py-8 px-4">

        <div class="w-full max-w-2xl bg-white shadow-lg rounded-2xl overflow-hidden border border-slate-100">

            {{-- Encabezado / portada --}}
            <div class="relative h-40 bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white">
                <div class="text-center">
                    <p class="text-xs uppercase tracking-[0.2em] mb-1">Estás invitad@ a la boda de</p>
                    <h1 class="text-2xl font-semibold">
                        {{ $event->bride_name }} & {{ $event->groom_name }}
                    </h1>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Info del evento --}}
                <div class="text-center space-y-2">

                    {{-- Texto dinámico si es preview --}}
                    @if ($isPreview)
                        <p class="text-sm text-slate-500">
                            Hola <span class="font-semibold">Nombre del invitado</span> (vista previa)
                        </p>
                        <p class="text-sm text-slate-600">
                            Así verán tus invitados su invitación real 💌
                        </p>
                    @else
                        <p class="text-sm text-slate-500">
                            Hola <span class="font-semibold">{{ $guest->full_name }}</span>,
                        </p>
                        <p class="text-sm text-slate-600">
                            Nos encantaría que nos acompañes en este día tan especial.
                        </p>
                    @endif

                    <div class="mt-4 inline-flex flex-col items-center justify-center px-4 py-3 bg-slate-50 rounded-xl text-sm text-slate-700">
                        <p class="font-semibold">
                            {{ \Illuminate\Support\Carbon::parse($event->wedding_date)->translatedFormat('d \\de F \\de Y') }}
                        </p>
                        <p>
                            Ceremonia: {{ $event->ceremony_time }} · {{ $event->ceremony_venue_name }}
                        </p>
                        <p>
                            Recepción: {{ $event->reception_time }} · {{ $event->reception_venue_name }}
                        </p>
                    </div>
                </div>


                {{-- NOTIFICACIONES (solo para invitados reales) --}}
                @if (!$isPreview)

                    @if (session('rsvp_status'))
                        <div class="p-3 rounded-md bg-emerald-50 text-emerald-700 text-sm">
                            {{ session('rsvp_status') }}
                        </div>
                    @endif

                    @if ($alreadyConfirmed && !session('rsvp_status'))
                        <div class="p-3 rounded-md bg-blue-50 text-blue-700 text-sm">
                            Ya habías registrado tu respuesta. Puedes actualizarla.
                        </div>
                    @endif

                @endif


                {{-- ===================================================== --}}
                {{-- ===============   FORMULARIO RSVP   ================== --}}
                {{-- ===================================================== --}}

                @if (!$isPreview)
                    {{-- FORMULARIO REAL (funciona y envía datos) --}}
                    <form method="POST"
                          action="{{ route('rsvp.submit', ['slug' => $event->custom_url_slug, 'token' => $guest->invitation_token]) }}"
                          class="space-y-4">

                        @csrf

                        {{-- Asistencia --}}
                        <div>
                            <p class="text-sm font-medium text-slate-800 mb-1">
                                ¿Podrás asistir?
                            </p>

                            <div class="space-y-1 text-sm">
                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="status" value="confirmed"
                                           @checked(old('status', $guest->status) === 'confirmed')
                                           class="rounded border-slate-300">
                                    <span>Sí, con gusto asistiré 🎉</span>
                                </label><br>

                                <label class="inline-flex items-center gap-2">
                                    <input type="radio" name="status" value="declined"
                                           @checked(old('status', $guest->status) === 'declined')
                                           class="rounded border-slate-300">
                                    <span>No podré asistir 😢</span>
                                </label>
                            </div>

                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Acompañantes --}}
                        @if ($guest->max_companions > 0)
                            <div>
                                <label class="block text-sm font-medium text-slate-800 mb-1">
                                    ¿Cuántas personas te acompañan?
                                </label>
                                <p class="text-xs text-slate-500 mb-1">
                                    Puedes traer hasta {{ $guest->max_companions }} acompañante(s).
                                </p>

                                <input type="number"
                                       name="confirmed_companions"
                                       min="0"
                                       max="{{ $guest->max_companions }}"
                                       value="{{ old('confirmed_companions', $guest->confirmed_companions) }}"
                                       class="w-24 border-slate-300 rounded-md text-sm">

                                @error('confirmed_companions')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif


                        {{-- Restricciones alimentarias --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-800 mb-1">
                                ¿Tienes alguna restricción alimentaria?
                            </label>
                            <textarea name="dietary_restrictions"
                                      rows="2"
                                      class="w-full border-slate-300 rounded-md text-sm"
                                      placeholder="Ejemplo: vegetariano, vegano, sin gluten, alergia a mariscos, etc.">{{ old('dietary_restrictions', $guest->dietary_restrictions) }}</textarea>

                            @error('dietary_restrictions')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- Mensaje para los novios --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-800 mb-1">
                                Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}
                            </label>
                            <textarea name="message_to_couple"
                                      rows="3"
                                      class="w-full border-slate-300 rounded-md text-sm"
                                      placeholder="Déjales un mensaje bonito 💌">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>

                            @error('message_to_couple')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2 flex justify-center">
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700">
                                Enviar respuesta
                            </button>
                        </div>
                    </form>

                @else
                    {{-- FORMULARIO DEMO DESHABILITADO (vista previa) --}}
                    <form class="space-y-4 opacity-70 pointer-events-none">

                        <div>
                            <p class="text-sm font-medium text-slate-800 mb-1">
                                ¿Podrás asistir?
                            </p>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" disabled>
                                <span>Sí, con gusto asistiré 🎉</span>
                            </label><br>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" disabled>
                                <span>No podré asistir 😢</span>
                            </label>
                        </div>

                        <div>
                            <label class="text-sm font-medium mb-1 block">¿Cuántas personas te acompañan?</label>
                            <input type="number" disabled value="1" class="w-24 border-slate-300 rounded-md text-sm">
                        </div>

                        <textarea disabled rows="2" class="w-full border-slate-300 rounded-md text-sm">
Ejemplo de restricciones: sin gluten, vegano...
                        </textarea>

                        <textarea disabled rows="3" class="w-full border-slate-300 rounded-md text-sm">
Mensaje ejemplo para los novios 💌
                        </textarea>

                        <div class="pt-2 flex justify-center">
                            <button type="button"
                                    class="inline-flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-semibold text-white bg-purple-400 cursor-default">
                                Enviar respuesta (vista previa)
                            </button>
                        </div>
                    </form>
                @endif


                {{-- MAPAS --}}
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 text-center space-y-1">
                    @if ($event->ceremony_maps_link)
                        <p>
                            📍 Cómo llegar a la ceremonia:
                            <a href="{{ $event->ceremony_maps_link }}" target="_blank" class="text-purple-600 underline">
                                Ver en mapas
                            </a>
                        </p>
                    @endif

                    @if ($event->reception_maps_link)
                        <p>
                            🎉 Cómo llegar a la recepción:
                            <a href="{{ $event->reception_maps_link }}" target="_blank" class="text-purple-600 underline">
                                Ver en mapas
                            </a>
                        </p>
                    @endif
                </div>

            </div>
        </div>

        <p class="mt-4 text-xs text-slate-400">
            FestLink · Invitaciones digitales
        </p>
    </div>
</body>
</html>
