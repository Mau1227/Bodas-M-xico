{{--
|--------------------------------------------------------------------------
| resources/views/layouts/public.blade.php
|--------------------------------------------------------------------------
| Esta es tu plantilla maestra pública.
| Contiene el <head>, <nav> (header), y <footer>.
| El contenido de cada página se inyectará donde dice @yield('content').
|
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FestLink - El enlace de tus momentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
        }
        /* ... (Aquí van TODOS tus estilos de la paleta, gradientes, animaciones, etc.) ... */
        .gradient-primary {
            background: linear-gradient(135deg, #7C3AED 0%, #2DD4BF 100%);
        }
        .gradient-hero {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 30%, #2DD4BF 100%);
        }
        /* ... etc ... */
        .bg-mesh {
            background-color: #F8F7FF;
            background-image: 
                radial-gradient(at 0% 0%, rgba(124, 58, 237, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(45, 212, 191, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 140, 130, 0.05) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-mesh">
    
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">FestLink</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#caracteristicas" class="text-gray-700 hover:text-purple-600 transition">Características</a>
                    <a href="#como-funciona" class="text-gray-700 hover:text-purple-600 transition">Cómo funciona</a>
                    <a href="#plantillas" class="text-gray-700 hover:text-purple-600 transition">Plantillas</a>
                    <a href="#precios" class="text-gray-700 hover:text-purple-600 transition">Precios</a>
                </div>
                <div class="flex items-center space-x-4">

                    @guest
                        {{-- MUESTRA ESTO SI EL USUARIO NO ESTÁ LOGUEADO --}}

                        <a href="{{ route('login') }}" class="hidden md:inline-block text-purple-600 hover:text-purple-700 font-medium transition">
                            Iniciar sesión
                        </a>
                        
                        <a href="{{ route('register') }}" class="gradient-primary text-white px-6 py-2 rounded-full font-semibold hover:shadow-lg transition transform hover:scale-105">
                            Crear gratis
                        </a>
                    @endguest

                    @auth
                        {{-- MUESTRA ESTO SI EL USUARIO SÍ INICIÓ SESIÓN --}}

                        <span class="hidden md:inline-block text-gray-700">
                            Hola, {{ Auth::user()->full_name }}
                        </span>

                        <a href="{{ route('home') }}" class="gradient-primary text-white px-6 py-2 rounded-full font-semibold hover:shadow-lg transition transform hover:scale-105">
                            Mi Panel
                        </a>

                        <a href="{{ route('logout') }}"
                        class="hidden md:inline-block text-purple-600 hover:text-purple-700 font-medium transition"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                            Cerrar sesión
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endauth

                </div>
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 text-white py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold">FestLink</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        El enlace de tus momentos. Crea y comparte invitaciones digitales hermosas.
                    </p>
                    <div class="flex space-x-4">
                        </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Producto</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Características</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Plantillas</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Precios</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Recursos</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Centro de ayuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Guías</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Contacto</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Empresa</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Acerca de</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Términos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Privacidad</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Ruz Visual</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8">
                </div>
        </div>
    </footer>
    <script>
        // Smooth scroll para los enlaces de navegación
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animación de entrada para elementos al hacer scroll
        // ... (Tu código de Intersection Observer) ...
    </script>
    </body>
</html>