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
<title>Boda {{ $event->bride_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Great+Vibes&display=swap" rel="stylesheet">
<style>
/* === BOHO CHIC === */
:root { --sage: {{ $event->primary_color }}; --sand: #fdfcf8; --text: #5d5c58; --accent: #d4a373; }
body { font-family: 'Cormorant Garamond', serif; color: var(--text); background: var(--sand); font-size: 18px; margin: 0; overflow-x: hidden; }
body.modal-open { overflow: hidden; }
h1, h2, h3 { font-weight: 600; color: var(--sage); margin: 0; }
.script { font-family: 'Great Vibes', cursive; color: var(--accent); font-size: 2.5rem; }
.container { max-width: 1000px; margin: 0 auto; padding: 0 20px; }
.section { padding: 70px 0; }
.btn { background: var(--sage); color: white; padding: 15px 35px; border-radius: 50px; border: none; font-family: inherit; font-size: 1.2rem; cursor: pointer; display: inline-block; text-decoration: none; margin-top: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
.btn:hover { transform: translateY(-2px); }
.arch-img { border-radius: 200px 200px 0 0; width: 100%; height: 450px; object-fit: cover; display: block; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
.hero { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background: url('{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600' }}') center/cover; position: relative; }
.hero::after { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.3); }
.hero-content { z-index: 2; background: rgba(255,255,255,0.9); padding: 50px 30px; border-radius: 300px 300px 0 0; width: 90%; max-width: 500px; margin-bottom: -50px; padding-top: 80px; box-shadow: 0 -10px 40px rgba(0,0,0,0.1); }
.grid-2 { display: grid; grid-template-columns: 1fr; gap: 40px; align-items: center; }
@media(min-width: 768px) { .grid-2 { grid-template-columns: 1fr 1fr; } }
.countdown { display: flex; justify-content: center; gap: 15px; margin: 30px 0; }
.circle { width: 70px; height: 70px; border: 1px solid var(--sage); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 1.2rem; }
.circle small { font-size: 0.6rem; text-transform: uppercase; }
#youtube-player { position: absolute; width: 0; height: 0; opacity: 0; pointer-events: none; }
.modal { position: fixed; inset: 0; background: rgba(255,255,255,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; transition: 0.5s; padding: 20px; }
.modal.hidden { opacity: 0; pointer-events: none; }

/* Galería Boho (Slider) */
.carousel { overflow-x: auto; display: flex; gap: 15px; padding-bottom: 20px; scroll-snap-type: x mandatory; }
.carousel img { width: 80%; max-width: 400px; height: 400px; object-fit: cover; border-radius: 20px; scroll-snap-align: center; flex-shrink: 0; }

/* Padres */
.parents-col { text-align: center; }
.parents-col h3 { font-family: 'Great Vibes', cursive; font-size: 2rem; color: var(--accent); margin-bottom: 15px; }
.parents-col ul { list-style: none; padding: 0; font-size: 1.2rem; }

/* Mensajes RSVP */
.error-text { color: #b91c1c; font-size: 0.8rem; margin-top: 4px; margin-bottom: 8px; }
.success-box {
    background: #ecfdf3;
    border-left: 4px solid #16a34a;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.9rem;
    color: #166534;
    margin-bottom: 20px;
}

/* Footer */
footer { background: var(--sage); color: white; padding: 50px 0; text-align: center; }
</style>
</head>
<body class="modal-open">

<div id="youtube-player"></div>

{{-- MODAL INICIAL CON MENSAJE SEGÚN ESTADO DEL INVITADO --}}
<div id="modalInicial" class="modal" role="dialog" aria-modal="true">
    <div style="text-align: center; max-width:480px;">
        <span class="script">Bienvenidos</span>
        <p style="font-size:1rem; line-height:1.7; margin-bottom: 20px;">
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
            {{ $hasResponded ? 'Actualizar respuesta' : 'Ver invitación' }}
        </button>
    </div>
</div>

<header class="hero" id="inicio">
    <div class="hero-content">
        <span class="script">Nos casamos</span>
        <h1 style="font-size: 3rem; line-height: 1;">
            {{ $event->bride_name }} <br> & <br> {{ $event->groom_name }}
        </h1>
        <p style="text-transform: uppercase; letter-spacing: 2px; margin-top: 15px;">
            {{ $fecha_completa }}
            @if($hora_evento_formato)
                • {{ $hora_evento_formato }} hrs
            @endif
        </p>
    </div>
</header>

<section class="section">
    <div class="container" style="text-align: center;">
        <span class="script">Save the Date</span>
        <div class="countdown" id="countdown" role="timer" aria-live="polite">
            <div class="circle"><span id="dias">0</span><small>Días</small></div>
            <div class="circle"><span id="horas">0</span><small>Hrs</small></div>
            <div class="circle"><span id="minutos">0</span><small>Min</small></div>
            <div class="circle"><span id="segundos">0</span><small>Seg</small></div>
        </div>
    </div>
</section>

<section class="section" style="background: #f4f1ea;">
    <div class="container">
        <div class="grid-2">
            <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800" class="arch-img" alt="Pareja">
            <div style="text-align: center; padding: 20px;">
                <h2>Nuestra Historia</h2>
                <p style="margin: 20px 0; font-size: 1.3rem;">
                    "{{ $frases_novia[0] ?? 'Amor es solo una palabra hasta que alguien llega para darle sentido.' }}"
                </p>
                <div style="border: 1px dashed var(--sage); padding: 15px; border-radius: 15px; display: inline-block;">
                    Pase para {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2">
            <div class="parents-col">
                <h3>Padres de la Novia</h3>
                <ul>
                    @forelse($padres_novia as $p)
                        @if(!empty($p)) <li>{{ $p }}</li> @endif
                    @empty
                        <li>Familia de la Novia</li>
                    @endforelse
                </ul>
            </div>
            <div class="parents-col">
                <h3>Padres del Novio</h3>
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

<section class="section" style="background: #f4f1ea;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 30px;">Momentos Inolvidables</h2>
        <div class="carousel">
            @forelse($event->eventPhotos as $photo)
                <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto del evento">
            @empty
                <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=600" alt="Foto de boda">
                <img src="https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=600" alt="Foto de boda">
                <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=600" alt="Foto de boda">
            @endforelse
        </div>
        <p style="text-align:center; font-size:0.9rem; margin-top:10px; opacity:0.6;">(Desliza para ver más)</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-2">
            <div>
                <span class="script">Ceremonia</span>
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">
                    {{ $event->reception_venue_name }}
                </h2>
                <p>{{ $event->reception_venue_address }}</p>
                
                <div style="margin-top: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <h3 style="font-size: 1.5rem; color: var(--accent);">Código de Vestimenta</h3>
                    <p>{{ $event->dress_code }}</p>
                </div>

                <button class="btn" id="btnCalendario">Mapa & Calendario</button>

                @if($event->reception_maps_link)
                    <p style="margin-top: 12px;">
                        <a href="{{ $event->reception_maps_link }}" target="_blank" style="color:var(--accent);text-decoration:underline;">
                            Ver ubicación en mapas
                        </a>
                    </p>
                @endif
            </div>
            <div>
                 <iframe src="{{ $event->reception_maps_link }}"
                         width="100%"
                         height="300"
                         frameborder="0"
                         style="border-radius: 20px;"></iframe>
            </div>
        </div>
    </div>
</section>

<section class="section" id="rsvp" style="background: var(--sage); color: white;">
    <div class="container" style="max-width: 600px; text-align: center;">
        <h2 style="color: white; font-size: 3rem;">Confirma tu asistencia</h2>

        <p style="margin-bottom: 20px;">
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

        {{-- Mensajes de estado (solo sentido en modo real) --}}
        @if(!$isPreview)
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
              style="text-align: left; background: white; padding: 40px; border-radius: 20px; color: var(--text); margin-top: 10px;">
            @csrf

            <label style="display:block; margin-bottom: 10px; font-weight: bold;">¿Nos acompañas?</label>
            <div style="margin-bottom: 10px;">
                <label>
                    <input type="radio"
                           name="status"
                           value="confirmed"
                           @checked(old('status', $guest->status) === 'confirmed')>
                    ¡Sí, acepto!
                </label><br>
                <label>
                    <input type="radio"
                           name="status"
                           value="declined"
                           @checked(old('status', $guest->status) === 'declined')>
                    No podré asistir
                </label>
            </div>
            @error('status')
                <p class="error-text">{{ $message }}</p>
            @enderror

            @if ($guest->max_companions > 0)
            <input type="number"
                   name="confirmed_companions"
                   placeholder="Acompañantes (Máx {{ $guest->max_companions }})"
                   min="0"
                   max="{{ $guest->max_companions }}"
                   value="{{ old('confirmed_companions', $guest->confirmed_companions) }}"
                   style="width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 5px;">
            @error('confirmed_companions')
                <p class="error-text">{{ $message }}</p>
            @enderror
            @endif

            <textarea name="dietary_restrictions"
                      placeholder="Restricciones alimentarias (si las hay)..."
                      rows="2"
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 10px;">{{ old('dietary_restrictions', $guest->dietary_restrictions) }}</textarea>
            @error('dietary_restrictions')
                <p class="error-text">{{ $message }}</p>
            @enderror

            <textarea name="message_to_couple"
                      placeholder="Deja un mensaje..."
                      rows="3"
                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>
            @error('message_to_couple')
                <p class="error-text">{{ $message }}</p>
            @enderror

            <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">
                {{ $hasResponded ? 'Actualizar respuesta' : 'Confirmar' }}
            </button>
        </form>
        @else
            <div style="background: white; padding: 20px; border-radius: 20px; color: black; margin-top: 10px;">
                Vista previa RSVP (aquí verás cómo se mostrará el formulario real).
            </div>
        @endif
    </div>
</section>

<footer>
    <h2 style="color: white; font-size: 2rem; margin-bottom: 10px;">
        {{ $event->bride_name }} & {{ $event->groom_name }}
    </h2>
    <p>{{ $fecha_evento->format('Y') }} &mdash; Gracias por ser parte de nuestra historia.</p>
</footer>

<script>
    // ==== Variables base ====
    const fechaEvento = {{ $fechaEventoJs }};
    const novia       = @json($event->bride_name);
    const novio       = @json($event->groom_name);
    const lugarNombre = @json($event->reception_venue_name);

    // ==== YouTube ====
    let youtubeUrl = @json($event->music_url ?? '');
    let videoId = youtubeUrl;
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = youtubeUrl ? youtubeUrl.match(regExp) : null;
    if (match && match[2].length === 11) { videoId = match[2]; }

    let player;

    function onYouTubeIframeAPIReady() {
        if(!videoId) return;
        player = new YT.Player('youtube-player', {
            height: '0',
            width: '0',
            videoId: videoId,
            playerVars: { autoplay: 0, loop: 1, playlist: videoId, controls: 0, modestbranding: 1 }
        });
    }

    // Cargar API YT
    (function(){
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    })();

    // Modal
    const modalInicial = document.getElementById('modalInicial');
    const btnEntrar    = document.getElementById('btnEntrar');

    btnEntrar.addEventListener('click', function() {
        modalInicial.classList.add('hidden');
        document.body.classList.remove('modal-open');
        if(player && typeof player.playVideo === 'function') {
            player.playVideo();
        }
    });

    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape' && !modalInicial.classList.contains('hidden')) {
            btnEntrar.click();
        }
    });

    // Countdown
    function updateCountdown() {
        const now       = Date.now();
        const distance  = fechaEvento - now;

        if (isNaN(distance)) {
            console.error('fechaEvento inválida:', fechaEvento);
            return;
        }

        if (distance <= 0) {
            const countdown = document.getElementById('countdown');
            if (countdown) {
                countdown.innerHTML =
                    '<div class="circle" style="width:auto;padding:10px 20px;border-radius:999px;">' +
                    '<span style="font-size:1.1rem;">¡Llegó el gran día! 🎉</span>' +
                    '</div>';
            }
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

    // Botón Mapa & Calendario (.ics)
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
