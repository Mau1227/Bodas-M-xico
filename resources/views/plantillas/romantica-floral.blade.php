@php
  // --- Obtenemos y formateamos todas las fechas y horas con Carbon (el sistema de fechas de Laravel) ---
  $fecha_evento = \Carbon\Carbon::parse($event->wedding_date . ' ' . $event->ceremony_time);
  $fecha_completa = $fecha_evento->locale('es')->isoFormat('dddd, DD [de] MMMM [de] YYYY');
  $hora_evento_formato = $fecha_evento->format('H:i');
  $dia_numero = $fecha_evento->format('d');
  
  // --- Procesamos los campos de texto de "padres" (de TEXT a array) ---
  $padres_novia = $event->bride_parents ? explode("\n", trim($event->bride_parents)) : [];
  $padres_novio = $event->groom_parents ? explode("\n", trim($event->groom_parents)) : [];

  // --- Procesamos las frases (de TEXT a array) ---
  $frases_novia = $event->bride_story ? explode("\n", trim($event->bride_story)) : ['Contigo encontré mi lugar en el mundo.'];
  $frases_novio = $event->groom_story ? explode("\n", trim($event->groom_story)) : ['Eres mi presente y mi futuro.'];
@endphp

@php
    $isPreview = $isPreview ?? false;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</title>
<meta name="description" content="Te invitamos a celebrar nuestra boda...">
<meta property="og:title" content="Boda de {{ $event->bride_name }} & {{ $event->groom_name }}">
<meta property="og:description" content="{{ $event->welcome_message }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Lato:wght@300;400;700&display=swap');

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  /* === ¡Colores Dinámicos! === */
  --sage: {{ $event->primary_color }};
  --mint: {{ $event->secondary_color }};
  --gold:#A68B5B;
  --taupe:#B0A99F;
  --cream:#FAF9F6;
  --dark:#3A3A3A;
  --white:#FFFFFF;
}
html{scroll-behavior:smooth}
body{font-family:'Lato',sans-serif;color:var(--dark);background:var(--cream);line-height:1.8;overflow-x:hidden}
body.modal-open{overflow:hidden}
h1,h2,h3,h4{font-family:'Playfair Display',serif;font-weight:600;line-height:1.3;letter-spacing:1px}
img{max-width:100%;height:auto;display:block}
button{cursor:pointer;font-family:inherit;border:none}
a{color:var(--sage);text-decoration:none;transition:color 0.3s}
a:hover,a:focus{color:var(--gold)}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
.section{padding:120px 0;position:relative}
@media(max-width:768px){.section{padding:80px 0}}

/* Control de música */
.music-control{
  position:fixed;
  left:30px;
  bottom:30px;
  z-index:9998;
  width:60px;
  height:60px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--sage),var(--mint));
  border:3px solid var(--white);
  box-shadow:0 8px 30px rgba(125,155,123,0.4);
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
  transition:all 0.4s;
  animation:pulse 2s ease-in-out infinite;
}
.music-control:hover{
  transform:scale(1.1);
  box-shadow:0 12px 40px rgba(125,155,123,0.6);
}
.music-control svg{
  width:28px;
  height:28px;
  fill:var(--white);
}
.music-control.muted{
  background:linear-gradient(135deg,var(--taupe),#8B8B8B);
  animation:none;
}
#youtube-player{
  display:none;
}

/* Animaciones scroll */
.fade-in{opacity:0;transform:translateY(40px);transition:opacity 0.8s ease,transform 0.8s ease}
.fade-in.visible{opacity:1;transform:translateY(0)}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}

/* Modal inicial */
.modal{position:fixed;inset:0;background:rgba(58,58,58,0.95);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;visibility:hidden;transition:opacity 0.5s,visibility 0.5s;backdrop-filter:blur(5px)}
.modal.active{opacity:1;visibility:visible}
.modal-content{background:var(--white);padding:70px 50px;border-radius:25px;max-width:600px;width:90%;text-align:center;box-shadow:0 25px 80px rgba(0,0,0,0.4);position:relative;border:4px solid var(--gold);animation:modalEntry 0.6s ease}
@keyframes modalEntry{from{transform:scale(0.8);opacity:0}to{transform:scale(1);opacity:1}}
.modal-content svg{width:160px;height:160px;margin:0 auto 40px;animation:float 3s ease-in-out infinite}
.modal-content h2{font-size:2.6rem;margin-bottom:20px;color:var(--sage);font-style:italic;letter-spacing:2px}
.modal-content p{margin-bottom:40px;color:var(--dark);font-size:1.25rem;font-weight:300;line-height:1.8}

/* Botones */
.btn{display:inline-block;padding:18px 45px;background:linear-gradient(135deg,var(--sage),var(--mint));color:var(--white);border-radius:50px;font-size:1.05rem;font-weight:600;transition:all 0.4s;box-shadow:0 8px 25px rgba(125,155,123,0.3);position:relative;overflow:hidden;text-transform:uppercase;letter-spacing:2.5px}
.btn::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--mint),var(--sage));opacity:0;transition:opacity 0.4s}
.btn:hover::before,.btn:focus::before{opacity:1}
.btn span{position:relative;z-index:1}
.btn:hover,.btn:focus{transform:translateY(-3px);box-shadow:0 12px 35px rgba(125,155,123,0.4);text-decoration:none}
.btn:focus{outline:3px solid var(--gold);outline-offset:4px}
.btn-secondary{background:linear-gradient(135deg,var(--gold),#C9A66B);box-shadow:0 8px 25px rgba(166,139,91,0.3)}
.btn-secondary::before{background:linear-gradient(135deg,#C9A66B,var(--gold))}

/* Hero */
.hero{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  text-align:center;
  position:relative;
  /* Usamos la 'cover_photo_url' que subió el usuario */
  background: linear-gradient(rgba(125,155,123,0.25),rgba(168,202,173,0.35)),
              /* IMPORTANTE: 
                Asegúrate de haber corrido 'php artisan storage:link'
                para que esta imagen sea visible.
              */
              url('{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1600' }}') center/cover fixed;
  color:var(--white);
  overflow:hidden;
}.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at center,transparent 0%,rgba(58,58,58,0.5) 100%)}
.hero-content{position:relative;z-index:2;padding:50px 30px;animation:fadeInUp 1.2s ease}
@keyframes fadeInUp{from{opacity:0;transform:translateY(50px)}to{opacity:1;transform:translateY(0)}}
.hero h1{font-size:clamp(3.5rem,11vw,8rem);margin-bottom:30px;text-shadow:4px 4px 15px rgba(0,0,0,0.6);font-style:italic;font-weight:700;letter-spacing:6px;line-height:1.1}
.hero h1::after{content:'';display:block;width:250px;height:3px;background:linear-gradient(to right,transparent,var(--gold),transparent);margin:30px auto;box-shadow:0 0 15px var(--gold)}
.hero p{font-size:clamp(1.3rem,3.5vw,1.8rem);margin-bottom:50px;font-weight:300;letter-spacing:2.5px;animation:fadeInUp 1.4s ease;font-family:'Cormorant Garamond',serif;font-style:italic}
.hero .btn{animation:fadeInUp 1.6s ease;font-size:1.1rem;padding:20px 50px}
.hero-decoration{position:absolute;opacity:0.12;pointer-events:none}
.hero-leaf{width:120px;height:120px;fill:var(--white);animation:float 6s ease-in-out infinite}
.hero-leaf.left{top:20%;left:10%;animation-delay:-2s}
.hero-leaf.right{top:60%;right:15%;animation-delay:-4s}

/* Contador */
.countdown-section{background:linear-gradient(to bottom,var(--white),var(--cream));text-align:center}
.section-title{font-size:clamp(3rem,7vw,4.5rem);text-align:center;margin-bottom:70px;color:var(--sage);font-style:italic;position:relative;display:inline-block;left:50%;transform:translateX(-50%)}
.section-title::after{content:'';position:absolute;bottom:-20px;left:50%;transform:translateX(-50%);width:100px;height:4px;background:linear-gradient(to right,transparent,var(--gold),transparent)}
.countdown{display:flex;gap:35px;justify-content:center;flex-wrap:wrap;margin-top:50px}
.countdown-item{background:var(--white);padding:40px 35px;border-radius:25px;min-width:140px;box-shadow:0 12px 50px rgba(0,0,0,0.1);border:3px solid var(--mint);transition:all 0.4s}
.countdown-item:hover{transform:translateY(-10px) scale(1.08);box-shadow:0 18px 60px rgba(125,155,123,0.25);border-color:var(--sage)}
.countdown-item span{display:block;font-size:3.5rem;font-weight:700;color:var(--sage);font-family:'Playfair Display',serif}
.countdown-item small{font-size:0.95rem;text-transform:uppercase;letter-spacing:2.5px;color:var(--gold);font-weight:600;margin-top:8px;display:block}


.save-date-section .section-title,
.countdown-section .section-title{
  display:block;
  margin:0 auto 70px;
  left:auto;
  transform:none;
}


/* Grid */
.grid-2{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:70px;align-items:center}

/* Cards */
.card{background:var(--white);padding:60px 50px;border-radius:25px;box-shadow:0 15px 50px rgba(0,0,0,0.1);transition:all 0.5s;border:3px solid transparent;position:relative;overflow:hidden}
.card::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(168,202,173,0.15) 0%,transparent 70%);transform:scale(0);transition:transform 0.6s; pointer-events:none;z-index: 0;}
.card:hover::before{transform:scale(1)}
.card:hover{transform:translateY(-12px);border-color:var(--mint);box-shadow:0 25px 70px rgba(125,155,123,0.2)}

/* Invitación */
.invitation-text{font-size:1.55rem;line-height:2.1;color:var(--dark);font-weight:300;font-family:'Cormorant Garamond',serif;font-style:italic;text-align:justify}
.invitation-img{border-radius:25px;box-shadow:0 25px 70px rgba(0,0,0,0.18);border:6px solid var(--white);transition:transform 0.5s;width:100%;height:auto;object-fit:cover}
.invitation-img:hover{transform:scale(1.06) rotate(2deg)}
.pases-info{margin-top:35px;font-size:1.4rem;color:var(--gold);font-weight:600;padding:25px;background:linear-gradient(135deg,rgba(168,202,173,0.15),rgba(166,139,91,0.15));border-radius:18px;border-left:5px solid var(--gold);text-align:center;letter-spacing:1px}

/* Padres */
.parents-section{background:linear-gradient(to bottom,var(--cream),var(--white))}
.parent-card h3{color:var(--sage);margin-bottom:35px;text-align:center;font-size:2.3rem;font-style:italic;letter-spacing:1px}
.parent-card ul{list-style:none;text-align:center;font-size:1.35rem;line-height:2.8;font-family:'Cormorant Garamond',serif}
.parent-card ul li{position:relative;padding:12px 0;transition:color 0.3s;font-weight:400}
.parent-card ul li:hover{color:var(--sage)}
.parent-card ul li::before{content:'❦';position:absolute;left:50%;transform:translateX(-50%) translateY(-100%);opacity:0;transition:all 0.3s;color:var(--gold);font-size:1.8rem}
.parent-card ul li:hover::before{opacity:1;transform:translateX(-50%) translateY(-150%)}

/* Carrusel */
.carousel{position:relative;max-width:800px;margin:0 auto;border-radius:30px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,0.25);border:8px solid var(--white)}
.carousel-inner{display:flex;transition:transform 0.7s cubic-bezier(0.68,-0.55,0.265,1.55)}
.carousel-item{min-width:100%;position:relative}
.carousel-item img{width:100%;height:550px;object-fit:cover}
.carousel-btn{position:absolute;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.95);width:55px;height:55px;border-radius:50%;font-size:2.2rem;z-index:2;transition:all 0.3s;color:var(--sage);display:flex;align-items:center;justify-content:center;box-shadow:0 5px 20px rgba(0,0,0,0.25);font-weight:600}
.carousel-btn:hover{background:var(--white);transform:translateY(-50%) scale(1.2);box-shadow:0 10px 35px rgba(125,155,123,0.35)}
.carousel-btn.prev{left:25px}
.carousel-btn.next{right:25px}
.frases{text-align:center;margin-top:60px;padding:50px 40px;background:var(--white);border-radius:25px;box-shadow:0 12px 50px rgba(0,0,0,0.1);min-height:180px;border-left:6px solid var(--gold);position:relative}
.frases::before{content:'"';position:absolute;top:15px;left:35px;font-size:6rem;color:var(--mint);opacity:0.35;font-family:Georgia,serif;line-height:1}
.frases p{font-style:italic;font-size:1.5rem;color:var(--sage);font-family:'Cormorant Garamond',serif;font-weight:400;line-height:2;padding:0 40px}
.frases cite{display:block;margin-top:25px;font-size:1.25rem;color:var(--gold);font-style:normal;font-weight:600;letter-spacing:1.5px}

/* Save the date */
.save-date-section{background:linear-gradient(135deg,var(--sage),var(--mint));color:var(--white);text-align:center}
.save-date-section .section-title{color:var(--white)}
.save-date-section p{font-size:1.6rem;margin-bottom:35px;font-family:'Cormorant Garamond',serif;font-style:italic;letter-spacing:1px}
.calendar-icon{width:90px;height:90px;margin:0 auto 40px;animation:pulse 2s ease-in-out infinite}

/* Lugar */
.venue-info h3{color:var(--sage);font-size:2.8rem;margin-bottom:25px;font-style:italic;letter-spacing:1px}
.venue-info p{font-size:1.3rem;margin-bottom:25px;line-height:2}
.venue-img{margin-top:35px;border-radius:25px;box-shadow:0 25px 70px rgba(0,0,0,0.18);border:6px solid var(--white);width:100%;height:auto;object-fit:cover}
.map-container{border-radius:25px;overflow:hidden;box-shadow:0 25px 70px rgba(0,0,0,0.18);height:500px;border:6px solid var(--white)}
.map-container iframe{width:100%;height:100%;border:0}

/* Código vestimenta MEJORADO */
.dress-code-section{background:linear-gradient(to bottom,var(--white),var(--cream))}
.dress-card{text-align:center;display:flex;flex-direction:column;align-items:center}
.dress-image-container{
  width:100%;
  max-width:400px;
  height:450px;
  border-radius:25px;
  overflow:hidden;
  box-shadow:0 20px 60px rgba(0,0,0,0.15);
  border:6px solid var(--white);
  margin:0 auto 30px;
  transition:all 0.5s;
}
.dress-card:hover .dress-image-container{
  transform:scale(1.05);
  box-shadow:0 25px 70px rgba(125,155,123,0.25);
}
.dress-image-container img{
  width:100%;
  height:100%;
  object-fit:cover;
  transition:transform 0.5s;
}
.dress-card:hover .dress-image-container img{
  transform:scale(1.1);
}
.dress-card h3{color:var(--sage);margin-bottom:20px;font-size:2.3rem;font-style:italic;letter-spacing:1px}
.dress-card h4{color:var(--gold);margin-bottom:15px;font-size:1.5rem;font-weight:600;letter-spacing:1px}
.dress-card p{font-size:1.2rem;line-height:2;color:var(--dark);font-weight:300;max-width:450px;margin:0 auto}
.dress-note{text-align:center;margin-top:60px;font-size:1.15rem;opacity:0.8;font-style:italic;padding:25px 30px;background:rgba(166,139,91,0.12);border-radius:18px;max-width:700px;margin-left:auto;margin-right:auto;border-left:4px solid var(--gold)}

/* Mesa regalos */
.gifts-section{background:linear-gradient(135deg,var(--mint),var(--sage));color:var(--white);text-align:center}
.gifts-section .section-title{color:var(--white)}
.gift-img{max-width:500px;margin:0 auto 50px;border-radius:25px;box-shadow:0 30px 80px rgba(0,0,0,0.35);border:8px solid var(--white)}
.gifts-section p{font-size:1.45rem;margin-bottom:45px;font-family:'Cormorant Garamond',serif;font-style:italic;max-width:750px;margin-left:auto;margin-right:auto;line-height:2}

/* Timeline */
.timeline-section{background:var(--white)}
.timeline{position:relative;padding-left:90px;max-width:800px;margin:0 auto}
.timeline::before{content:'';position:absolute;left:35px;top:0;bottom:0;width:4px;background:linear-gradient(to bottom,var(--mint),var(--sage),var(--gold))}
.timeline-item{position:relative;margin-bottom:70px;padding:40px;background:var(--cream);border-radius:25px;box-shadow:0 12px 50px rgba(0,0,0,0.1);transition:all 0.4s;border:3px solid transparent}
.timeline-item:hover{transform:translateX(20px);border-color:var(--sage);box-shadow:0 18px 60px rgba(125,155,123,0.18)}
.timeline-item::before{content:'';position:absolute;left:-67px;top:42px;width:28px;height:28px;border-radius:50%;background:var(--gold);border:6px solid var(--white);box-shadow:0 0 0 4px var(--sage),0 6px 18px rgba(0,0,0,0.25);transition:all 0.4s}
.timeline-item:hover::before{transform:scale(1.35);box-shadow:0 0 0 6px var(--sage),0 10px 30px rgba(166,139,91,0.45)}
.timeline-item h3{color:var(--sage);margin-bottom:15px;font-size:1.8rem;font-style:italic;display:flex;align-items:center;gap:18px;letter-spacing:1px}
.timeline-item p{color:var(--dark);font-size:1.15rem;line-height:2;font-weight:300}
.timeline-icon{width:55px;height:55px;fill:var(--sage);transition:fill 0.3s;flex-shrink:0}
.timeline-item:hover .timeline-icon{fill:var(--gold)}

/* RSVP */
.rsvp-section{background:linear-gradient(to bottom,var(--cream),var(--white));text-align:center}
.rsvp-section p{font-size:1.45rem;margin-bottom:45px;font-family:'Cormorant Garamond',serif;font-style:italic;color:var(--sage);line-height:1.9}
.form-group{margin-bottom:30px;text-align:left}
.form-group label{display:block;margin-bottom:12px;font-weight:600;color:var(--sage);font-size:1.15rem;letter-spacing:0.5px}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:18px 22px;border:3px solid var(--mint);border-radius:15px;font-size:1.05rem;font-family:inherit;transition:all 0.3s;background:var(--white)}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{outline:none;border-color:var(--sage);box-shadow:0 6px 25px rgba(125,155,123,0.12)}
.form-group textarea{resize:vertical;min-height:140px}
.honeypot{position:absolute;left:-9999px;opacity:0}
.success-message{background:linear-gradient(135deg,rgba(168,202,173,0.25),rgba(125,155,123,0.25));padding:40px;border-radius:20px;border-left:6px solid var(--sage);margin-top:35px}
.success-message h3{color:var(--sage);margin-bottom:18px;font-size:2rem;letter-spacing:1px}

/* Footer */
footer{background:linear-gradient(135deg,var(--sage),var(--mint));color:var(--white);padding:70px 0;text-align:center}
footer p{font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-style:italic;letter-spacing:1.5px}

/* Decoraciones florales */
.floral-decoration{position:absolute;opacity:0.06;pointer-events:none;width:250px;height:250px}
.floral-left{top:10%;left:-60px;transform:rotate(-15deg)}
.floral-right{bottom:10%;right:-60px;transform:rotate(15deg)}

@media(max-width:768px){
  .hero h1{font-size:2.8rem;letter-spacing:3px}
  .section-title{font-size:2.3rem}
  .countdown{gap:18px}
  .countdown-item{min-width:90px;padding:25px 18px}
  .countdown-item span{font-size:2.3rem}
  .timeline{padding-left:60px}
  .timeline::before{left:18px}
  .timeline-item::before{left:-52px}
  .carousel-item img{height:400px}
  .grid-2{gap:50px}
  .dress-image-container{height:400px;max-width:100%}
  .music-control{left:20px;bottom:20px;width:55px;height:55px}
  .music-control svg{width:24px;height:24px}
  .invitation-text{font-size:1.35rem;text-align:left}
  .frases p{font-size:1.3rem;padding:0 20px}
}

@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
:focus-visible{outline:3px solid var(--gold);outline-offset:3px}
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

<div id="modalInicial" class="modal active" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
  <div class="modal-content">
    @php
        $hasResponded = in_array($guest->status, ['confirmed', 'declined'])
            || $guest->confirmed_companions > 0
            || !empty($guest->dietary_restrictions)
            || !empty($guest->message_to_couple);
    @endphp

    <p class="mb-8" style="font-size:1.3rem;">
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
    <button type="button" class="btn" id ="btnEntrar">
              <span>
                  {{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}
              </span>
    </button>
  </div>
</div>

<section class="hero" id="inicio">
  <!-- ... (Decoraciones) ... -->
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
    <h2 class="section-title">Nuestra Invitación</h2>
    <div class="grid-2">
      <div>
        <!-- Mensaje de Bienvenida Dinámico -->
        @php
            $totalPases = 1 + (int) ($guest->max_companions ?? 0);
        @endphp

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
        <img src="https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800" alt="Fotografía de {{ $event->bride_name }} y {{ $event->groom_name }}" class="invitation-img" loading="lazy" width="600" height="400">
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
          <!-- Bucle de los padres del novio (desde el campo de texto) -->
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
          <!-- Bucle de los padres de la novia (desde el campo de texto) -->
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
        <!-- Bucle de la Galería de Fotos -->
        @forelse($event->eventPhotos as $photo)
        <div class="carousel-item">
          <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Foto de la galería" loading="lazy" width="800" height="550">
        </div>
        @empty
        <!-- Si no hay fotos en la galería, muestra la de portada -->
        <div class="carousel-item">
          <img src="{{ $event->cover_photo_url ? asset('storage/' . $event->cover_photo_url) : 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800' }}" alt="Foto de portada" loading="lazy" width="800" height="550">
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
    <svg class="calendar-icon" viewBox="0 0 80 80" aria-hidden="true">
      <!-- (SVG del calendario) -->
      <text x="40" y="52" text-anchor="middle" font-size="24" font-weight="bold" fill="#7D9B7B" font-family="serif">{{ $dia_numero }}</text>
    </svg>
    <h2 class="section-title">Save the Date</h2>
    <p>
      {{ $fecha_completa }} a las {{ $hora_evento_formato }} horas
    </p>
    <button type="button" class="btn btn-secondary" id="btnCalendario"><span>Guardar en mi calendario</span></button>
  </div>
</section>

<section class="section fade-in">
  <div class="container">
    <h2 class="section-title">Les esperamos en...</h2>
    <div class="grid-2">
      <div class="venue-info">
        <h3>{{ $event->reception_venue_name }}</h3>
        <p>{{ $event->reception_venue_address }}</p>
        <p style="opacity:0.8;font-style:italic;font-family:'Cormorant Garamond',serif;font-size:1.2rem">
          {{ $event->additional_info }} <!-- Info Adicional -->
        </p>
        <img src="https://images.unsplash.com/photo-1519167758481-83f29da8a1c4?w=800" alt="{{ $event->reception_venue_name }}" class="venue-img" loading="lazy" width="600" height="400">
      </div>
      <div class="map-container">
        <!-- Link de Mapas Dinámico -->
        <iframe src="{{ $event->reception_maps_link }}" title="Mapa de ubicación de {{ $event->reception_venue_name }}" loading="lazy" allowfullscreen></iframe>
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
        <p>(Aquí podría ir otra columna, ej. Hombres)</p>
      </div>
    </div>
    <p class="dress-note">
      💍 Por favor evita el blanco absoluto, marfil y beige claro. ¡Queremos que brilles con tu mejor estilo y elegancia!
    </p>
  </div>
</section>

<!-- (Mesa de Regalos - Aún necesitamos construir esta lógica) -->
<!-- ... -->

<section class="section timeline-section fade-in">
  <div class="container">
    <h2 class="section-title">Itinerario del evento</h2>
    <div class="timeline">
      <!-- Bucle del Itinerario -->
      @forelse($event->itineraryItems->sortBy('time') as $item)
      <div class="timeline-item">
        <h3>
          <svg class="timeline-icon" viewBox="0 0 60 60" aria-hidden="true">
            <!-- (SVG Icon) -->
          </svg>
          {{ date('H:i', strtotime($item->time)) }} – {{ $item->activity }}
        </h3>
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

    @php
        $hasResponded = in_array($guest->status, ['confirmed', 'declined'])
            || $guest->confirmed_companions > 0
            || !empty($guest->dietary_restrictions)
            || !empty($guest->message_to_couple);
    @endphp

    <p class="mb-8" style="font-size:1.3rem;">
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


    {{-- Mensajes de estado (solo tienen sentido en modo real) --}}
    @if (!$isPreview)
        @if (session('rsvp_status'))
            <div class="success-message">
                <h3>¡Gracias por tu respuesta! 💖</h3>
                <p>{{ session('rsvp_status') }}</p>
            </div>
        @elseif ($alreadyConfirmed)
            <div class="success-message">
                <h3>Ya habías confirmado anteriormente</h3>
                <p>Puedes actualizar tu respuesta si lo necesitas y volver a enviar el formulario.</p>
            </div>
        @endif
    @endif

    <div class="grid-2" style="margin-top:40px;align-items:flex-start;">

      {{-- ===========================
           COLUMNA IZQUIERDA
         =========================== --}}
      <div>
        @if ($isPreview)
          {{-- VISTA PREVIA: formulario “de mentiras” --}}
          <div class="card" style="text-align:left;">
            <p class="text-sm text-slate-600 mb-4">
              Estás viendo una <strong>vista previa</strong>. Así se verá el formulario para tus invitados,
              pero aquí los campos están deshabilitados.
            </p>

            {{-- Asistencia (demo) --}}
            <div class="form-group">
              <label class="block text-sm font-medium text-slate-800 mb-1">
                ¿Podrás asistir?
              </label>

              <div class="space-y-2 text-sm">
                <label class="inline-flex items-center gap-2 opacity-60">
                  <input type="radio" disabled checked>
                  <span>Sí, con gusto asistiré 🎉</span>
                </label><br>

                <label class="inline-flex items-center gap-2 opacity-60">
                  <input type="radio" disabled>
                  <span>No podré asistir 😢</span>
                </label>
              </div>
            </div>

            {{-- Acompañantes (demo) --}}
            @if ($guest->max_companions > 0)
              <div class="form-group">
                <label class="block text-sm font-medium text-slate-800 mb-1">
                  ¿Cuántas personas te acompañan?
                </label>
                <p class="text-xs text-slate-500 mb-2">
                  Puedes traer hasta {{ $guest->max_companions }} acompañante(s).
                </p>

                <input type="number"
                       disabled
                       value="1"
                       class="w-24 border-slate-300 rounded-md text-sm opacity-60">
              </div>
            @endif

            {{-- Restricciones (demo) --}}
            <div class="form-group">
              <label class="block text-sm font-medium text-slate-800 mb-1">
                ¿Tienes alguna restricción alimentaria?
              </label>
              <textarea rows="2"
                        disabled
                        class="w-full border-slate-300 rounded-md text-sm opacity-60"
                        placeholder="Ejemplo: vegetariano, vegano, sin gluten, alergia a mariscos, etc."></textarea>
            </div>

            {{-- Mensaje (demo) --}}
            <div class="form-group">
              <label class="block text-sm font-medium text-slate-800 mb-1">
                Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}
              </label>
              <textarea rows="3"
                        disabled
                        class="w-full border-slate-300 rounded-md text-sm opacity-60"
                        placeholder="Déjales un mensaje bonito a los novios 💌"></textarea>
            </div>

            <div style="text-align:center;margin-top:20px;">
              <button type="submit" class="btn">
              <span>
                  {{ $hasResponded ? 'Actualizar respuesta' : 'Enviar respuesta' }}
              </span>
            </button>
            </div>
          </div>
        @else
          {{-- MODO REAL: formulario funcional --}}
          <form method="POST"
                action="{{ route('rsvp.submit', ['slug' => $event->custom_url_slug, 'token' => $guest->invitation_token]) }}"
                class="card"
                style="text-align:left;">
            @csrf

            {{-- Asistencia --}}
            <div class="form-group">
              <label class="block text-sm font-medium text-slate-800 mb-1">
                ¿Podrás asistir?
              </label>

              <div class="space-y-2 text-sm">
                <label class="inline-flex items-center gap-2">
                  <input type="radio"
                         name="status"
                         value="confirmed"
                         @checked(old('status', $guest->status) === 'confirmed')
                         class="rounded border-slate-300">
                  <span>Sí, con gusto asistiré 🎉</span>
                </label><br>

                <label class="inline-flex items-center gap-2">
                  <input type="radio"
                         name="status"
                         value="declined"
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
              <div class="form-group">
                <label class="block text-sm font-medium text-slate-800 mb-1">
                  ¿Cuántas personas te acompañan?
                </label>
                <p class="text-xs text-slate-500 mb-2">
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
            <div class="form-group">
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
            <div class="form-group">
              <label class="block text-sm font-medium text-slate-800 mb-1">
                Mensaje para {{ $event->bride_name }} & {{ $event->groom_name }}
              </label>
              <textarea name="message_to_couple"
                        rows="3"
                        class="w-full border-slate-300 rounded-md text-sm"
                        placeholder="Déjales un mensaje bonito a los novios 💌">{{ old('message_to_couple', $guest->message_to_couple) }}</textarea>

              @error('message_to_couple')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <div style="text-align:center;margin-top:20px;">
              <button type="submit" class="btn">
                <span>Enviar respuesta</span>
              </button>
            </div>
          </form>
        @endif
      </div>

      {{-- ===========================
           COLUMNA DERECHA (resumen)
         =========================== --}}
      <div class="card" style="text-align:left;">
        <h3 style="margin-bottom:15px;">
          {{ $isPreview ? 'Ejemplo de cómo verá su invitación' : 'Detalles de tu invitación' }}
        </h3>
        <p><strong>Invitad@:</strong> {{ $guest->full_name }}</p>
        <p><strong>Evento:</strong> Boda de {{ $event->bride_name }} & {{ $event->groom_name }}</p>
        <p><strong>Fecha:</strong> {{ $fecha_completa }} a las {{ $hora_evento_formato }} hrs</p>
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
  <p style="margin-top:15px;font-size:1.15rem;opacity:0.95">Con amor y gratitud para nuestros seres queridos</p>
</footer>

<script>
const fechaEvento = new Date('{{ $event->wedding_date }}T{{ $event->ceremony_time }}').getTime();
const novia = '{{ $event->bride_name }}';
const novio = '{{ $event->groom_name }}';
const lugarNombre = '{{ $event->reception_venue_name }}';
const youtubeVideoId = '{{ $event->music_url ?? '' }}'; // Usamos ?? '' por si está nulo
const frasesNovia = @json($frases_novia);
const frasesNovio = @json($frases_novio);
const totalFotos = {{ $event->eventPhotos->count() > 0 ? $event->eventPhotos->count() : ($event->cover_photo_url ? 1 : 0) }};



// ===== MÚSICA DE FONDO CON YOUTUBE =====
let player;
let isMusicPlaying = false;

function onYouTubeIframeAPIReady() {
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
const tag = document.createElement('script');
tag.src = 'https://www.youtube.com/iframe_api';
const firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

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

// Contador
function actualizarContador(){
  const ahora = new Date().getTime();
  const diferencia = fechaEvento - ahora;
  if(diferencia <= 0){
    document.getElementById('countdown').innerHTML = '<div class="countdown-item" style="min-width:auto;padding:40px 60px"><span style="font-size:3rem">¡Llegó el gran día! 🎉</span></div>';
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

// Carrusel
let currentSlide = 0;
const carouselInner = document.getElementById('carouselInner');
const btnPrev = document.getElementById('btnPrev');
const btnNext = document.getElementById('btnNext');
const fraseTexto = document.getElementById('fraseTexto');
const fraseAutor = document.getElementById('fraseAutor');

function actualizarCarrusel(){
  carouselInner.style.transform = `translateX(-${currentSlide * 100}%)`;
  const esPar = currentSlide % 2 === 0;
  const frases = esPar ? frasesNovia : frasesNovio;
  const autor = esPar ? novia : novio;
  const indice = Math.floor(currentSlide / 2) % frases.length;
  fraseTexto.textContent = frases[indice];
  fraseAutor.textContent = `— ${autor}`;
}

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

document.getElementById('btnCalendario').addEventListener('click', () => {
  
  // Helper para formatear fecha a YYYYMMDDTHHmmSSZ (formato UTC para .ics)
  function formatICSDate(date) {
    const pad = (num) => (num < 10 ? '0' + num : num);
    
    const year = date.getUTCFullYear();
    const month = pad(date.getUTCMonth() + 1); // Meses son 0-indexados
    const day = pad(date.getUTCDate());
    const hours = pad(date.getUTCHours());
    const minutes = pad(date.getUTCMinutes());
    const seconds = pad(date.getUTCSeconds());
    
    return `${year}${month}${day}T${hours}${minutes}${seconds}Z`;
  }

  // 'fechaEvento' es el timestamp de JS que definimos al inicio del script
  const fechaInicioJS = new Date(fechaEvento);
  
  // Asumimos una duración de 5 horas (5 * 60 * 60 * 1000 milisegundos)
  const fechaFinJS = new Date(fechaEvento + (5 * 60 * 60 * 1000)); 

  const dtstart = formatICSDate(fechaInicioJS);
  const dtend = formatICSDate(fechaFinJS);
  
  const evento = [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'PRODID:-//FestLink//ES',
    'BEGIN:VEVENT',
    `DTSTART:${dtstart}`,
    `DTEND:${dtend}`,
    // 'novia', 'novio' y 'lugarNombre' son las variables JS globales
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
  
  // Usamos las variables JS para el nombre del archivo
  link.download = `boda-${novia.toLowerCase()}-${novio.toLowerCase()}.ics`;
  link.click();
});

// RSVP
const modalRsvp = document.getElementById('modalRsvp');
const btnRsvp = document.getElementById('btnRsvp');
const btnCerrarRsvp = document.getElementById('btnCerrarRsvp');
const formRsvp = document.getElementById('formRsvp');
const successMsg = document.getElementById('successMsg');

btnRsvp.addEventListener('click', () => {
  modalRsvp.classList.add('active');
  document.body.classList.add('modal-open');
  setTimeout(() => document.getElementById('nombre').focus(), 100);
});

btnCerrarRsvp.addEventListener('click', () => {
  modalRsvp.classList.remove('active');
  document.body.classList.remove('modal-open');
  formRsvp.reset();
  successMsg.style.display = 'none';
});

document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape' && modalRsvp.classList.contains('active')){
    btnCerrarRsvp.click();
  }
});

formRsvp.addEventListener('submit', (e) => {
  e.preventDefault();
  if(formRsvp.website.value) return;
  
  const nombre = document.getElementById('nombre').value.trim();
  const contacto = document.getElementById('contacto').value.trim();
  const asistentes = document.getElementById('asistentes').value;
  const comentario = document.getElementById('comentario').value.trim();
  
  if(!nombre || !contacto){
    alert('Por favor completa todos los campos requeridos');
    return;
  }
  
  successMsg.innerHTML = `
    <h3>¡Confirmación recibida! 🎉</h3>
    <p><strong>Nombre:</strong> ${nombre}</p>
    <p><strong>Contacto:</strong> ${contacto}</p>
    <p><strong>Número de asistentes:</strong> ${asistentes}</p>
    ${comentario ? `<p><strong>Tu mensaje:</strong> ${comentario}</p>` : ''}
    <p style="margin-top:25px;font-style:italic;font-size:1.15rem">¡Muchas gracias por confirmar! Te esperamos con mucha ilusión en nuestra boda.</p>
  `;
  successMsg.style.display = 'block';
  formRsvp.style.display = 'none';
  
  setTimeout(() => {
    btnCerrarRsvp.click();
    formRsvp.style.display = 'block';
  }, 6000);
});
</script>
</body>
</html>