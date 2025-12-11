{{-- resources/views/guests/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Alertas --}}
        @if (session('status'))
            <div class="mb-2 p-3 rounded-md bg-green-50 text-green-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('import_summary'))
            @php $s = session('import_summary'); @endphp
            <div class="mb-2 p-3 rounded-md bg-blue-50 text-blue-800 text-sm">
                <p class="font-semibold mb-1">
                    Resumen de importación:
                </p>
                <ul class="list-disc list-inside space-y-0.5">
                    <li>Invitados agregados: <strong>{{ $s['created'] }}</strong></li>
                    <li>Filas omitidas: <strong>{{ $s['skipped'] }}</strong></li>
                </ul>

                @if (!empty($s['errors']))
                    <details class="mt-2">
                        <summary class="cursor-pointer text-xs underline">
                            Ver detalles de filas con problemas ({{ count($s['errors']) }})
                        </summary>
                        <ul class="mt-1 text-xs list-disc list-inside">
                            @foreach ($s['errors'] as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-2 p-3 rounded-md bg-red-50 text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario: agregar invitado (manual) --}}
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
                <h2 class="text-lg font-semibold">Agregar invitado</h2>
                <p class="text-xs text-gray-500">
                    Registra invitados individuales o usa la importación masiva más abajo.
                </p>
            </div>

            <form action="{{ route('guests.store') }}" method="POST"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nombre completo *</label>
                    <input type="text" name="full_name"
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500"
                           required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="phone"
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Acompañantes permitidos</label>
                    <input type="number" name="max_companions" min="0" max="10" value="0"
                           class="w-full border-gray-300 rounded-md text-sm focus:ring-purple-500 focus:border-purple-500">
                </div>

                <div class="md:col-span-4 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md text-sm font-semibold hover:bg-purple-700">
                        Guardar invitado
                    </button>
                </div>
            </form>
        </div>

        {{-- Bloque de importación masiva --}}
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">

            <h2 class="text-xl font-semibold text-gray-900 mb-1">
                🎉 Invita masivamente
            </h2>

            <p class="text-sm text-gray-600 mb-3">
                Sube un archivo <strong>CSV</strong> con tu lista de invitados y los agregaremos automáticamente.
            </p>

            <p class="text-sm text-gray-500 mb-4">
                Formato sugerido:
                <code class="text-xs bg-gray-100 px-2 py-1 rounded">
                    Nombre de invitado,Correo,Número de Telefono,Número de Acompañantes
                </code>
            </p>

            <div class="mb-4">
                <a href="{{ route('guests.template') }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-purple-700 bg-purple-50 hover:bg-purple-100">
                    ⬇️ Descargar plantilla CSV
                </a>
            </div>

            <form action="{{ route('guests.import') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="flex flex-col md:flex-row items-start md:items-center gap-4">
                @csrf

                <label for="file_csv"
                       class="inline-flex items-center justify-center px-4 py-2 border border-dashed border-purple-300 rounded-md text-sm text-purple-700 bg-purple-50 hover:bg-purple-100 cursor-pointer w-full sm:w-auto">
                    📎 Seleccionar archivo
                    <input id="file_csv" type="file" name="file" class="hidden" accept=".csv">
                </label>

                <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 w-full sm:w-auto">
                    📥 Importar invitados
                </button>

                <p class="text-xs text-gray-400 max-w-xs leading-tight">
                    * Solo se aceptan archivos CSV (puedes exportar tu Excel como CSV).
                </p>
            </form>

            @if ($errors->has('file'))
                <p class="mt-2 text-sm text-red-600">
                    {{ $errors->first('file') }}
                </p>
            @endif
        </div>

        {{-- Lista de invitados --}}
        <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                <h2 class="text-lg font-semibold">Lista de invitados</h2>
                <p class="text-xs text-gray-500">
                    Total: {{ $guests->total() }} invitado(s)
                </p>
            </div>

            {{-- VISTA MOBILE: cards --}}
            <div class="space-y-3 md:hidden">
                @forelse ($guests as $guest)
                    <div class="border border-gray-100 rounded-lg p-3 shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $guest->full_name }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-500" truncate block">
                                    @if($guest->email)
                                        {{ $guest->email }}
                                    @else
                                        <span class="italic text-gray-400">Sin correo</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    @if($guest->phone)
                                        📞 {{ $guest->phone }}
                                    @else
                                        <span class="italic text-gray-400">Sin teléfono</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-1">
                                @switch($guest->status)
                                    @case('confirmed')
                                        <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-[11px]">Confirmado</span>
                                        @break
                                    @case('declined')
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px]">No asiste</span>
                                        @break
                                    @default
                                        <span class="px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-[11px]">Pendiente</span>
                                @endswitch

                                <span class="text-[11px] text-gray-500">
                                    Acompañantes: {{ $guest->max_companions }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-2 flex justify-end">
                            <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('¿Seguro que quieres eliminar este invitado?')"
                                        class="text-[11px] text-red-600 hover:underline">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="py-4 text-center text-gray-500 text-sm">
                        Aún no has agregado invitados.
                    </p>
                @endforelse
            </div>

            {{-- VISTA DESKTOP: tabla --}}
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-xs uppercase text-gray-500">
                                <th class="py-2 pr-4">Nombre</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Teléfono</th>
                                <th class="py-2 pr-4 text-center">Acompañantes</th>
                                <th class="py-2 pr-4">Estado</th>
                                <th class="py-2 pr-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($guests as $guest)
                            <tr class="border-b last:border-0">
                                <td class="py-2 pr-4 whitespace-nowrap">{{ $guest->full_name }}</td>
                                <td class="py-2 pr-4 whitespace-nowrap">{{ $guest->email ?? '-' }}</td>
                                <td class="py-2 pr-4 whitespace-nowrap">{{ $guest->phone ?? '-' }}</td>
                                <td class="py-2 pr-4 text-center">{{ $guest->max_companions }}</td>
                                <td class="py-2 pr-4">
                                    @switch($guest->status)
                                        @case('confirmed')
                                            <span class="px-2 py-1 rounded-full bg-green-50 text-green-700 text-xs">Confirmado</span>
                                            @break
                                        @case('declined')
                                            <span class="px-2 py-1 rounded-full bg-red-50 text-red-700 text-xs">No asiste</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 rounded-full bg-yellow-50 text-yellow-700 text-xs">Pendiente</span>
                                    @endswitch
                                </td>
                                <td class="py-2 pr-4 text-right">
                                    <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('¿Seguro que quieres eliminar este invitado?')"
                                                class="text-xs text-red-600 hover:underline">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500 text-sm">
                                    Aún no has agregado invitados.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $guests->links() }}
            </div>
        </div>
    </div>
@endsection
