@extends('layouts.app')

@section('content')
{{-- 1. Agregado px-4 para margen en móviles --}}
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- 2. Header Responsivo: flex-col en móvil, flex-row en escritorio --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mandar invitaciones</h1>
            <p class="text-sm text-gray-500 mt-1">
                Evento: <span class="font-semibold">{{ $event->title ?? 'Mi boda' }}</span>
            </p>
        </div>

        <a href="{{ route('guests.index') }}"
           class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 w-full md:w-auto">
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

        {{-- 3. Barra de Herramientas Responsiva --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
            
            {{-- Botones de selección --}}
            <div class="flex items-center justify-between lg:justify-start space-x-4">
                <button type="button" id="select-all"
                        class="text-sm text-purple-600 hover:text-purple-800 font-medium hover:underline">
                    Seleccionar todos
                </button>
                <button type="button" id="unselect-all"
                        class="text-sm text-gray-500 hover:text-gray-700 font-medium hover:underline">
                    Quitar selección
                </button>
            </div>

            {{-- Botones de Acción Masiva (Apilados en móvil, fila en PC) --}}
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- WhatsApp masivo --}}
                <button type="button" id="whatsapp-bulk-btn"
                        class="inline-flex justify-center items-center px-4 py-2 rounded-md text-sm font-semibold
                            text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3h4l2 3h8a2 2 0 012 2v1M9 13h3m4 0h.01M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
                    </svg>
                    WhatsApp masivo
                </button>

                <button type="submit"
                        class="inline-flex justify-center items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 disabled:opacity-50 w-full sm:w-auto shadow-sm"
                        onclick="return confirm('¿Enviar invitaciones a los invitados seleccionados?');">
                    Enviar seleccionadas
                </button>
            </div>
        </div>

        {{-- 4. Contenedor de Tabla con Scroll Horizontal --}}
        <div class="overflow-hidden bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm whitespace-nowrap">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">
                                {{-- Icono de check --}}
                                <svg class="w-4 h-4 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Nombre</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Correo</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">URL</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase tracking-wider text-xs">Enviado</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700 uppercase tracking-wider text-xs">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($guests as $guest)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-4 text-center align-top sm:align-middle">
                                    <input type="checkbox"
                                        name="guests[]"
                                        value="{{ $guest->id }}"
                                        class="guest-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        data-name="{{ $guest->full_name }}"
                                        data-url="{{ $guest->invitation_url ?? '' }}">
                                </td>
                                <td class="px-4 py-4 align-top sm:align-middle">
                                    <div class="font-medium text-gray-900">{{ $guest->full_name }}</div>
                                    {{-- Móvil: Mostrar teléfono si existe para referencia rápida --}}
                                    @if($guest->phone)
                                        <div class="text-xs text-gray-400 mt-0.5 md:hidden">{{ $guest->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top sm:align-middle">
                                    @if($guest->email)
                                        <span class="text-gray-600">{{ $guest->email }}</span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin correo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top sm:align-middle">
                                    @if($guest->invitation_url)
                                        <div class="max-w-[150px]">
                                            <input type="text"
                                                   readonly
                                                   class="w-full text-xs border-gray-200 rounded bg-gray-50 px-2 py-1 text-gray-500 focus:ring-0 focus:border-gray-300 truncate"
                                                   value="{{ $guest->invitation_url }}">
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">--</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top sm:align-middle text-gray-500">
                                    @if($guest->invitation_sent_at)
                                        {{ $guest->invitation_sent_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top sm:align-middle">
                                    <div class="flex flex-col space-y-2 min-w-[140px]">

                                        {{-- 💌 Reenviar correo --}}
                                        @if($guest->email)
                                            <form method="POST" action="{{ route('guests.invitations.sendSingle', $guest) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded text-xs font-semibold
                                                            text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-100
                                                            transition-colors duration-150">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-3 8H6a2 2 0 01-2-2V8m16 0v6a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Enviar Correo
                                                </button>
                                            </form>
                                        @else
                                            <span class="w-full inline-flex justify-center px-3 py-1.5 rounded text-xs font-medium text-gray-400 bg-gray-50 border border-dashed border-gray-200">
                                                Sin correo
                                            </span>
                                        @endif

                                        {{-- 💬 Enviar por WhatsApp --}}
                                        @php
                                            $whatsappUrl = null;
                                            if ($guest->phone && $guest->invitation_url) {
                                                $cleanPhone = preg_replace('/\D+/', '', $guest->phone);
                                                // Ajuste básico para México
                                                $phoneWithCountry = '521' . $cleanPhone; 
                                                $waMessage = urlencode("Hola {$guest->full_name} 👋\nAquí está tu invitación: {$guest->invitation_url}");
                                                $whatsappUrl = "https://wa.me/{$phoneWithCountry}?text={$waMessage}";
                                            }
                                        @endphp

                                        @if($whatsappUrl)
                                            <a href="{{ $whatsappUrl }}" target="_blank"
                                            class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded text-xs font-semibold
                                                    text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100
                                                    transition-colors duration-150">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h4l2 3h8a2 2 0 012 2v1M9 13h3m4 0h.01M5 21h14a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2z" />
                                                </svg>
                                                WhatsApp
                                            </a>
                                        @endif

                                        {{-- 📋 Copiar link --}}
                                        @if($guest->invitation_url)
                                            <button type="button"
                                                    onclick="copyInvitationUrl('{{ $guest->invitation_url }}')"
                                                    class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded text-xs font-semibold
                                                        text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200
                                                        transition-colors duration-150">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8a2 2 0 002-2V7a2 2 0 00-2-2H9L5 9v5a2 2 0 002 2z" />
                                                </svg>
                                                Copiar
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 bg-gray-50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p>No hay invitados registrados todavía.</p>
                                    </div>
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
    </form>
</div>

{{-- Javascript se mantiene igual, pero es bueno asegurarnos que funcione con selectores genéricos --}}
<script>
    // ... (Tu script actual funciona perfecto aquí) ...
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
        // Fallback simple y moderno
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => showToast('Link copiado'));
        } else {
            // Fallback antiguo
            const dummy = document.createElement('input');
            dummy.value = url;
            document.body.appendChild(dummy);
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            showToast('Link copiado');
        }
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.className = 'fixed bottom-5 right-5 bg-gray-800 text-white text-xs font-semibold px-4 py-3 rounded shadow-lg z-50 animate-bounce';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }

    // 💬 WhatsApp masivo
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
            
            showToast('Mensaje masivo copiado. Pégalo en WhatsApp.');
        });
    }
</script>
@endsection