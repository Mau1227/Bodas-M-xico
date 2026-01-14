<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - FestLink</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f7ff;
            /* Quitamos el margin:0 porque Tailwind ya lo resetea, pero no estorba */
        }
        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-primary {
            background: linear-gradient(135deg, #7C3AED 0%, #2DD4BF 100%);
        }
        /* Opcional: Estilo para barra de scroll más bonita */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>

<body class="h-screen overflow-hidden bg-[#f8f7ff]">

    {{-- Backdrop para el sidebar móvil (z-20 para estar debajo del sidebar pero encima del contenido) --}}
    <div
        id="sidebar-backdrop"
        class="fixed inset-0 bg-black/50 z-20 hidden md:hidden transition-opacity opacity-0"
        onclick="toggleSidebar()"
    ></div>

    {{-- WRAPPER PRINCIPAL: Ocupa toda la pantalla --}}
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside
            id="sidebar"
            class="w-64 bg-white shadow-lg flex flex-col transition-transform duration-300 ease-in-out
                   fixed inset-y-0 left-0 z-30 transform -translate-x-full
                   md:relative md:translate-x-0 md:inset-auto md:shadow-none border-r border-gray-200"
        >
            {{-- Contenedor interno del Sidebar para scroll vertical si hay muchos items --}}
            <div class="flex flex-col flex-1 h-full overflow-y-auto p-4">
                
                <div class="mb-6 flex flex-col items-center border-b border-gray-100 pb-4">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-800 tracking-tight">FestLink</span>
                    </div>
                    @auth
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider text-center px-2 truncate w-full">
                            {{ Auth::user()->full_name }}
                        </span>
                    @endauth
                </div>
                
                <nav class="flex-1 space-y-1">
                    @php
                        function isActive($path) {
                            return Request::is($path) || Request::is($path . '/*');
                        }
                    @endphp

                    {{-- Links (sin cambios, solo indentación) --}}
                    <a href="{{ route('home') }}" 
                       @class([
                            'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-purple-50 text-purple-700' => isActive('home'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !isActive('home')
                       ])>
                        <svg class="@if(isActive('home')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('evento.index') }}"
                       @class([
                            'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-purple-50 text-purple-700' => isActive('dashboard/evento'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !isActive('dashboard/evento')
                       ])>
                        <svg class="@if(isActive('dashboard/evento')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Mis Eventos
                    </a>

                    <a href="{{ route('guests.index') }}"
                    @class([
                         'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                         'bg-purple-50 text-purple-700' => request()->routeIs('guests.index'),
                         'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.index'),
                    ])>
                     <svg class="@if(request()->routeIs('guests.index')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                               d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                     </svg>
                     Invitados
                    </a>

                    <a href="{{ route('guests.invitations') }}"
                    @class([
                            'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-purple-50 text-purple-700' => request()->routeIs('guests.invitations'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.invitations'),
                    ])>
                        <svg class="@if(request()->routeIs('guests.invitations')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Invitaciones
                    </a>

                    <a href="{{ route('messages.index') }}"
                    @class([
                            'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-purple-50 text-purple-700' => request()->routeIs('messages.index'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('messages.index'),
                    ])>
                        <svg class="@if(request()->routeIs('messages.index')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8h-4a3 3 0 01-3-3V5a3 3 0 013-3h10a3 3 0 013 3v7a3 3 0 01-3 3h-4l-4 4z"/>
                        </svg>
                        Mensajes
                    </a>

                    <a href="{{ route('stats.index') }}"
                    @class([
                            'group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-purple-50 text-purple-700' => request()->routeIs('stats.index'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('stats.index'),
                    ])>
                        <svg class="@if(request()->routeIs('stats.index')) text-purple-600 @else text-gray-400 group-hover:text-gray-500 @endif mr-3 h-5 w-5 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Estadísticas
                    </a>

                </nav>
            </div>
            
            {{-- Footer del Sidebar --}}
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="group flex w-full items-center rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar Sesión
                </a>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="bg-white shadow-sm border-b border-gray-200 z-10 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        
                        {{-- Botón hamburguesa sólo móvil --}}
                        <div class="flex items-center md:hidden">
                            <button type="button"
                                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    onclick="toggleSidebar()">
                                <span class="sr-only">Abrir menú</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="hidden md:block"></div>
                        
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="/" class="text-sm font-medium text-gray-500 hover:text-purple-600 transition">Ver sitio</a>
                            <a href="#" class="text-sm font-medium text-gray-500 hover:text-purple-600 transition">Soporte</a>
                        </div>

                        <div class="flex items-center space-x-3 ml-4">
                            @auth
                                <span class="hidden sm:inline text-sm text-gray-700 font-medium">Hola, {{ Str::before(Auth::user()->full_name, ' ') }}</span>
                            @endauth
                            <a href="#"
                               class="gradient-primary text-white px-4 py-2 rounded-full text-xs font-bold shadow hover:shadow-lg transition transform hover:-translate-y-0.5">
                                Mi Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#f8f7ff] p-4 md:p-8 pb-24">
                {{-- Contenedor max-width centrado para que no se estire infinito en pantallas ultra-wide --}}
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            // Lógica para togglear
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                // ABRIR
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                // Timeout pequeño para la transición de opacidad
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                }, 10);
            } else {
                // CERRAR
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('opacity-0');
                // Esperar a que acabe la transición para ocultar el div
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>