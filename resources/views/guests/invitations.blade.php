@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mandar invitaciones</h1>
            <p class="text-sm text-gray-500">
                Evento: <span class="font-semibold">{{ $event->title ?? 'Mi boda' }}</span>
            </p>
        </div>

        <a href="{{ route('guests.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">
            ← Volver a invitados
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('guests.invitations.sendBulk') }}">
        @csrf

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-3">
                <button type="button" id="select-all"
                        class="text-sm text-purple-600 hover:underline">
                    Seleccionar todos
                </button>
                <button type="button" id="unselect-all"
                        class="text-sm text-gray-500 hover:underline">
                    Quitar selección
                </button>
            </div>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
                    onclick="return confirm('¿Enviar invitaciones a los invitados seleccionados?');">
                Enviar invitaciones seleccionadas
            </button>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 w-10">
                            {{-- checkbox --}}
                        </th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Nombre</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Correo</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">URL de invitación</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Último envío</th>
                        <th class="px-3 py-2 text-left font-medium text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guests as $guest)
                        <tr class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-3 py-2 text-center">
                                @if($guest->email)
                                    <input type="checkbox" name="guests[]" value="{{ $guest->id }}" class="guest-checkbox">
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="font-medium text-gray-900">{{ $guest->full_name }}</div>
                            </td>
                            <td class="px-3 py-2">
                                @if($guest->email)
                                    <span class="text-gray-800">{{ $guest->email }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin correo</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($guest->invitation_url)
                                    <div class="flex items-center space-x-2">
                                        <input type="text"
                                               readonly
                                               class="w-full text-xs border-gray-200 rounded-md bg-gray-50 px-2 py-1 text-gray-700"
                                               value="{{ $guest->invitation_url }}">
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin URL</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-500">
                                @if($guest->invitation_sent_at)
                                    {{ $guest->invitation_sent_at->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-xs text-gray-400">Nunca enviado</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($guest->email)
                                    <form method="POST" action="{{ route('guests.invitations.sendSingle', $guest) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100"
                                                onclick="return confirm('¿Enviar / reenviar invitación a {{ $guest->full_name }}?');">
                                            Reenviar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin correo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500">
                                No hay invitados registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $guests->links() }}
        </div>
    </form>
</div>

<script>
    const checkboxes = document.querySelectorAll('.guest-checkbox');
    const selectAllBtn = document.getElementById('select-all');
    const unselectAllBtn = document.getElementById('unselect-all');

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = true);
        });
    }

    if (unselectAllBtn) {
        unselectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
        });
    }
</script>
@endsection
