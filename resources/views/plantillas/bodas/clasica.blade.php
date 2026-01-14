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

    // --- Helpers generales de plantilla ---
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
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<style>
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Crimson+Text:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@300;400;500;600&display=swap');

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold: {{ $event->primary_color }};
  --dark: {{ $event->secondary_color ?? '#222222' }};
  --black: #1a1a1a;
  --cream: #f8f6f0;
  --white: #ffffff;
  --gold-light: #d4af37;
}
html{scroll-behavior:smooth}
body{
  font-family:'Montserrat',sans-serif;
  color:var(--black);
  background:var(--black);
  line-height:1.7;
  overflow-x:hidden;
}
body.modal-open{overflow:hidden}
h1,h2,h3,h4{
  font-family:'Cinzel',serif;
  font-weight:700;
  line-height:1.2;
  letter-spacing:3px;
  text-transform:uppercase;
}
img{max-width:100%;height:auto;display:block}
button{cursor:pointer;font-family:inherit;border:none}
a{color:var(--gold);text-decoration:none;transition:color 0.3s}
a:hover{color:var(--gold-light)}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
.section{padding:100px 0;position:relative;background:var(--cream)}
@media(max-width:768px){.section{padding:60px 0}}

/* Música */
.music-control{
  position:fixed;
  left:30px;
  bottom:30px;
  z-index:9998;
  width:60px;
  height:60px;
  background:var(--gold);
  border:2px solid var(--black);
  box-shadow:0 4px 20px rgba(212,175,55,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition:all 0.3s;
}
.music-control:hover{
  background:var(--gold-light);
  transform:scale(1.1);
}
.music-control svg{width:28px;height:28px;fill:var(--black)}
.music-control.muted{background:var(--dark);opacity:0.7}
#youtube-player{display:none}

/* Animaciones */
.fade-in{opacity:0;transform:translateY(40px);transition:opacity 0.8s,transform 0.8s}
.fade-in.visible{opacity:1;transform:translateY(0)}

/* Modal */
.modal{
  position:fixed;
  inset:0;
  background:rgba(26,26,26,0.97);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:9999;
  opacity:0;
  visibility:hidden;
  transition:opacity 0.5s,visibility 0.5s;
}
.modal.active{opacity:1;visibility:visible}
.modal-content{
  background:var(--cream);
  padding:60px;
  max-width:600px;
  width:90%;
  text-align:center;
  box-shadow:0 0 80px rgba(212,175,55,0.3);
  border:3px solid var(--gold);
  position:relative;
}
.modal-content::before{
  content:'';
  position:absolute;
  inset:-10px;
  border:1px solid var(--gold);
  opacity:0.5;
}
.modal-content h2{
  font-size:2.5rem;
  margin-bottom:25px;
  color:var(--black);
  letter-spacing:4px;
}
.modal-content p{
  margin-bottom:40px;
  color:var(--dark);
  font-size:1.1rem;
  line-height:1.8;
}

/* Botones */
.btn{
  display:inline-block;
  padding:18px 50px;
  background:var(--gold);
  color:var(--black);
  font-size:0.9rem;
  font-weight:600;
  letter-spacing:3px;
  text-transform:uppercase;
  transition:all 0.3s;
  border:2px solid var(--gold);
  position:relative;
  overflow:hidden;
}
.btn::before{
  content:'';
  position:absolute;
  inset:0;
  background:var(--black);
  transform:scaleX(0);
  transform-origin:left;
  transition:transform 0.4s;
  z-index:0;
}
.btn:hover::before{transform:scaleX(1)}
.btn span{position:relative;z-index:1}
.btn:hover{
  color:var(--gold);
  box-shadow:0 8px 30px rgba(212,175,55,0.4);
}

/* Hero */
.hero{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  position:relative;
  background:linear-gradient(rgba(26,26,26,0.5),rgba(26,26,26,0.7)),
              url('{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600' }}') center/cover fixed;
  color:var(--white);
}
.hero::after{
  content:'';
  position:absolute;
  inset:0;
  border:20px solid rgba(212,175,55,0.2);
  pointer-events:none;
}
.hero-content{
  position:relative;
  z-index:2;
  padding:50px 30px;
}
.hero h1{
  font-size:clamp(3rem,10vw,7rem);
  margin-bottom:40px;
  text-shadow:3px 3px 10px rgba(0,0,0,0.8);
  letter-spacing:8px;
  font-weight:900;
}
.hero h1::before,
.hero h1::after{
  content:'◆';
  display:inline-block;
  margin:0 30px;
  color:var(--gold);
  font-size:0.4em;
}
.hero p{
  font-size:1.4rem;
  margin-bottom:50px;
  font-weight:300;
  letter-spacing:4px;
  font-family:'Crimson Text',serif;
  font-style:italic;
}

/* Sección título */
.section-title{
  font-size:clamp(2.5rem,6vw,4rem);
  text-align:center;
  margin-bottom:80px;
  color:var(--black);
  position:relative;
  display:inline-block;
  left:50%;
  transform:translateX(-50%);
}
.section-title::before,
.section-title::after{
  content:'';
  position:absolute;
  top:50%;
  width:100px;
  height:2px;
  background:var(--gold);
}
.section-title::before{right:calc(100% + 30px)}
.section-title::after{left:calc(100% + 30px)}

/* Contador */
.countdown-section{
  background:var(--black);
  color:var(--white);
}
.countdown-section .section-title{
  color:var(--gold);
}
.countdown-section .section-title::before,
.countdown-section .section-title::after{
  background:var(--gold);
}
.countdown{
  display:flex;
  gap:40px;
  justify-content:center;
  flex-wrap:wrap;
}
.countdown-item{
  background:transparent;
  padding:40px;
  border:2px solid var(--gold);
  min-width:140px;
  position:relative;
}
.countdown-item::before{
  content:'';
  position:absolute;
  inset:-10px;
  border:1px solid var(--gold);
  opacity:0.3;
}
.countdown-item span{
  display:block;
  font-size:4rem;
  font-weight:700;
  color:var(--gold);
  font-family:'Cinzel',serif;
}
.countdown-item small{
  font-size:0.85rem;
  text-transform:uppercase;
  letter-spacing:3px;
  color:var(--white);
  font-weight:600;
  margin-top:10px;
  display:block;
}

/* Grid */
.grid-2{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
  gap:60px;
  align-items:center;
}

/* Cards */
.card{
  background:var(--white);
  padding:50px;
  border:2px solid var(--gold);
  position:relative;
  transition:all 0.4s;
}
.card::before{
  content:'';
  position:absolute;
  inset:-8px;
  border:1px solid var(--gold);
  opacity:0;
  transition:opacity 0.4s;
}
.card:hover::before{opacity:0.5}
.card:hover{
  transform:translateY(-10px);
  box-shadow:0 20px 60px rgba(0,0,0,0.2);
}

/* Texto invitación */
.pases-info{
  margin-top:30px;
  font-size:1.3rem;
  color:var(--gold);
  font-weight:600;
  padding:20px;
  border:2px solid var(--gold);
  text-align:center;
  letter-spacing:2px;
  background:rgba(212,175,55,0.05);
}

/* Padres */
.parent-card h3{
  color:var(--black);
  margin-bottom:30px;
  text-align:center;
  font-size:2rem;
  letter-spacing:3px;
}
.parent-card ul{
  list-style:none;
  text-align:center;
  font-size:1.2rem;
  line-height:2.5;
  font-family:'Crimson Text',serif;
}

/* Carrusel */
.carousel{
  position:relative;
  max-width:900px;
  margin:0 auto;
  border:5px solid var(--gold);
  box-shadow:0 30px 80px rgba(0,0,0,0.4);
}
.carousel-inner{
  display:flex;
  transition:transform 0.7s ease;
}
.carousel-item{min-width:100%}
.carousel-item img{
  width:100%;
  height:600px;
  object-fit:cover;
}
.carousel-btn{
  position:absolute;
  top:50%;
  transform:translateY(-50%);
  background:var(--gold);
  width:60px;
  height:60px;
  font-size:2.5rem;
  z-index:2;
  transition:all 0.3s;
  color:var(--black);
  display:flex;
  align-items:center;
  justify-content:center;
  border:2px solid var(--black);
  font-weight:700;
}
.carousel-btn:hover{
  background:var(--black);
  color:var(--gold);
}
.carousel-btn.prev{left:0}
.carousel-btn.next{right:0}

.frases{
  text-align:center;
  margin-top:50px;
  padding:40px;
  background:var(--black);
  color:var(--gold);
  border:2px solid var(--gold);
  min-height:150px;
  position:relative;
}
.frases p{
  font-style:italic;
  font-size:1.4rem;
  font-family:'Crimson Text',serif;
  line-height:2;
}
.frases cite{
  display:block;
  margin-top:20px;
  font-size:1.1rem;
  font-style:normal;
  font-weight:600;
  letter-spacing:2px;
  text-transform:uppercase;
}

/* Save date */
.save-date-section{
  background:var(--gold);
  color:var(--black);
}
.save-date-section .section-title{color:var(--black)}
.save-date-section .section-title::before,
.save-date-section .section-title::after{background:var(--black)}
.save-date-section p{
  font-size:1.5rem;
  margin-bottom:40px;
  font-family:'Crimson Text',serif;
  letter-spacing:1px;
}

/* Venue */
.venue-info h3{
  color:var(--black);
  font-size:2.5rem;
  margin-bottom:25px;
  letter-spacing:3px;
}
.venue-info p{
  font-size:1.2rem;
  margin-bottom:20px;
  line-height:1.9;
}
.venue-img{
  margin-top:30px;
  border:5px solid var(--gold);
  box-shadow:0 20px 60px rgba(0,0,0,0.3);
}
.map-container{
  border:5px solid var(--gold);
  height:500px;
  box-shadow:0 20px 60px rgba(0,0,0,0.3);
}
.map-container iframe{width:100%;height:100%;border:0}

/* Vestimenta */
.dress-card{text-align:center}
.dress-card h3{
  color:var(--black);
  margin-bottom:20px;
  font-size:2rem;
  letter-spacing:3px;
}
.dress-card p{
  font-size:1.1rem;
  line-height:1.9;
  color:var(--dark);
  max-width:450px;
  margin:0 auto;
}
.dress-note{
  margin-top:20px;
  text-align:center;
  font-size:1rem;
  font-style:italic;
}

/* Timeline */
.timeline{
  position:relative;
  padding-left:80px;
  max-width:900px;
  margin:0 auto;
}
.timeline::before{
  content:'';
  position:absolute;
  left:30px;
  top:0;
  bottom:0;
  width:2px;
  background:var(--gold);
}
.timeline-item{
  position:relative;
  margin-bottom:60px;
  padding:35px;
  background:var(--white);
  border:2px solid var(--gold);
  transition:all 0.3s;
}
.timeline-item:hover{
  transform:translateX(15px);
  box-shadow:0 15px 50px rgba(0,0,0,0.2);
}
.timeline-item::before{
  content:'';
  position:absolute;
  left:-62px;
  top:35px;
  width:20px;
  height:20px;
  background:var(--gold);
  border:3px solid var(--black);
  transform:rotate(45deg);
}
.timeline-item h3{
  color:var(--black);
  margin-bottom:10px;
  font-size:1.5rem;
  letter-spacing:2px;
}
.timeline-item p{
  color:var(--dark);
  font-size:1rem;
  line-height:1.8;
}

/* RSVP */
.rsvp-section{text-align:center}
.rsvp-section p{
  font-size:1.3rem;
  margin-bottom:40px;
  font-family:'Crimson Text',serif;
  color:var(--dark);
}
.form-group{margin-bottom:25px;text-align:left}
.form-group label{
  display:block;
  margin-bottom:10px;
  font-weight:600;
  color:var(--black);
  font-size:1rem;
  letter-spacing:1px;
  text-transform:uppercase;
}
.form-group input,
.form-group textarea,
.form-group select{
  width:100%;
  padding:15px 20px;
  border:2px solid var(--gold);
  font-size:1rem;
  font-family:inherit;
  transition:all 0.3s;
  background:var(--white);
}
.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus{
  outline:none;
  border-color:var(--black);
  box-shadow:0 0 20px rgba(212,175,55,0.3);
}
.success-message{
  background:rgba(212,175,55,0.1);
  padding:35px;
  border:2px solid var(--gold);
  margin-top:30px;
}
.success-message h3{
  color:var(--black);
  margin-bottom:15px;
  font-size:1.8rem;
  letter-spacing:2px;
}

/* Footer */
footer{
  background:var(--black);
  color:var(--gold);
  padding:60px 0;
  text-align:center;
  border-top:3px solid var(--gold);
}
footer p{
  font-family:'Cinzel',serif;
  font-size:1.2rem;
  letter-spacing:3px;
  text-transform:uppercase;
}

@media(max-width:768px){
  .hero h1{font-size:2.5rem;letter-spacing:4px}
  .hero h1::before,.hero h1::after{margin:0 15px}
  .section-title{font-size:2rem}
  .section-title::before,.section-title::after{width:50px}
  .countdown{gap:20px}
  .countdown-item{min-width:100px;padding:25px 20px}
  .countdown-item span{font-size:2.5rem}
  .carousel-item img{height:400px}
  .timeline{padding-left:60px}
  .timeline::before{left:15px}
  .timeline-item::before{left:-47px}
}
</style>
</head>
<body class="modal-open">

<!-- Control de música -->
<div id="musicControl" class="music-control" title="Reproducir/Pausar música">
  <svg viewBox="0 0 24 24" id="musicIcon">
    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
  </svg>
</div>
<div id="youtube-player"></div>

{{-- MODAL INICIAL --}}
<div id="modalInicial" class="modal active" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
  <div class="modal-content">
    <h2 id="modalTitle">Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</h2>
    <p style="font-size:1.3rem;">
      @if ($guest->status === 'confirmed')
          Querid@ <strong>{{ $guest->full_name }}</strong>, ¡ya recibimos tu confirmación! 🥳  
          Si necesitas cambiar algún detalle, puedes actualizar tu respuesta aquí mismo.
      @elseif ($guest->status === 'declined')
          Querid@ <strong>{{ $guest->full_name }}</strong>, sentimos que no podrás asistir 💚  
          Si cambias de opinión, puedes actualizar tu respuesta desde este mismo formulario.
      @else
          Querid@ <strong>{{ $guest->full_name }}</strong>, nos encantará saber si podrás acompañarnos
          en este día tan especial 💌
      @endif
    </p>
    <button type="button" class="btn" id="btnEntrar">
        <span>{{ $hasResponded ? 'Actualizar respuesta' : 'Ver invitación' }}</span>
    </button>
  </div>
</div>

<section class="hero" id="inicio">
  <div class="hero-content">
    <h1>{{ $event->bride_name }} & {{ $event->groom_name }}</h1>
    <p>Nos casamos y queremos que seas parte de este momento inolvidable</p>
    <a href="#rsvp" class="btn"><span>Confirmar asistencia</span></a>
  </div>
</section>

<section class="section countdown-section fade-in" id="detalles">
  <div class="container">
    <h2 class="section-title">¡Nos casamos!</h2>
    <div id="countdown" class="countdown" role="timer" aria-live="polite">
      <div class="countdown-item"><span id="dias">0</span><small>Días</small></div>
      <div class="countdown-item"><span id="horas">0</span><small>Horas</small></div>
      <div class="countdown-item"><span id="minutos">0</span><small>Minutos</small></div>
      <div class="countdown-item"><span id="segundos">0</span><small>Segundos</small></div>
    </div>
  </div>
</section>

<section class="section fade-in">
  <div class="container">
    <h2 class="section-title">Nuestra invitación</h2>
    <div class="grid-2">
      <div>
        @if ($isPreview)
            <p class="pases-info">
                Ejemplo: pase para {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
            </p>
        @else
            <p class="pases-info">
                Pase para {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
            </p>
        @endif
      </div>
      <div>
        <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800"
             alt="Fotografía de {{ $event->bride_name }} y {{ $event->groom_name }}"
             class="invitation-img"
             loading="lazy"
             width="600"
             height="400">
      </div>
    </div>
  </div>
</section>

<section class="section parents-section fade-in">
  <div class="container">
    <h2 class="section-title">Con la bendición de nuestros padres</h2>
    <div class="grid-2">
      <div class="card parent-card">
        <h3>Padres del novio</h3>
        <ul>
          @forelse($padres_novio as $padre)
            @if(!empty($padre)) <li>{{ $padre }}</li> @endif
          @empty
            <li>Familia del Novio</li>
          @endforelse
        </ul>
      </div>
      <div class="card parent-card">
        <h3>Padres de la novia</h3>
        <ul>
          @forelse($padres_novia as $padre)
            @if(!empty($padre)) <li>{{ $padre }}</li> @endif
          @empty
            <li>Familia de la Novia</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section fade-in">
  <div class="container">
    <h2 class="section-title">Nuestra historia</h2>
    <div class="carousel" role="region" aria-label="Carrusel de fotos de la pareja">
      <div class="carousel-inner" id="carouselInner">
        @forelse($event->eventPhotos as $photo)
          <div class="carousel-item">
            <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto de la galería" loading="lazy" width="800" height="550">
          </div>
        @empty
          <div class="carousel-item">
            <img src="{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800' }}"
                 alt="Foto de portada"
                 loading="lazy"
                 width="800"
                 height="550">
          </div>
        @endforelse
      </div>
      <button type="button" class="carousel-btn prev" aria-label="Foto anterior" id="btnPrev">‹</button>
      <button type="button" class="carousel-btn next" aria-label="Foto siguiente" id="btnNext">›</button>
    </div>
    <div class="frases" aria-live="polite" id="frasesContainer">
      <p id="fraseTexto"></p>
      <cite id="fraseAutor"></cite>
    </div>
  </div>
</section>

<section class="section save-date-section fade-in">
  <div class="container">
    <h2 class="section-title">Save the Date</h2>
    <p>
      {{ $fecha_completa }}
      @if($hora_evento_formato)
        a las {{ $hora_evento_formato }} horas
      @endif
    </p>
    <button type="button" class="btn" id="btnCalendario"><span>Guardar en mi calendario</span></button>
  </div>
</section>

<section class="section fade-in">
  <div class="container">
    <h2 class="section-title">Les esperamos en...</h2>
    <div class="grid-2">
      <div class="venue-info">
        <h3>{{ $event->reception_venue_name }}</h3>
        <p>{{ $event->reception_venue_address }}</p>
        @if($event->additional_info)
          <p style="opacity:0.8;font-style:italic;font-family:'Crimson Text',serif;font-size:1.2rem">
            {{ $event->additional_info }}
          </p>
        @endif
        <img src="https://images.unsplash.com/photo-1519167758481-83f29da8a1c4?w=800"
             alt="{{ $event->reception_venue_name }}"
             class="venue-img"
             loading="lazy"
             width="600"
             height="400">
      </div>
      <div class="map-container">
        <iframe src="{{ $event->reception_maps_link }}"
                title="Mapa de ubicación de {{ $event->reception_venue_name }}"
                loading="lazy"
                allowfullscreen></iframe>
      </div>
    </div>
  </div>
</section>

<section class="section dress-code-section fade-in">
  <div class="container">
    <h2 class="section-title">Código de vestimenta</h2>
    <div class="grid-2">
      <div class="card dress-card">
        <h3>{{ $event->dress_code }}</h3>
        <p>(Aquí irá una descripción más detallada del código de vestimenta que el usuario puede añadir)</p>
      </div>
      <div class="card dress-card">
        <p>(Aquí podría ir otra columna, ej. Hombres / Mujeres, tips, etc.)</p>
      </div>
    </div>
    <p class="dress-note">
      💍 Por favor evita el blanco absoluto, marfil y beige claro. ¡Queremos que brilles con tu mejor estilo y elegancia!
    </p>
  </div>
</section>

<section class="section timeline-section fade-in">
  <div class="container">
    <h2 class="section-title">Itinerario del evento</h2>
    <div class="timeline">
      @forelse($event->itineraryItems->sortBy('time') as $item)
        <div class="timeline-item">
          <h3>{{ date('H:i', strtotime($item->time)) }} – {{ $item->activity }}</h3>
        </div>
      @empty
        <div class="timeline-item">
          <h3>Itinerario pendiente</h3>
          <p>Los novios están finalizando los detalles del gran día.</p>
        </div>
      @endforelse
    </div>
  </div>
</section>

<section class="section rsvp-section fade-in" id="rsvp">
  <div class="container">
    <h2 class="section-title">Confirma tu asistencia</h2>

    <p style="font-size:1.3rem;">
      @if ($guest->status === 'confirmed')
          Querid@ <strong>{{ $guest->full_name }}</strong>, ¡ya recibimos tu confirmación! 🥳  
          Si necesitas cambiar algún detalle, puedes actualizar tu respuesta aquí mismo.
      @elseif ($guest->status === 'declined')
          Querid@ <strong>{{ $guest->full_name }}</strong>, sentimos que no podrás asistir 💚  
          Si cambias de opinión, puedes actualizar tu respuesta desde este mismo formulario.
      @else
          Querid@ <strong>{{ $guest->full_name }}</strong>, nos encantará saber si podrás acompañarnos
          en este día tan especial 💌
      @endif
    </p>

    @if (!$isPreview)
        @if (session('rsvp_status'))
            <div class="success-message">
                <h3>¡Gracias por tu respuesta! 💖</h3>
                <p>{{ session('rsvp_status') }}</p>
            </div>
        @elseif (!empty($alreadyConfirmed))
            <div class="success-message">
                <h3>Ya habías confirmado anteriormente</h3>
                <p>Puedes actualizar tu respuesta si lo necesitas y volver a enviar el formulario.</p>
            </div>
        @endif
    @endif

    <div class="grid-2" style="margin-top:40px;align-items:flex-start;">

      {{-- Columna izquierda: formulario --}}
      <div>
        @if ($isPreview)
          <div class="card" style="text-align:left;">
            <p class="text-sm text-slate-600 mb-4">
              Estás viendo una <strong>vista previa</strong>. Así se verá el formulario para tus invitados,
              pero aquí los campos están deshabilitados.
            </p>

            <div class="form-group">
              <label>¿Podrás asistir?</label>
              <div>
                <label class="opacity-60">
                  <input type="radio" disabled checked> Sí, con gusto asistiré 🎉
                </label><br>
                <label class="opacity-60">
                  <input type="radio" disabled> No podré asistir 😢
                </label>
              </div>
            </div>

            @if ($guest->max_companions > 0)
              <div class="form-group">
                <label>¿Cuántas personas te acompañan?</label>
                <p style="font-size:0.8rem;opacity:0.7;">
                  Puedes traer hasta {{ $guest->max_companions }} acompañante(s).
                </p>
                <input type="number" disabled value="1" style="width:80px;opacity:0.6;">
              </div>
            @endif

            <div class="form-group">
              <label>¿Tienes alguna restricción alimentaria?</label>
              <textarea rows="2" disabled
                        placeholder="Ejemplo: vegetariano, vegano, sin gluten, alergia a mariscos, etc."
                        style="opacity:0.6;"></textarea>
            </div>

            <div class="form-group">
              <label>Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}</label>
              <textarea rows="3" disabled
                        placeholder="Déjales un mensaje bonito a los novios 💌"
                        style="opacity:0.6;"></textarea>
            </div>

            <div style="text-align:center;margin-top:20px;">
              <button type="button" class="btn">
                <span>{{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}</span>
              </button>
            </div>
          </div>
        @else
          <form method="POST"
                action="{{ route('rsvp.submit', ['slug' => $event->custom_url_slug, 'token' => $guest->invitation_token]) }}"
                class="card"
                style="text-align:left;">
            @csrf

            <div class="form-group">
              <label>¿Podrás asistir?</label>
              <div>
                <label>
                  <input type="radio"
                         name="status"
                         value="confirmed"
                         @checked(old('status', $guest->status) === 'confirmed')>
                  Sí, con gusto asistiré 🎉
                </label><br>
                <label>
                  <input type="radio"
                         name="status"
                         value="declined"
                         @checked(old('status', $guest->status) === 'declined')>
                  No podré asistir 😢
                </label>
              </div>
              @error('status')
                <p style="margin-top:4px;font-size:0.8rem;color:#b91c1c;">{{ $message }}</p>
              @enderror
            </div>

            @if ($guest->max_companions > 0)
              <div class="form-group">
                <label>¿Cuántas personas te acompañan?</label>
                <p style="font-size:0.8rem;opacity:0.7;">
                  Puedes traer hasta {{ $guest->max_companions }} acompañante(s).
                </p>
                <input type="number"
                       name="confirmed_companions"
                       min="0"
                       max="{{ $guest->max_companions }}"
                       value="{{ old('confirmed_companions', $guest->confirmed_companions) }}"
                       style="width:80px;">
                @error('confirmed_companions')
                  <p style="margin-top:4px;font-size:0.8rem;color:#b91c1c;">{{ $message }}</p>
                @enderror
              </div>
            @endif

            <div class="form-group">
              <label>¿Tienes alguna restricción alimentaria?</label>
              <textarea name="dietary_restrictions"
                        rows="2"
                        placeholder="Ejemplo: vegetariano, vegano, sin gluten, alergia a mariscos, etc.">{{ old('dietary_restrictions', $guest->dietary_restrictions) }}</textarea>
              @error('dietary_restrictions')
                <p style="margin-top:4px;font-size:0.8rem;color:#b91c1c;">{{ $message }}</p>
              @enderror
            </div>

            <div class="form-group">
              <label>Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}</label>
              <textarea name="message_to_couple"
                        rows="3"
                        placeholder="Déjales un mensaje bonito a los novios 💌">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>
              @error('message_to_couple')
                <p style="margin-top:4px;font-size:0.8rem;color:#b91c1c;">{{ $message }}</p>
              @enderror
            </div>

            <div style="text-align:center;margin-top:20px;">
              <button type="submit" class="btn">
                <span>{{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}</span>
              </button>
            </div>
          </form>
        @endif
      </div>

      {{-- Columna derecha: resumen de invitación --}}
      <div class="card" style="text-align:left;">
        <h3 style="margin-bottom:15px;">
          {{ $isPreview ? 'Ejemplo de cómo verá su invitación' : 'Detalles de tu invitación' }}
        </h3>
        <p><strong>Invitad@:</strong> {{ $guest->full_name }}</p>
        <p><strong>Evento:</strong> Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</p>
        <p><strong>Fecha:</strong> {{ $fecha_completa }}@if($hora_evento_formato) a las {{ $hora_evento_formato }} hrs @endif</p>
        <p><strong>Lugar:</strong> {{ $event->reception_venue_name }}</p>
        <p><strong>Dirección:</strong> {{ $event->reception_venue_address }}</p>
        <p style="margin-top:15px;">
          <strong>Pases:</strong> {{ $totalPases }} persona{{ $totalPases > 1 ? 's' : '' }}
        </p>

        @if ($event->dress_code)
          <p style="margin-top:10px;">
            <strong>Código de vestimenta:</strong> {{ $event->dress_code }}
          </p>
        @endif

        @if ($event->reception_maps_link)
          <p style="margin-top:15px;">
            <a href="{{ $event->reception_maps_link }}" target="_blank">
              📍 Ver ubicación en mapas
            </a>
          </p>
        @endif
      </div>
    </div>
  </div>
</section>

<footer>
  <p>{{ $event->bride_name }} & {{ $event->groom_name }} • {{ $fecha_evento->format('Y') }}</p>
  <p style="margin-top:15px;font-size:1.15rem;opacity:0.95">
    Con amor y gratitud para nuestros seres queridos
  </p>
</footer>

<script>
const fechaEvento = {{ $fechaEventoJs }};
const novia       = @json($event->bride_name);
const novio       = @json($event->groom_name);
const lugarNombre = @json($event->reception_venue_name);
const youtubeUrl  = @json($event->music_url ?? '');
const frasesNovia = @json($frases_novia);
const frasesNovio = @json($frases_novio);
const totalFotos  = {{ $event->eventPhotos->count() > 0 ? $event->eventPhotos->count() : 1 }};

// === Obtener ID de YouTube (acepta URL o ID) ===
let youtubeVideoId = '';
if (youtubeUrl) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)?([^#&?]*).*/;
    const match = youtubeUrl.match(regExp);
    if (match && match[2].length === 11) {
        youtubeVideoId = match[2];
    } else {
        youtubeVideoId = youtubeUrl;
    }
}

// ===== MÚSICA DE FONDO CON YOUTUBE =====
let player;
let isMusicPlaying = false;

function onYouTubeIframeAPIReady() {
  if (!youtubeVideoId) return;
  player = new YT.Player('youtube-player', {
    videoId: youtubeVideoId,
    playerVars: {
      autoplay: 0,
      loop: 1,
      playlist: youtubeVideoId,
      controls: 0,
      showinfo: 0,
      modestbranding: 1,
      iv_load_policy: 3
    },
    events: {
      onReady: onPlayerReady
    }
  });
}

function onPlayerReady(event) {
  const musicControl = document.getElementById('musicControl');
  const musicIcon = document.getElementById('musicIcon');
  
  musicControl.addEventListener('click', () => {
    if (!player) return;
    if (isMusicPlaying) {
      player.pauseVideo();
      musicControl.classList.add('muted');
      musicIcon.innerHTML = '<path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>';
      isMusicPlaying = false;
    } else {
      player.playVideo();
      player.setVolume(30);
      musicControl.classList.remove('muted');
      musicIcon.innerHTML = '<path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>';
      isMusicPlaying = true;
    }
  });
}

// Cargar API de YouTube
(function(){
  const tag = document.createElement('script');
  tag.src = 'https://www.youtube.com/iframe_api';
  const firstScriptTag = document.getElementsByTagName('script')[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
})();

// Animación scroll
const observerOptions = {threshold: 0.1, rootMargin: '0px 0px -100px 0px'};
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if(entry.isIntersecting){
      entry.target.classList.add('visible');
    }
  });
}, observerOptions);
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

// Modal inicial
const modalInicial = document.getElementById('modalInicial');
const btnEntrar = document.getElementById('btnEntrar');
btnEntrar.addEventListener('click', () => {
  modalInicial.classList.remove('active');
  document.body.classList.remove('modal-open');
});
document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape' && modalInicial.classList.contains('active')){
    btnEntrar.click();
  }
});
setTimeout(() => btnEntrar.focus(), 100);

// ===== CONTADOR =====
function actualizarContador(){
  const ahora = Date.now();
  const diferencia = fechaEvento - ahora;

  if (isNaN(diferencia)) {
    console.error('fechaEvento inválida:', fechaEvento);
    return;
  }

  if(diferencia <= 0){
    document.getElementById('countdown').innerHTML =
      '<div class="countdown-item" style="min-width:auto;padding:40px 60px">' +
      '<span style="font-size:3rem">¡Llegó el gran día! 🎉</span></div>';
    return;
  }

  const dias = Math.floor(diferencia / (1000*60*60*24));
  const horas = Math.floor((diferencia % (1000*60*60*24)) / (1000*60*60));
  const minutos = Math.floor((diferencia % (1000*60*60)) / (1000*60));
  const segundos = Math.floor((diferencia % (1000*60)) / 1000);

  document.getElementById('dias').textContent = dias;
  document.getElementById('horas').textContent = horas;
  document.getElementById('minutos').textContent = minutos;
  document.getElementById('segundos').textContent = segundos;
}
actualizarContador();
setInterval(actualizarContador, 1000);

// ===== CARRUSEL + FRASES =====
let currentSlide = 0;
const carouselInner = document.getElementById('carouselInner');
const btnPrev = document.getElementById('btnPrev');
const btnNext = document.getElementById('btnNext');
const fraseTexto = document.getElementById('fraseTexto');
const fraseAutor = document.getElementById('fraseAutor');

function actualizarCarrusel(){
  if (!carouselInner) return;
  carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
  const esPar = currentSlide % 2 === 0;
  const frases = esPar ? frasesNovia : frasesNovio;
  const autor = esPar ? novia : novio;
  if (frases.length === 0) return;
  const indice = Math.floor(currentSlide / 2) % frases.length;
  fraseTexto.textContent = frases[indice];
  fraseAutor.textContent = `— ${autor}`;
}

if (btnPrev && btnNext && totalFotos > 0) {
  btnPrev.addEventListener('click', () => {
    currentSlide = currentSlide === 0 ? totalFotos - 1 : currentSlide - 1;
    actualizarCarrusel();
  });

  btnNext.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % totalFotos;
    actualizarCarrusel();
  });

  actualizarCarrusel();
  setInterval(() => {
    currentSlide = (currentSlide + 1) % totalFotos;
    actualizarCarrusel();
  }, 5000);
}

// ===== BOTÓN AÑADIR AL CALENDARIO (.ICS) =====
document.getElementById('btnCalendario').addEventListener('click', () => {
  function formatICSDate(date) {
    const pad = (num) => (num < 10 ? '0' + num : num);
    const year = date.getUTCFullYear();
    const month = pad(date.getUTCMonth() + 1);
    const day = pad(date.getUTCDate());
    const hours = pad(date.getUTCHours());
    const minutes = pad(date.getUTCMinutes());
    const seconds = pad(date.getUTCSeconds());
    return `${year}${month}${day}T${hours}${minutes}${seconds}Z`;
  }

  const fechaInicioJS = new Date(fechaEvento);
  const fechaFinJS = new Date(fechaEvento + (5 * 60 * 60 * 1000)); // +5h

  const dtstart = formatICSDate(fechaInicioJS);
  const dtend   = formatICSDate(fechaFinJS);
  
  const evento = [
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
  
  const blob = new Blob([evento], {type: 'text/calendar;charset=utf-8'});
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `boda-${novia.toLowerCase()}-${novio.toLowerCase()}.ics`;
  link.click();
});
</script>
</body>
</html>
