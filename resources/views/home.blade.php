@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- HEADER CON BIENVENIDA Y CUENTA REGRESIVA --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Resumen del Evento</h1>
            <p class="text-gray-500 mt-1">Aquí tienes lo más importante de tu boda.</p>
        </div>
        
        {{-- 🔥 Idea Extra: Widget de cuenta regresiva (Calculado en controlador o JS) --}}
        <div class="bg-indigo-50 px-4 py-2 rounded-lg border border-indigo-100 flex items-center text-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-semibold text-sm">Faltan {{ $daysLeft ?? '0' }} días</span>
        </div>
    </div>

    {{-- GRID DE TARJETAS RESPONSIVE --}}
    {{-- 🔥 grid-cols-2 en móvil ahorra mucho espacio vertical --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        
        {{-- Tarjeta 1: Total Invitaciones --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Invitaciones</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalInvitados }}</p>
                </div>
                <div class="p-2 bg-gray-50 rounded-lg text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-3 8H6a2 2 0 01-2-2V8m16 0v6a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">Enviadas o generadas</div>
        </div>

        {{-- Tarjeta 2: Confirmados --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-teal-600 uppercase">Confirmados</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-teal-600">{{ $confirmados }}</p>
                </div>
                <div class="p-2 bg-teal-50 rounded-lg text-teal-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            {{-- 🔥 Idea Extra: Barra de progreso --}}
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                @php $porcentaje = $totalInvitados > 0 ? ($confirmados / $totalInvitados) * 100 : 0; @endphp
                <div class="bg-teal-500 h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
            </div>
        </div>

        {{-- Tarjeta 3: Pendientes --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-orange-500 uppercase">Pendientes</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-orange-500">{{ $pendientes }}</p>
                </div>
                <div class="p-2 bg-orange-50 rounded-lg text-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-400">Por responder</div>
        </div>

        {{-- Tarjeta 4: No Asisten --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between h-full">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-red-500 uppercase">Rechazados</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-red-600">{{ $noAsisten }}</p>
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
             <div class="mt-2 text-xs text-gray-400">Libres para reasignar</div>
        </div>
        
        {{-- Tarjeta 5: Total Personas (Desglose) --}}
        {{-- 🔥 Idea Extra: Mostrar Adultos vs Niños aquí mismo --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between h-full col-span-2 md:col-span-1 lg:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-purple-600 uppercase">Asistentes</p>
                    <p class="mt-1 text-2xl sm:text-3xl font-bold text-purple-700">{{ $totalPersonas }}</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 🔥 Idea Extra: Sección de Alertas / Catering --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 flex items-start">
            <svg class="w-6 h-6 text-yellow-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h4 class="font-bold text-yellow-800 text-sm">Restricciones Alimentarias</h4>
                <p class="text-xs text-yellow-700 mt-1">
                    Hay <strong>{{ $alergicos ?? 0 }}</strong> personas con alergias o dieta vegana/vegetariana. 
                    <a href="#" class="underline font-semibold hover:text-yellow-900">Ver lista</a>.
                </p>
            </div>
        </div>
        
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center">
                 <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 <div>
                    <h4 class="font-bold text-blue-800 text-sm">Mesas Asignadas</h4>
                    <p class="text-xs text-blue-700 mt-1">Has asignado mesa al <strong>45%</strong> de confirmados.</p>
                 </div>
            </div>
            <a href="#" class="text-xs bg-white text-blue-600 px-3 py-1 rounded-full border border-blue-200 font-semibold shadow-sm">Gestionar</a>
        </div>
    </div>


    {{-- TABLA DE EVENTOS --}}
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="p-6 flex flex-col md:flex-row justify-between items-center border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-xl font-bold text-gray-900 mb-4 md:mb-0">
                Mis Eventos
            </h2>
            
            <a href="{{ route('evento.chooseType') }}"
            class="gradient-primary text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:shadow-lg transition transform hover:scale-105 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Crear Nuevo Evento
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Evento</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Link Invitados</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($events as $event)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">
                                        {{ substr($event->groom_name, 0, 1) }}&{{ substr($event->bride_name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->groom_name }} & {{ $event->bride_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $event->title ?? 'Boda' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($event->wedding_date)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($event->wedding_date)->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="#" target="_blank" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-100">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Ver link
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('evento.edit', $event) }}" class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('evento.destroy', $event) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-gray-400 hover:text-red-600 transition-colors"
                                                title="Eliminar"
                                                onclick="return confirm('¿Eliminar evento?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z"></path></svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay eventos</h3>
                                <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer evento.</p>
                                <div class="mt-6">
                                    <a href="{{ route('evento.chooseType') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                        Crear Evento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection