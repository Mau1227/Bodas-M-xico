@extends('layouts.app')

@section('content')

    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Crear Nuevo Evento
    </h1>

    <form action="{{ route('evento.store') }}" method="POST">
        @csrf

        <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="groom_name" class="block text-sm font-medium text-gray-700">Nombre del Novio</label>
                    <input type="text" name="groom_name" id="groom_name" value="{{ old('groom_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('groom_name') ring-2 ring-red-500 @enderror">
                    
                    @error('groom_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="bride_name" class="block text-sm font-medium text-gray-700">Nombre de la Novia</label>
                    <input type="text" name="bride_name" id="bride_name" value="{{ old('bride_name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('bride_name') ring-2 ring-red-500 @enderror">
                    @error('bride_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="custom_url_slug" class="block text-sm font-medium text-gray-700">URL Personalizada</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-500 sm:text-sm">
                        festlink.com/evento/
                    </span>
                    <input type="text" name="custom_url_slug" id="custom_url_slug" value="{{ old('custom_url_slug') }}" required
                           placeholder="ej: juanylupe"
                           class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('custom_url_slug') ring-2 ring-red-500 @enderror">
                </div>
                @error('custom_url_slug')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-2 text-sm text-gray-500">Solo letras minúsculas, números y guiones (ej. 'boda-ana-y-carlos').</p>
                @enderror
            </div>
            
            <div>
                <label for="wedding_date" class="block text-sm font-medium text-gray-700">Fecha de la Boda</label>
                <input type="date" name="wedding_date" id="wedding_date" value="{{ old('wedding_date') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('wedding_date') ring-2 ring-red-500 @enderror">
                @error('wedding_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <fieldset class="border-t border-b border-gray-200 pt-6 pb-6">
                <legend class="text-lg font-medium text-gray-900">Ceremonia</legend>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="ceremony_time" class="block text-sm font-medium text-gray-700">Hora de la Ceremonia</label>
                        <input type="time" name="ceremony_time" id="ceremony_time" value="{{ old('ceremony_time') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_time') ring-2 ring-red-500 @enderror">
                        @error('ceremony_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ceremony_venue_name" class="block text-sm font-medium text-gray-700">Lugar de la Ceremonia (Nombre)</label>
                        <input type="text" name="ceremony_venue_name" id="ceremony_venue_name" value="{{ old('ceremony_venue_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_venue_name') ring-2 ring-red-500 @enderror">
                        @error('ceremony_venue_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ceremony_venue_address" class="block text-sm font-medium text-gray-700">Dirección de la Ceremonia</label>
                        <input type="text" name="ceremony_venue_address" id="ceremony_venue_address" value="{{ old('ceremony_venue_address') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_venue_address') ring-2 ring-red-500 @enderror">
                        @error('ceremony_venue_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ceremony_maps_link" class="block text-sm font-medium text-gray-700">Link de Google Maps (Ceremonia)</label>
                        <input type="url" name="ceremony_maps_link" id="ceremony_maps_link" value="{{ old('ceremony_maps_link') }}"
                               placeholder="https://goo.gl/maps/..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('ceremony_maps_link') ring-2 ring-red-500 @enderror">
                        @error('ceremony_maps_link')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend class="text-lg font-medium text-gray-900">Recepción</legend>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="reception_time" class="block text-sm font-medium text-gray-700">Hora de la Recepción</label>
                        <input type="time" name="reception_time" id="reception_time" value="{{ old('reception_time') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_time') ring-2 ring-red-500 @enderror">
                        @error('reception_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="reception_venue_name" class="block text-sm font-medium text-gray-700">Lugar de la Recepción (Nombre)</label>
                        <input type="text" name="reception_venue_name" id="reception_venue_name" value="{{ old('reception_venue_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_venue_name') ring-2 ring-red-500 @enderror">
                        @error('reception_venue_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="reception_venue_address" class="block text-sm font-medium text-gray-700">Dirección de la Recepción</label>
                        <input type="text" name="reception_venue_address" id="reception_venue_address" value="{{ old('reception_venue_address') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_venue_address') ring-2 ring-red-500 @enderror">
                        @error('reception_venue_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="reception_maps_link" class="block text-sm font-medium text-gray-700">Link de Google Maps (Recepción)</label>
                        <input type="url" name="reception_maps_link" id="reception_maps_link" value="{{ old('reception_maps_link') }}"
                               placeholder="https://goo.gl/maps/..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('reception_maps_link') ring-2 ring-red-500 @enderror">
                        @error('reception_maps_link')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </fieldset>

            <fieldset class="border-t border-gray-200 pt-6 space-y-6">
                </fieldset>

            <div class="pt-6 text-right">
                <a href="{{ route('home') }}" class="mr-3 rounded-md border border-gray-300 bg-white py-2 px-6 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-full gradient-primary px-6 py-2 text-sm font-semibold text-white shadow-sm hover:shadow-lg transition transform hover:scale-105">
                    Guardar Evento
                </button>
            </div>
        </div>
    </form>

@endsection