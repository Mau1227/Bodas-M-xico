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

            <div class="flex items-center space-x-3">
                {{-- 🔥 Nuevo: WhatsApp masivo --}}
                <button type="button" id="whatsapp-bulk-btn"
                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold
                            text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3h4l2 3h8a2 2 0 012 2v1M9 13h3m4 0h.01M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
                    </svg>
                    WhatsApp masivo
                </button>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50"
                        onclick="return confirm('¿Enviar invitaciones a los invitados seleccionados?');">
                    Enviar invitaciones seleccionadas
                </button>
            </div>
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
                                <input type="checkbox"
                                    name="guests[]"
                                    value="{{ $guest->id }}"
                                    class="guest-checkbox"
                                    data-name="{{ $guest->full_name }}"
                                    data-url="{{ $guest->invitation_url ?? '' }}">
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
                                <div class="flex flex-col space-y-2">

                                    {{-- 💌 Reenviar correo --}}
                                    @if($guest->email)
                                        <form method="POST" action="{{ route('guests.invitations.sendSingle', $guest) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-semibold
                                                        text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-100
                                                        transition-colors duration-150">
                                                {{-- icono sobre --}}
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-3 8H6a2 2 0 01-2-2V8m16 0v6a2 2 0 01-2 2z" />
                                                </svg>
                                                Reenviar correo
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-medium text-gray-400 bg-gray-50 border border-dashed border-gray-200">
                                            Sin correo
                                        </span>
                                    @endif

                                    {{-- 💬 Enviar por WhatsApp --}}
                                    @php
                                        $whatsappUrl = null;
                                        if ($guest->phone && $guest->invitation_url) {
                                            // Limpia el teléfono (por si tiene espacios o guiones)
                                            $cleanPhone = preg_replace('/\D+/', '', $guest->phone);
                                            // Asumo México: 52 + 1 + número de 10 dígitos (ajusta si manejas otro formato)
                                            $phoneWithCountry = '521' . $cleanPhone;
                                            $waMessage = urlencode("¡Hola {$guest->full_name}! 👋\nTe compartimos tu invitación a nuestra boda 💜\nConfirma aquí: {$guest->invitation_url}");
                                            $whatsappUrl = "https://wa.me/{$phoneWithCountry}?text={$waMessage}";
                                        }
                                    @endphp

                                    @if($whatsappUrl)
                                        <a href="{{ $whatsappUrl }}" target="_blank"
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-semibold
                                                text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100
                                                transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 3h4l2 3h8a2 2 0 012 2v1M9 13h3m4 0h.01M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
                                            </svg>
                                            WhatsApp
                                        </a>
                                    @else
                                        <span class="inline-flex px-3 py-1.5 rounded-md text-xs font-medium text-gray-400 bg-gray-50 border border-dashed border-gray-200">
                                            Sin WhatsApp
                                        </span>
                                    @endif

                                    {{-- 📋 Copiar link --}}
                                    @if($guest->invitation_url)
                                        <button type="button"
                                                onclick="copyInvitationUrl('{{ $guest->invitation_url }}')"
                                                class="inline-flex items-center justify-center px-3 py-1.5 rounded-md text-xs font-semibold
                                                    text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200
                                                    transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16h8a2 2 0 002-2V7a2 2 0 00-2-2H9L5 9v5a2 2 0 002 2z" />
                                            </svg>
                                            Copiar link
                                        </button>
                                    @endif

                                </div>
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
    const whatsappBulkBtn = document.getElementById('whatsapp-bulk-btn');

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

    window.copyInvitationUrl = function (url) {
        if (!navigator.clipboard) {
            const dummy = document.createElement('input');
            dummy.value = url;
            document.body.appendChild(dummy);
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
        } else {
            navigator.clipboard.writeText(url);
        }

        const toast = document.createElement('div');
        toast.textContent = 'Link copiado';
        toast.className = 'fixed bottom-5 right-5 bg-gray-900 text-white text-xs px-3 py-2 rounded-md shadow-lg opacity-90';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 1800);
    }

    // 💬 WhatsApp masivo con invitados seleccionados
    if (whatsappBulkBtn) {
        whatsappBulkBtn.addEventListener('click', () => {
            const selected = Array.from(document.querySelectorAll('.guest-checkbox:checked'));

            if (selected.length === 0) {
                alert('Selecciona al menos un invitado.');
                return;
            }

            const lines = [];
            selected.forEach(cb => {
                const name = cb.dataset.name || 'Invitado';
                const url = cb.dataset.url || '';
                if (url) {
                    lines.push(`${name}: ${url}`);
                }
            });

            if (lines.length === 0) {
                alert('Los invitados seleccionados no tienen URL de invitación.');
                return;
            }

            const text = lines.join('\n\n');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            }

            const encoded = encodeURIComponent(text);
            const waUrl = `https://wa.me/?text=${encoded}`;
            window.open(waUrl, '_blank');

            const toast = document.createElement('div');
            toast.textContent = 'Mensaje masivo copiado. Pégalo o envíalo en WhatsApp.';
            toast.className = 'fixed bottom-5 right-5 bg-emerald-700 text-white text-xs px-3 py-2 rounded-md shadow-lg opacity-90';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        });
    }
</script>
@endsection
