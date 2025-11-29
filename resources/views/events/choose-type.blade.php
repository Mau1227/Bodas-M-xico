@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        ¿Qué tipo de evento quieres crear?
    </h1>

    <p class="mb-6 text-gray-600">
        Selecciona el tipo de evento y en el siguiente paso podrás personalizar todos los detalles.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $types = [
                'wedding'      => ['label' => 'Boda', 'desc' => 'Novio & Novia, ceremonia y recepción.'],
                'birthday'     => ['label' => 'Cumpleaños', 'desc' => 'Fiestas de cumpleaños para cualquier edad.'],
                'xv'           => ['label' => 'XV Años', 'desc' => 'Eventos de XV años, misa y fiesta.'],
                'baby_shower'  => ['label' => 'Baby Shower', 'desc' => 'Bienvenida para el nuevo bebé.'],
                'corporate'    => ['label' => 'Evento Corporativo', 'desc' => 'Conferencias, lanzamientos, reuniones.'],
                'other'        => ['label' => 'Otro Evento', 'desc' => 'Cualquier otro tipo de celebración.'],
            ];
        @endphp

        @foreach($types as $key => $info)
            <a href="{{ route('evento.create', $key) }}"
               class="block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    {{ $info['label'] }}
                </h2>
                <p class="text-sm text-gray-600 mb-4">
                    {{ $info['desc'] }}
                </p>
                <span class="inline-flex items-center text-sm font-semibold text-purple-600">
                    Elegir este tipo
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </a>
        @endforeach
    </div>
@endsection
