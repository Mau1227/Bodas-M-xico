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

    // --- Helpers de plantilla ---
    $isPreview = $isPreview ?? false;

    $totalPases = 1 + (int) ($guest->max_companions ?? 0);

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
<title>Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Tenor+Sans&display=swap" rel="stylesheet">

<style>
/* === ESTILO MODERNO MINIMALISTA === */
:root { --primary: {{ $event->primary_color }}; --text: #1a1a1a; --bg: #ffffff; --gray: #f9f9f9; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Montserrat', sans-serif; color: var(--text); background: var(--bg); line-height: 1.6; overflow-x: hidden; }
body.modal-open { overflow: hidden; }
h1, h2, h3 { font-family: 'Tenor Sans', sans-serif; text-transform: uppercase; letter-spacing: 2px; font-weight: 400; }
img { max-width: 100%; height: auto; display: block; object-fit: cover; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.section { padding: 80px 0; }

.btn {
    display: inline-block; padding: 15px 40px;
    background: var(--text); color: white;
    text-transform: uppercase; font-size: 0.8rem;
    letter-spacing: 2px; border: 1px solid var(--text);
    text-decoration: none; transition: 0.3s; cursor: pointer;
    margin-top: 20px;
}
.btn:hover { background: transparent; color: var(--text); }

.hero {
    height: 100vh; display: flex; align-items: center; justify-content: center;
    position: relative; text-align: center; color: white;
}
.hero-bg {
    position: absolute; inset: 0; z-index: -1;
    background: url('{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600' }}')
                center/cover no-repeat;
    filter: brightness(0.6);
}
.hero h1 { font-size: clamp(2.5rem, 5vw, 5rem); margin-bottom: 10px; }

.grid-2 {
    display: grid; grid-template-columns: 1fr;
    gap: 40px; align-items: center;
}
@media(min-width: 768px) {
    .grid-2 { grid-template-columns: 1fr 1fr; gap: 80px; }
}

/* Countdown */
.countdown {
    display: flex; justify-content: center; gap: 20px;
    margin-top: 40px; flex-wrap: wrap;
}
.countdown-item { text-align: center; min-width: 80px; }
.countdown-item span {
    font-size: 3rem; font-family: 'Tenor Sans', sans-serif;
    line-height: 1; display: block;
}

/* Timeline */
.timeline-item {
    border-left: 1px solid var(--text);
    padding-left: 30px; padding-bottom: 40px; position: relative;
}
.timeline-item::before {
    content: ''; position: absolute; left: -5px; top: 5px;
    width: 9px; height: 9px; background: var(--text); border-radius: 50%;
}

/* Formularios */
input, textarea, select {
    width: 100%; padding: 15px;
    border: 1px solid #ddd; margin-bottom: 15px;
    font-family: inherit; background: white;
}
.error-text {
    color: #b91c1c;
    font-size: 0.8rem;
    margin-top: -8px;
    margin-bottom: 10px;
}
.success-box {
    background: #ecfdf3;
    border-left: 4px solid #16a34a;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #166534;
    margin-bottom: 16px;
}

/* YouTube & Música */
#youtube-player {
    position: absolute; top: -9999px; left: -9999px;
    width: 0; height: 0; opacity: 0; pointer-events: none;
}
.music-control {
    position: fixed; bottom: 20px; right: 20px;
    width: 50px; height: 50px; background: white;
    border-radius: 50%; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; z-index: 100; transition: transform 0.3s;
}
.music-control svg { fill: var(--text); }
.music-control.muted svg { opacity: 0.5; }
.music-control:hover { transform: scale(1.1); }

/* Modal */
.modal {
    position: fixed; inset: 0; background: white;
    z-index: 9999; display: flex; align-items: center;
    justify-content: center; padding: 20px; text-align: center;
    transition: 0.5s;
}
.modal.hidden { opacity: 0; visibility: hidden; pointer-events: none; }

/* Galería Minimalista */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 10px;
}
.gallery-img {
    width: 100%; height: 300px;
    object-fit: cover; filter: grayscale(100%);
    transition: 0.5s;
}
.gallery-img:hover { filter: grayscale(0%); }

/* Padres & Footer */
.parents-list { list-style: none; margin-top: 20px; font-size: 1.1rem; }
.parents-list li { margin-bottom: 10px; }
footer {
    background: var(--text); color: white;
    padding: 60px 0; text-align: center;
    letter-spacing: 2px; font-size: 0.9rem;
}
</style>
</head>
<body class="modal-open">

<div id="youtube-player"></div>
<div id="musicControl" class="music-control" title="Reproducir/Pausar música">
    <svg viewBox="0 0 24 24" width="24" height="24" id="musicIcon">
        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
    </svg>
</div>

{{-- MODAL INICIAL PERSONALIZADO POR INVITADO --}}
<div id="modalInicial" class="modal" role="dialog" aria-modal="true">
    <div class="container">
        <p style="letter-spacing: 2px; margin-bottom: 20px;">Bienvenidos</p>
        <p style="max-width:500px;margin:0 auto 20px;font-size:0.95rem;line-height:1.7;color:#444;">
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
        <button class="btn" id="btnEntrar" style="background: white; color: var(--text); border-color: var(--text);">
            {{ $hasResponded ? 'Actualizar respuesta' : 'Entrar a la invitación' }}
        </button>
    </div>
</div>

<header class="hero" id="inicio">
    <div class="hero-bg"></div>
    <div class="container">
        <p>SAVE THE DATE</p>
        <h1>{{ $event->bride_name }} <br> & <br> {{ $event->groom_name }}</h1>
        <p>
            {{ $fecha_completa }}
            @if($hora_evento_formato)
                • {{ $hora_evento_formato }} hrs
            @endif
        </p>
        <a href="#rsvp" class="btn" style="border-color: white; background: white; color: black;">
            Confirmar Asistencia
        </a>
    </div>
</header>

<section class="section">
    <div class="container" style="text-align: center;">
        <h2>Falta muy poco</h2>
        <div class="countdown" id="countdown" role="timer" aria-live="polite">
            <div class="countdown-item"><span id="dias">0</span><small>Días</small></div>
            <div class="countdown-item"><span id="horas">0</span><small>Hrs</small></div>
            <div class="countdown-item"><span id="minutos">0</span><small>Min</small></div>
            <div class="countdown-item"><span id="segundos">0</span><small>Seg</small></div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--gray);">
    <div class="container">
        <div class="grid-2">
            <div>
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800" alt="Pareja">
            </div>
            <div>
                <h2>Nuestra Historia</h2>
                <p style="margin: 20px 0; color: #555;">
                    {{ $frases_novia[0] ?? 'Juntos es nuestro lugar favorito.' }}
                </p>
                <div style="padding: 20px; border: 1px dashed var(--text); display: inline-block;">
                    <strong>
                        Pase para {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 50px;">Con la bendición de</h2>
        <div class="grid-2" style="text-align: center;">
            <div>
                <h3>Padres de la Novia</h3>
                <ul class="parents-list">
                    @forelse($padres_novia as $p)
                        @if(!empty($p)) <li>{{ $p }}</li> @endif
                    @empty
                        <li>Familia de la Novia</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3>Padres del Novio</h3>
                <ul class="parents-list">
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

<section class="section" style="background: var(--text);">
    <div class="container">
        <h2 style="text-align: center; color: white; margin-bottom: 40px;">Galería de Fotos</h2>
        <div class="gallery-grid">
            @forelse($event->eventPhotos as $photo)
                <img src="{{ asset('storage/' . $photo->photo_url) }}" class="gallery-img" alt="Foto del evento">
            @empty
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=600" class="gallery-img" alt="Foto de boda">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=600" class="gallery-img" alt="Foto de boda">
                <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=600" class="gallery-img" alt="Foto de boda">
            @endforelse
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2">
            <div>
                <h2>Cuándo & Dónde</h2>
                <p style="font-size: 1.5rem; margin-top: 10px;">
                    {{ $event->reception_venue_name }}
                </p>
                <p>{{ $event->reception_venue_address }}</p>
                @if($hora_evento_formato)
                    <p style="margin-top: 20px; font-weight: 600;">{{ $hora_evento_formato }} HRS</p>
                @endif
                
                <div style="margin-top: 30px; border-left: 4px solid var(--text); padding-left: 20px;">
                    <h3>Código de Vestimenta</h3>
                    <p>{{ $event->dress_code }}</p>
                </div>
                
                <button id="btnCalendario" class="btn">Agendar</button>

                @if($event->reception_maps_link)
                    <p style="margin-top: 15px;">
                        <a href="{{ $event->reception_maps_link }}" target="_blank" style="text-decoration:underline;color:var(--text);">
                            Ver ubicación en mapas
                        </a>
                    </p>
                @endif
            </div>
            <div style="height: 400px; background: #eee;">
                <iframe src="{{ $event->reception_maps_link }}" width="100%" height="100%" frameborder="0" style="filter: grayscale(1);"></iframe>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--gray);">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 40px;">Itinerario</h2>
        <div style="max-width: 600px; margin: 0 auto;">
            @forelse($event->itineraryItems->sortBy('time') as $item)
                <div class="timeline-item">
                    <strong style="font-size: 1.2rem;">
                        {{ date('H:i', strtotime($item->time)) }}
                    </strong>
                    <span style="margin-left: 20px; color: #555;">
                        {{ $item->activity }}
                    </span>
                </div>
            @empty
                <p style="text-align:center;color:#777;">Los novios están definiendo los detalles del itinerario.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="section" id="rsvp">
    <div class="container" style="max-width: 600px; text-align: center;">
        <h2>Confirma tu asistencia</h2>

        <p style="margin: 15px 0 25px; color: #666;">
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

        {{-- Mensajes de estado (modo real) --}}
        @if (!$isPreview)
            @if (session('rsvp_status'))
                <div class="success-box">
                    <strong>¡Gracias por tu respuesta! </strong>
                    <p style="margin-top:4px;">{{ session('rsvp_status') }}</p>
                </div>
            @elseif (!empty($alreadyConfirmed))
                <div class="success-box">
                    <strong>Ya habías confirmado anteriormente</strong>
                    <p style="margin-top:4px;">Puedes actualizar tu respuesta si lo necesitas y volver a enviar el formulario.</p>
                </div>
            @endif
        @endif

        @if (!$isPreview)
        <form method="POST"
              action="{{ route('rsvp.submit', ['slug' => $event->custom_url_slug, 'token' => $guest->invitation_token]) }}"
              style="text-align: left; margin-top: 15px;">
            @csrf

            <label style="font-weight:600;">¿Asistirás?</label>
            <div style="margin-bottom: 8px; margin-top: 5px;">
                <label style="margin-right: 20px;">
                    <input type="radio"
                           name="status"
                           value="confirmed"
                           @checked(old('status', $guest->status) === 'confirmed')>
                    Sí, asistiré
                </label>
                <label>
                    <input type="radio"
                           name="status"
                           value="declined"
                           @checked(old('status', $guest->status) === 'declined')>
                    No podré
                </label>
            </div>
            @error('status')
                <p class="error-text">{{ $message }}</p>
            @enderror

            @if ($guest->max_companions > 0)
                <label style="font-weight:600;">Acompañantes (Máx {{ $guest->max_companions }})</label>
                <input type="number"
                       name="confirmed_companions"
                       min="0"
                       max="{{ $guest->max_companions }}"
                       value="{{ old('confirmed_companions', $guest->confirmed_companions) }}">
                @error('confirmed_companions')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            @endif

            <label style="font-weight:600;">Restricciones Alimentarias</label>
            <textarea name="dietary_restrictions" rows="2">{{ old('dietary_restrictions', $guest->dietary_restrictions) }}</textarea>
            @error('dietary_restrictions')
                <p class="error-text">{{ $message }}</p>
            @enderror

            <label style="font-weight:600;">Mensaje</label>
            <textarea name="message_to_couple" rows="3">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>
            @error('message_to_couple')
                <p class="error-text">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">
                {{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}
            </button>
        </form>
        @else
        <div style="padding: 40px; border: 1px solid #ddd; margin-top: 20px;">
            Vista previa del formulario RSVP (los campos reales aparecerán aquí para tus invitados).
        </div>
        @endif
    </div>
</section>

<footer>
    <p>{{ $event->bride_name }} & {{ $event->groom_name }}</p>
    <p>{{ $fecha_evento->format('Y') }}</p>
</footer>

<script>
    // ==== Variables base ====
    const fechaEvento = {{ $fechaEventoJs }};
    const novia       = @json($event->bride_name);
    const novio       = @json($event->groom_name);
    const lugarNombre = @json($event->reception_venue_name);

    // ==== YouTube & Música ====
    let youtubeUrl = @json($event->music_url ?? '');
    let videoId = youtubeUrl;

    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = youtubeUrl ? youtubeUrl.match(regExp) : null;
    if (match && match[2].length === 11) {
        videoId = match[2];
    }

    let player;
    let isMusicPlaying = false;

    function onYouTubeIframeAPIReady() {
        if (!videoId) return;
        player = new YT.Player('youtube-player', {
            height: '0',
            width: '0',
            videoId: videoId,
            playerVars: { autoplay: 0, loop: 1, playlist: videoId, controls: 0, modestbranding: 1 }
        });
    }

    // Cargar API de YouTube
    (function(){
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    })();

    // Modal inicial
    const modalInicial = document.getElementById('modalInicial');
    const btnEntrar    = document.getElementById('btnEntrar');

    btnEntrar.addEventListener('click', function() {
        modalInicial.classList.add('hidden');
        document.body.classList.remove('modal-open');
        if (player && typeof player.playVideo === 'function') {
            player.playVideo();
            isMusicPlaying = true;
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalInicial.classList.contains('hidden')) {
            btnEntrar.click();
        }
    });

    // Botón de música (play/pause)
    const musicControl = document.getElementById('musicControl');
    const musicIcon    = document.getElementById('musicIcon');

    musicControl.addEventListener('click', () => {
        if (!player || typeof player.playVideo !== 'function') return;

        if (isMusicPlaying) {
            player.pauseVideo();
            isMusicPlaying = false;
            musicControl.classList.add('muted');
            musicIcon.innerHTML = '<path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>';
        } else {
            player.playVideo();
            isMusicPlaying = true;
            musicControl.classList.remove('muted');
            musicIcon.innerHTML = '<path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>';
        }
    });

    // Countdown
    function updateCountdown() {
        const now = Date.now();
        const distance = fechaEvento - now;

        if (isNaN(distance)) {
            console.error('fechaEvento inválida:', fechaEvento);
            return;
        }

        if (distance <= 0) {
            document.getElementById('countdown').innerHTML =
                '<div class="countdown-item" style="min-width:auto;padding:20px 40px">' +
                '<span style="font-size:1.6rem">¡Llegó el gran día! 🎉</span></div>';
            return;
        }

        const dias     = Math.floor(distance / (1000 * 60 * 60 * 24));
        const horas    = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutos  = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const segundos = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('dias').textContent     = dias;
        document.getElementById('horas').textContent    = horas;
        document.getElementById('minutos').textContent  = minutos;
        document.getElementById('segundos').textContent = segundos;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Botón Agendar -> archivo .ics como en las otras plantillas
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
