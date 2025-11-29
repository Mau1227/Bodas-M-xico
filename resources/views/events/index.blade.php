@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Mis eventos
    </h1>

    <a href="{{ route('evento.chooseType') }}"
       class="inline-flex items-center mb-4 rounded-full gradient-primary px-4 py-2 text-sm font-semibold text-white shadow-sm">
        + Crear nuevo evento
    </a>

    @forelse($events as $event)
        <div class="mb-3 bg-white rounded-xl border border-gray-100 p-4 flex justify-between items-center">
            <div>
                <p class="font-semibold text-gray-900">
                    {{ $event->display_title }}
                </p>
                @if($event->wedding_date)
                    <p class="text-sm text-gray-600">
                        {{ $event->wedding_date->format('d/m/Y') }}
                    </p>
                @endif
            </div>

            <a href="{{ route('evento.edit', $event) }}"
            class="text-sm text-purple-600 font-semibold hover:underline">
                Editar
            </a>
        </div>
    @empty
        <p class="text-gray-500 text-sm mt-4">
            Aún no tienes eventos. 
            <a href="{{ route('evento.chooseType') }}" class="text-purple-600 hover:underline">
                Crea tu primer evento.
            </a>
        </p>
    @endforelse
@endsection
