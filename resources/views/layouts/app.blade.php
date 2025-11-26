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
        /* Estilos base de tu index para consistencia */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f7ff; /* Tu color bg-mesh */
        }
        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-primary {
            background: linear-gradient(135deg, #7C3AED 0%, #2DD4BF 100%);
        }
    </style>
</head>
<body class="h-screen flex overflow-hidden">

    <aside class="hidden w-64 flex-col bg-white p-4 shadow-lg md:flex">
        <div class="flex flex-col flex-1">
            
            <div class="mb-4 flex flex-col items-center border-b pb-4">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">FestLink</span>
                </div>
                @auth
                <span class="text-sm text-gray-500 truncate max-w-full">
                    {{ Auth::user()->full_name }}
                </span>
                @endauth
            </div>
            
            <nav class="flex-1 space-y-2">
                
                @php
                    function isActive($path) {
                        return Request::is($path) || Request::is($path . '/*');
                    }
                @endphp

                <a href="{{ route('home') }}" 
                   @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => isActive('home'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !isActive('home')
                   ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard (Resumen)
                </a>

                <a href="{{ route('evento.edit', $event) }}"
                   @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => isActive('dashboard/evento'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !isActive('dashboard/evento')
                   ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Mi Evento
                </a>

                {{-- Invitados --}}
                <a href="{{ route('guests.index') }}"
                @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => request()->routeIs('guests.index'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.index'),
                ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    Invitados
                </a>

                {{-- Mandar invitaciones --}}
                <a href="{{ route('guests.invitations') }}"
                @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => request()->routeIs('guests.invitations'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.invitations'),
                ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Mandar invitaciones
                </a>

                {{-- Mensajes de Invitados --}}
                <a href="{{ route('messages.index') }}"
                @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => request()->routeIs('guests.messages'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.messages'),
                ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8h-4a3 3 0 01-3-3V5a3 3 0 013-3h10a3 3 0 013 3v7a3 3 0 01-3 3h-4l-4 4z"/>
                    </svg>
                    Mensajes de Invitados
                </a>

                {{--Estadísticas --}}
                <a href="{{ route('stats.index') }}"
                @class([
                        'group flex items-center rounded-md px-3 py-2 text-sm font-medium',
                        'bg-purple-100 text-purple-700' => request()->routeIs('guests.messages'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('guests.messages'),
                ])>
                    <svg class="mr-3 h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8h-4a3 3 0 01-3-3V5a3 3 0 013-3h10a3 3 0 013 3v7a3 3 0 01-3 3h-4l-4 4z"/>
                    </svg>
                    Estadísticas
                </a>

                
                </nav>
        </div>
        
        <div class="mt-4 border-t pt-4">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="group flex w-full items-center rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-700">
                <svg class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar Sesión
            </a>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div></div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="/" class="text-gray-700 hover:text-purple-600 transition">Ver mi sitio</a>
                        <a href="#" class="text-gray-700 hover:text-purple-600 transition">Plantillas</a>
                        <a href="#" class="text-gray-700 hover:text-purple-600 transition">FAQ</a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                        <span class="text-sm text-gray-700">¡Hola, {{ Auth::user()->full_name }}!</span>
                        @endauth
                        <a href="#" class="gradient-primary text-white px-4 py-2 rounded-full text-sm font-semibold hover:shadow-lg transition">
                            Mi Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            
            @yield('content')

        </main>
    </div>
    </body>
</html>