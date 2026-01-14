@php
    use Carbon\Carbon;

    // --- Normalizamos la fecha del evento solo a Y-m-d, ignorando cualquier hora rara ---
    $rawDate  = (string) $event->wedding_date; // '2026-02-14' o '2026-02-14 00:00:00'
    $onlyDate = substr($rawDate, 0, 10);       // '2026-02-14'

    try {
        $fecha_evento = Carbon::createFromFormat('Y-m-d', $onlyDate);
    } catch (\Exception $e) {
        $fecha_evento = Carbon::today();
    }

    // --- Fecha+hora completa para JS (contador + .ics) ---
    if (!empty($event->ceremony_time)) {
        $fecha_evento_datetime = Carbon::parse($onlyDate.' '.$event->ceremony_time);
    } elseif (!empty($event->reception_time)) {
        $fecha_evento_datetime = Carbon::parse($onlyDate.' '.$event->reception_time);
    } else {
        $fecha_evento_datetime = Carbon::parse($onlyDate.' 00:00:00');
    }
    $fechaEventoJs = $fecha_evento_datetime->timestamp * 1000; // milisegundos

    // Formato completo en español (para usar en textos)
    $fecha_completa = $fecha_evento->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY');
    $dia_numero     = $fecha_evento->format('d');

    // --- Hora del evento: preferimos ceremonia, si no recepción ---
    $hora_evento_formato = null;
    if (!empty($event->ceremony_time)) {
        try {
            $hora_evento_formato = Carbon::parse($event->ceremony_time)->format('H:i');
        } catch (\Exception $e) {
            $hora_evento_formato = $event->ceremony_time;
        }
    } elseif (!empty($event->reception_time)) {
        try {
            $hora_evento_formato = Carbon::parse($event->reception_time)->format('H:i');
        } catch (\Exception $e) {
            $hora_evento_formato = $event->reception_time;
        }
    }

    // --- Procesamos los campos de texto de "padres" (de TEXT a array) ---
    $padres_novia = $event->bride_parents ? explode("\n", trim($event->bride_parents)) : [];
    $padres_novio = $event->groom_parents ? explode("\n", trim($event->groom_parents)) : [];

    // --- Procesamos las frases (de TEXT a array) ---
    $frases_novia = $event->bride_story ? explode("\n", trim($event->bride_story)) : ['Contigo encontré mi lugar en el mundo.'];
    $frases_novio = $event->groom_story ? explode("\n", trim($event->groom_story)) : ['Eres mi presente y mi futuro.'];

    // --- Otros helpers usados en floral ---
    $isPreview   = $isPreview ?? false;
    $totalPases  = 1 + (int) ($guest->max_companions ?? 0);
    $hasResponded = in_array($guest->status, ['confirmed', 'declined'])
        || $guest->confirmed_companions > 0
        || !empty($guest->dietary_restrictions)
        || !empty($guest->message_to_couple);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Boda {{ $event->bride_name }} & {{ $event->groom_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@300;400&display=swap" rel="stylesheet">
<style>
/* === ELEGANCIA DORADA === */
:root { --gold: #D4AF37; --dark: #111; --cream: #faf9f6; }
body { font-family: 'Lato', sans-serif; background: var(--dark); color: var(--cream); margin: 0; overflow-x: hidden; }
body.modal-open { overflow: hidden; }
h1, h2, h3 { font-family: 'Cinzel', serif; color: var(--gold); text-transform: uppercase; letter-spacing: 2px; text-align: center; }
.container { max-width: 900px; margin: 0 auto; padding: 0 20px; }
.section { padding: 80px 0; }

/* Borde Decorativo */
.gold-border { border: 1px solid var(--gold); padding: 20px; margin: 20px auto; position: relative; max-width: 600px; z-index: 1; }
.gold-border::before { 
    content: ''; position: absolute; inset: 4px; border: 1px solid var(--gold); 
    z-index: -1;
    pointer-events: none;
}

/* Botones */
.btn { 
    background: linear-gradient(45deg, #BF953F, #FCF6BA, #AA771C); 
    color: black; border: none; padding: 15px 40px; 
    font-family: 'Cinzel', serif; font-weight: bold; cursor: pointer; 
    display: inline-block; margin-top: 20px; transition: 0.5s; 
    text-decoration: none; position: relative; z-index: 10;
}
.btn:hover { filter: brightness(1.2); transform: scale(1.05); }

/* Hero */
.hero { 
    height: 100vh; display: flex; align-items: center; justify-content: center; 
    background: url('{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600' }}') center/cover; 
    position: relative; 
}
.hero-overlay { 
    background: rgba(0,0,0,0.7); position: absolute; inset: 0; padding: 20px; 
    display: flex; align-items: center; justify-content: center; 
}
.hero-card { 
    border: 2px solid var(--gold); padding: 40px 20px; 
    background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); 
    max-width: 600px; width: 100%; 
}

/* Grid */
.grid-2 { display: grid; grid-template-columns: 1fr; gap: 0; }
@media(min-width: 768px) { .grid-2 { grid-template-columns: 1fr 1fr; } }

/* Countdown */
.countdown { display: flex; justify-content: center; gap: 20px; margin-top: 40px; flex-wrap: wrap; }
.countdown-item { text-align: center; }
.countdown span { font-family: 'Cinzel', serif; font-size: 2rem; color: var(--cream); display: block; }
.countdown small { display: block; color: var(--gold); font-size: 0.7rem; border-top: 1px solid var(--gold); margin-top: 5px; padding-top: 5px; }

/* YouTube & Modal */
#youtube-player { position: absolute; width: 0; height: 0; opacity: 0; pointer-events: none; }
.modal { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; transition: opacity 0.5s, visibility 0.5s; padding: 20px; }
.modal.hidden { opacity: 0; visibility: hidden; pointer-events: none; }

/* Galería Elegante */
.gallery-frame { display: flex; overflow-x: auto; gap: 20px; padding: 20px 0; scroll-snap-type: x mandatory; }
.gallery-frame img { border: 2px solid var(--gold); height: 350px; flex-shrink: 0; scroll-snap-align: center; max-width: 90vw; object-fit: cover; }

/* Padres */
.parents-box { border-bottom: 1px solid var(--gold); padding-bottom: 20px; margin-bottom: 20px; }
.parents-box ul { list-style: none; padding: 0; line-height: 2; }

/* Inputs RSVP */
.rsvp-input, .rsvp-select, .rsvp-textarea {
    width: 100%; padding: 10px; border: 1px solid var(--gold); 
    background: transparent; color: var(--dark); font-family: inherit;
}
.rsvp-textarea { resize: vertical; min-height: 80px; }
.error-text { color: #b91c1c; font-size: 0.8rem; margin-top: 4px; }
.success-box {
    background: rgba(191,149,63,0.1);
    border-left: 4px solid var(--gold);
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    color: var(--cream);
}
</style>
</head>
<body class="modal-open">

<div id="youtube-player"></div>
<div id="modalInicial" class="modal" role="dialog" aria-modal="true">
    <div class="gold-border" style="padding: 40px; background: var(--dark); width: 100%; text-align: center; max-width: 600px;">
        <h2 style="font-size: 1.8rem; margin-bottom: 20px;">Bienvenidos</h2>

        <p style="margin: 20px 0; font-size: 1.1rem; line-height: 1.6;">
            @if ($guest->status === 'confirmed')
                Queridos <strong>{{ $guest->full_name }}</strong> y acompañantes ¡ya recibimos su confirmación!   
                Si necesitan cambiar algún detalle, pueden actualizar su respuesta aquí mismo.
            @elseif ($guest->status === 'declined')
                Queridos <strong>{{ $guest->full_name }}</strong> y acompañantes lamentamos que no puedan asistir   
                Si cambian de opinión, pueden actualizar su respuesta desde este mismo formulario.
            @else
                Queridos <strong>{{ $guest->full_name }}</strong> y acompañantes es un honor invitarlos a nuestra boda.  
                Nos encantará saber si podrán acompañarnos en este día tan especial 
            @endif
        </p>

        <button class="btn" id="btnEntrar">
            {{ $hasResponded ? 'Actualizar respuesta' : 'Ingresar a la invitación' }}
        </button>
    </div>
</div>

<header class="hero" id="inicio">
    <div class="hero-overlay">
        <div class="hero-card">
            <p style="letter-spacing: 4px; color: var(--cream); text-align: center; margin-bottom: 10px; font-size: 0.9rem;">NUESTRA BODA</p>
            <h1 style="font-size: clamp(2rem, 5vw, 3.5rem); margin: 20px 0; line-height: 1.2;">
                {{ $event->bride_name }} <br> & <br> {{ $event->groom_name }}
            </h1>
            <p style="text-align: center; font-size: 1.1rem; letter-spacing: 1px;">
                {{ $fecha_completa }} @if($hora_evento_formato) • {{ $hora_evento_formato }} hrs @endif
            </p>
            <div style="text-align: center;">
                <a href="#rsvp" class="btn">Confirmar asistencia</a>
            </div>
        </div>
    </div>
</header>

<section class="section">
    <div class="container">
        <h2>La cuenta regresiva</h2>
        <div class="countdown" id="countdown" role="timer" aria-live="polite">
            <div class="countdown-item"><span id="dias">0</span><small>DÍAS</small></div>
            <div class="countdown-item"><span id="horas">0</span><small>HRS</small></div>
            <div class="countdown-item"><span id="minutos">0</span><small>MIN</small></div>
            <div class="countdown-item"><span id="segundos">0</span><small>SEG</small></div>
        </div>
        
        <div class="gold-border" style="margin-top: 60px; text-align: center;">
            <p style="font-size: 0.9rem; letter-spacing: 2px;">PASE PERSONAL PARA</p>
            <h3 style="font-size: 3rem; margin: 10px 0;">
                {{ $totalPases }}
            </h3>
            <p style="font-size: 0.9rem; letter-spacing: 2px;">
                PERSONA{{ $totalPases > 1 ? 'S' : '' }}
            </p>
        </div>
    </div>
</section>

<div class="grid-2">
    <div style="background: url('https://images.unsplash.com/photo-1519741497674-611481863552?w=800') center/cover; min-height: 400px;"></div>
    <div style="background: var(--cream); color: var(--dark); padding: 60px 40px; display: flex; flex-direction: column; justify-content: center;">
        <h2>Ceremonia</h2>
        <p style="text-align: center; font-size: 1.2rem; margin-top: 20px; line-height: 1.6;">
            {{ $event->reception_venue_name }} <br>
            <span style="font-size: 1rem; opacity: 0.8;">{{ $event->reception_venue_address }}</span>
        </p>
        <div style="text-align: center; margin-top: 30px; border: 1px solid var(--gold); padding: 20px;">
            @if($hora_evento_formato)
                <p style="margin-bottom: 10px;"><strong>HORA:</strong> {{ $hora_evento_formato }} HRS</p>
            @endif
            @if($event->dress_code)
                <p><strong>DRESS CODE:</strong> {{ $event->dress_code }}</p>
            @endif
        </div>

        {{-- Botones: calendario + mapa --}}
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn" id="btnCalendario">Guardar en mi calendario</button><br>
            @if($event->reception_maps_link)
                <a href="{{ $event->reception_maps_link }}" target="_blank" style="display:inline-block;margin-top:10px;color:var(--gold);text-decoration:underline;">
                    Ver ubicación en mapas
                </a>
            @endif
        </div>
    </div>
</div>

<section class="section">
    <div class="container" style="text-align: center;">
        <h2>Nuestros padres</h2>
        <div class="grid-2" style="margin-top: 40px; gap: 40px;">
            <div class="parents-box">
                <h3>Padres de la novia</h3>
                <ul>
                    @forelse($padres_novia as $p)
                        @if(!empty($p)) <li>{{ $p }}</li> @endif
                    @empty
                        <li>Familia de la Novia</li>
                    @endforelse
                </ul>
            </div>
            <div class="parents-box">
                <h3>Padres del novio</h3>
                <ul>
                    @forelse($padres_novio as $p)
                        @if(!empty($p)) <li>{{ $p }}</li> @endif
                    @empty
                        <li>Familia del Novio</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: #000;">
    <div class="container">
        <h2>Nuestra Historia</h2>
        <div class="gallery-frame">
             @forelse($event->eventPhotos as $photo)
                <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto de la pareja">
            @empty
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=600" alt="Foto de boda">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=600" alt="Foto de boda">
            @endforelse
        </div>
        <p style="text-align: center; margin-top: 10px; font-size: 0.8rem; color: var(--gold); opacity: 0.7;">DESLIZA PARA VER MÁS</p>
    </div>
</section>

<section class="section" id="rsvp">
    <div class="container" style="max-width: 600px;">
        <div class="gold-border" style="background: var(--cream); color: var(--dark); padding: 40px;">
            <h2>Confirma tu asistencia</h2>

            <p style="text-align: center; margin-bottom: 20px; font-size: 1rem;">
                @if ($guest->status === 'confirmed')
                    <strong>{{ $guest->full_name }}</strong>, ¡Ya recibimos tu confirmación!   
                    Si necesitas cambiar algún detalle, puedes actualizar tu respuesta aquí mismo.
                @elseif ($guest->status === 'declined')
                    <strong>{{ $guest->full_name }}</strong>, entimos que no podrás asistir   
                    Si cambias de opinión, puedes actualizar tu respuesta desde este mismo formulario.
                @else
                    <strong>{{ $guest->full_name }}</strong>, nos encantará saber si podrás acompañarnos
                    en este día tan especial 
                @endif
            </p>

            @if (!$isPreview)
                @if (session('rsvp_status'))
                    <div class="success-box">
                        <strong>¡Gracias por tu respuesta! </strong>
                        <p style="margin-top:5px;">{{ session('rsvp_status') }}</p>
                    </div>
                @elseif (!empty($alreadyConfirmed))
                    <div class="success-box">
                        <strong>Ya habías confirmado anteriormente</strong>
                        <p style="margin-top:5px;">Puedes actualizar tu respuesta si lo necesitas y volver a enviar el formulario.</p>
                    </div>
                @endif
            @endif

            @if ($isPreview)
                <p style="text-align: center; padding: 20px; border: 1px dashed var(--gold); margin-top: 20px;">
                    Vista previa del formulario que verán tus invitados.  
                    Los campos aquí están deshabilitados.
                </p>
            @else
                <form method="POST"
                      action="{{ route('rsvp.submit', ['slug' => $event->custom_url_slug, 'token' => $guest->invitation_token]) }}"
                      style="margin-top: 20px;">
                    @csrf

                    {{-- Asistencia --}}
                    <div style="margin-bottom: 20px; text-align:left;">
                        <label style="display:block; font-weight: bold; margin-bottom: 8px;">
                            ¿Podrás asistir?
                        </label>
                        <select name="status" class="rsvp-select">
                            <option value="" disabled {{ old('status', $guest->status) === null ? 'selected' : '' }}>Selecciona una opción</option>
                            <option value="confirmed" {{ old('status', $guest->status) === 'confirmed' ? 'selected' : '' }}>
                                Sí, con gusto asistiré 
                            </option>
                            <option value="declined" {{ old('status', $guest->status) === 'declined' ? 'selected' : '' }}>
                                No podré asistir 
                            </option>
                        </select>
                        @error('status')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Acompañantes --}}
                    @if ($guest->max_companions > 0)
                        <div style="margin-bottom: 20px; text-align:left;">
                            <label style="display:block; font-weight: bold; margin-bottom: 5px;">
                                ¿Cuántas personas te acompañan?
                            </label>
                            <p style="font-size: 0.85rem; margin-bottom: 5px;">
                                Puedes traer hasta {{ $guest->max_companions }} acompañante(s).
                            </p>
                            <input type="number"
                                   name="confirmed_companions"
                                   min="0"
                                   max="{{ $guest->max_companions }}"
                                   value="{{ old('confirmed_companions', $guest->confirmed_companions) }}"
                                   class="rsvp-input">
                            @error('confirmed_companions')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Restricciones alimentarias --}}
                    <div style="margin-bottom: 20px; text-align:left;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">
                            ¿Tienes alguna restricción alimentaria?
                        </label>
                        <textarea name="dietary_restrictions"
                                  class="rsvp-textarea"
                                  placeholder="Ejemplo: vegetariano, vegano, sin gluten, alergia a mariscos, etc.">{{ old('dietary_restrictions', $guest->dietary_restrictions) }}</textarea>
                        @error('dietary_restrictions')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mensaje para los novios --}}
                    <div style="margin-bottom: 20px; text-align:left;">
                        <label style="display:block; font-weight:bold; margin-bottom:5px;">
                            Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}
                        </label>
                        <textarea name="message_to_couple"
                                  rows="3"
                                  class="rsvp-textarea"
                                  placeholder="Déjales un mensaje bonito a los novios 💌">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>
                        @error('message_to_couple')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="text-align: center; margin-top:10px;">
                        <button type="submit" class="btn">
                            {{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}
                        </button>
                    </div>
                </form>
            @endif

            {{-- Resumen a la derecha en floral, aquí lo dejamos abajo --}}
            <div style="margin-top:30px; font-size:0.95rem; text-align:left; border-top:1px solid rgba(0,0,0,0.1); padding-top:15px;">
                <p><strong>Invitad@:</strong> {{ $guest->full_name }}</p>
                <p><strong>Evento:</strong> Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</p>
                <p><strong>Fecha:</strong> {{ $fecha_completa }} @if($hora_evento_formato) a las {{ $hora_evento_formato }} hrs @endif</p>
                <p><strong>Lugar:</strong> {{ $event->reception_venue_name }}</p>
                <p><strong>Dirección:</strong> {{ $event->reception_venue_address }}</p>
                <p style="margin-top:10px;">
                    <strong>Pases:</strong> {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
                </p>
                @if ($event->dress_code)
                    <p><strong>Código de vestimenta:</strong> {{ $event->dress_code }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<footer style="background: #000; padding: 40px; text-align: center; border-top: 1px solid var(--gold);">
    <p style="color: var(--gold); letter-spacing: 3px; font-size: 1.2rem;">
        {{ $event->bride_name }} & {{ $event->groom_name }}
    </p>
    <p style="opacity: 0.7; font-size: 0.9rem; margin-top: 10px;">
        {{ $fecha_evento->format('Y') }}
    </p>
</footer>

<script>
    // ==== Variables desde PHP ====
    const fechaEvento   = {{ $fechaEventoJs }}; // milisegundos
    const novia         = @json($event->bride_name);
    const novio         = @json($event->groom_name);
    const lugarNombre   = @json($event->reception_venue_name);

    // ==== YouTube / música (similar a la romántica, pero auto play al entrar) ====
    let youtubeUrl = @json($event->music_url ?? '');
    let videoId = youtubeUrl;

    // Extraer ID si es URL completa
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match  = youtubeUrl ? youtubeUrl.match(regExp) : null;
    if (match && match[2].length === 11) {
        videoId = match[2];
    }

    let player;

    function onYouTubeIframeAPIReady() {
        if (!videoId) return;
        player = new YT.Player('youtube-player', {
            videoId: videoId,
            height: '0',
            width: '0',
            playerVars: { autoplay: 0, loop: 1, playlist: videoId, controls: 0, modestbranding: 1 }
        });
    }

    // Cargar API de YouTube
    const tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    // ==== Modal inicial ====
    const modalInicial = document.getElementById('modalInicial');
    const btnEntrar    = document.getElementById('btnEntrar');

    btnEntrar.addEventListener('click', () => {
        modalInicial.classList.add('hidden');
        document.body.classList.remove('modal-open');

        if (player && typeof player.playVideo === 'function') {
            player.playVideo();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalInicial.classList.contains('hidden')) {
            btnEntrar.click();
        }
    });

    // ==== Countdown  ====
    function actualizarContador() {
        const ahora       = Date.now();
        const diferencia  = fechaEvento - ahora;

        if (isNaN(diferencia)) {
            console.error('fechaEvento inválida:', fechaEvento);
            return;
        }

        if (diferencia <= 0) {
            document.getElementById('countdown').innerHTML =
                '<div class="countdown-item" style="min-width:auto;padding:20px 40px">' +
                '<span style="font-size:1.6rem">¡Llegó el gran día! 🎉</span></div>';
            return;
        }

        const dias     = Math.floor(diferencia / (1000*60*60*24));
        const horas    = Math.floor((diferencia % (1000*60*60*24)) / (1000*60*60));
        const minutos  = Math.floor((diferencia % (1000*60*60)) / (1000*60));
        const segundos = Math.floor((diferencia % (1000*60)) / 1000);

        document.getElementById('dias').textContent     = dias;
        document.getElementById('horas').textContent    = horas;
        document.getElementById('minutos').textContent  = minutos;
        document.getElementById('segundos').textContent = segundos;
    }

    actualizarContador();
    setInterval(actualizarContador, 1000);

    // ==== Botón Añadir al calendario (.ics)====
    document.getElementById('btnCalendario').addEventListener('click', () => {
        function formatICSDate(date) {
            const pad = (num) => (num < 10 ? '0' + num : num);
            const year    = date.getUTCFullYear();
            const month   = pad(date.getUTCMonth() + 1);
            const day     = pad(date.getUTCDate());
            const hours   = pad(date.getUTCHours());
            const minutes = pad(date.getUTCMinutes());
            const seconds = pad(date.getUTCSeconds());
            return `${year}${month}${day}T${hours}${minutes}${seconds}Z`;
        }

        const fechaInicioJS = new Date(fechaEvento);
        const fechaFinJS    = new Date(fechaEvento + (5 * 60 * 60 * 1000)); // +5h

        const dtstart = formatICSDate(fechaInicioJS);
        const dtend   = formatICSDate(fechaFinJS);

        const eventoICS = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//FestLink//ES',
            'BEGIN:VEVENT',
            `DTSTART:${dtstart}`,
            `DTEND:${dtend}`,
            `SUMMARY:Boda de ${novia} y ${novio}`,
            `LOCATION:${lugarNombre}`,
            'DESCRIPTION:¡No te pierdas nuestra boda!',
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR'
        ].join('\r\n');

        const blob = new Blob([eventoICS], {type: 'text/calendar;charset=utf-8'});
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `boda-${novia.toLowerCase()}-${novio.toLowerCase()}.ics`;
        link.click();
    });
</script>
</body>
</html>
