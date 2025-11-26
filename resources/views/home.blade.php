@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Dashboard (Resumen)
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Total Invitados</h3>
            <p class="mt-1 text-4xl font-bold text-gray-900">{{ $totalInvitados }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Confirmados</h3>
            <p class="mt-1 text-4xl font-bold text-teal-600">{{ $confirmados }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Pendientes</h3>
            <p class="mt-1 text-4xl font-bold text-orange-500">{{ $pendientes }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">No Asisten</h3>
            <p class="mt-1 text-4xl font-bold text-red-600">{{ $noAsisten }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
            <h3 class="text-sm font-medium text-gray-500">Total Personas</h3>
            <p class="mt-1 text-4xl font-bold text-purple-700">{{ $totalPersonas }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 md:p-8 rounded-2xl shadow-md border border-gray-100">

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">
                Mis Eventos
            </h2>
            
            <a href="{{ route('evento.create') }}" 
               class="gradient-primary text-white px-6 py-2 rounded-full font-semibold hover:shadow-lg transition transform hover:scale-105 inline-block">
                Crear Nuevo Evento
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres del Evento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL Pública</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    @forelse ($events as $event)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $event->groom_name }} & {{ $event->bride_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($event->wedding_date)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="#" class="text-sm text-purple-600 hover:text-purple-900">
                                    .../{{ $event->custom_url_slug }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    
                                <a href="{{ route('evento.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </a>

                                <form action="{{ route('evento.destroy', $event) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <button type="submit" 
                                            class="ml-4 text-red-600 hover:text-red-900"
                                            style="background:none; border:none; padding:0; cursor:pointer;"
                                            onclick="return confirm('¿Estás seguro de que quieres eliminar este evento? Esta acción no se puede deshacer.')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Aún no tienes eventos. ¡Crea uno para empezar!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    @endsection