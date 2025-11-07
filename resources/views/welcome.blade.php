{{--
|--------------------------------------------------------------------------
| resources/views/welcome.blade.php (VERSIÓN CORREGIDA)
|--------------------------------------------------------------------------
--}}

@extends('layouts.public')

@section('content')

    <section class="pt-32 pb-20 px-4 gradient-hero relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-white fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                        El enlace de tus momentos
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-purple-100 font-light">
                        Crea, comparte y celebra con un solo link. Invitaciones digitales hermosas para bodas, XV años, cumpleaños y más.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button class="bg-white text-purple-600 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition transform hover:scale-105 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Crea tu invitación ahora
                        </button>
                        <button class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-white hover:text-purple-600 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ver demo
                        </button>
                    </div>
                    <div class="mt-6 flex items-center gap-6 text-purple-100 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Gratis para empezar
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sin tarjeta de crédito
                        </div>
                    </div>
                </div>
                
                <div class="relative float-animation">
                    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform rotate-2">
                        <img src="https://images.unsplash.com/photo-1519741497674-611481863552?w=800&h=600&fit=crop" 
                             alt="Celebración de evento" 
                             class="w-full h-64 object-cover"
                        />
                        <div class="p-6 space-y-4">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-gray-900">Ana & Carlos</h3>
                                <p class="text-purple-600 font-medium">24 de Diciembre, 2025</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">Jardín Esmeralda</p>
                                        <p class="text-sm text-gray-600">Mérida, Yucatán</p>
                                    </div>
                                </div>
                                <button class="w-full bg-gradient-to-r from-purple-600 to-teal-500 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                                    Confirmar asistencia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="caracteristicas" class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Todo lo que necesitas en <span class="text-gradient">un solo link</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Conecta con tus invitados de forma moderna, elegante y sin complicaciones
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-purple-100/50 shadow-sm">
                    <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Plantillas hermosas</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Diseños profesionales y personalizables para bodas, XV años, bautizos, cumpleaños y eventos empresariales.
                    </p>
                </div>

                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-teal-100/50 shadow-sm">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">RSVP inteligente</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Gestiona confirmaciones en tiempo real. Controla lista de invitados, menús especiales y más desde tu celular.
                    </p>
                </div>

                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-orange-100/50 shadow-sm">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Comparte fácilmente</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Un link único para WhatsApp, redes sociales o email. Tus invitados solo necesitan un clic para verlo todo.
                    </p>
                </div>

                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-purple-100/50 shadow-sm">
                    <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Galería de fotos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Comparte los mejores momentos. Tus invitados pueden subir sus propias fotos del evento.
                    </p>
                </div>

                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-teal-100/50 shadow-sm">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Música ambiente</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Agrega tu playlist favorita para que la invitación transmita la emoción de tu celebración.
                    </p>
                </div>

                <div class="card-hover bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-orange-100/50 shadow-sm">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Estadísticas en vivo</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Visualiza en tiempo real quiénes han confirmado, cuántos asistirán y gestiona tu evento con datos precisos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="como-funciona" class="py-20 px-4 bg-mesh">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Crea tu invitación en <span class="text-gradient">3 simples pasos</span>
                </h2>
                <p class="text-xl text-gray-600">
                    De la idea a compartir, en minutos
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <div class="hidden md:block absolute top-1/4 left-1/4 right-1/4 h-1 bg-gradient-to-r from-purple-300 to-teal-300"></div>
                
                <div class="relative text-center">
                    <div class="w-20 h-20 gradient-primary rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        1
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Elige tu plantilla</h3>
                        <p class="text-gray-600">
                            Selecciona el diseño perfecto para tu evento. Bodas, XV años, cumpleaños o eventos corporativos.
                        </p>
                    </div>
                </div>

                <div class="relative text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        2
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Personaliza</h3>
                        <p class="text-gray-600">
                            Agrega tus fotos, textos, colores, ubicación, itinerario y toda la información de tu evento.
                        </p>
                    </div>
                </div>

                <div class="relative text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-lg">
                        3
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition">
                        <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Comparte</h3>
                        <p class="text-gray-600">
                            Obtén tu link único y compártelo por WhatsApp, redes sociales o email. ¡Listo para recibir confirmaciones!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="plantillas" class="py-20 px-4 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Plantillas para cada <span class="text-gradient">celebración</span>
                </h2>
                <p class="text-xl text-gray-600">
                    Diseños profesionales listos para personalizar
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="group card-hover bg-white rounded-2xl overflow-hidden shadow-lg">
                    <div class="h-64 overflow-hidden image-overlay">
                        <img src="https://images.unsplash.com/photo-1606800052052-a08af7148866?w=800&h=600&fit=crop" 
                             alt="Boda elegante" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <h4 class="text-xl font-bold text-gray-900">Bodas</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Diseños sofisticados para el día más importante de tu vida.</p>
                        <button class="text-purple-600 font-semibold hover:text-purple-700 transition flex items-center gap-2">
                            Ver plantillas 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="group card-hover bg-white rounded-2xl overflow-hidden shadow-lg">
                    <div class="h-64 overflow-hidden image-overlay">
                        <img src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=800&h=600&fit=crop" 
                             alt="XV años celebración" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                            <h4 class="text-xl font-bold text-gray-900">XV Años</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Invitaciones modernas para una celebración inolvidable.</p>
                        <button class="text-purple-600 font-semibold hover:text-purple-700 transition flex items-center gap-2">
                            Ver plantillas 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="group card-hover bg-white rounded-2xl overflow-hidden shadow-lg">
                    <div class="h-64 overflow-hidden image-overlay">
                        <img src="https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=800&h=600&fit=crop" 
                             alt="Cumpleaños festivo" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                            <h4 class="text-xl font-bold text-gray-900">Cumpleaños</h4>
                        </div>
                        <p class="text-gray-600 mb-4">Desde fiestas infantiles hasta celebraciones adultas.</p>
                        <button class="text-purple-600 font-semibold hover:text-purple-700 transition flex items-center gap-2">
                            Ver plantillas 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <button class="gradient-primary text-white px-10 py-4 rounded-full font-bold text-lg hover:shadow-xl transition transform hover:scale-105">
                    Explorar todas las plantillas
                </button>
            </div>
        </div>
    </section>

    <section class="py-20 px-4 gradient-hero relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-20 w-80 h-80 bg-white rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                ¿Listo para crear tu invitación?
            </h2>
            <p class="text-xl text-purple-100 mb-10 max-w-2xl mx-auto">
                Únete a miles de personas que ya están celebrando de forma moderna. Es gratis, fácil y no necesitas tarjeta de crédito.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="bg-white text-purple-600 px-10 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition transform hover:scale-105 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Crear mi invitación gratis
                </button>
                <button class="border-2 border-white text-white px-10 py-4 rounded-full font-semibold text-lg hover:bg-white hover:text-purple-600 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Hablar con ventas
                </button>
            </div>
        </div>
    </section>

@endsection