@extends('layouts.app')

@section('content')

{{-- Tarjeta: Mensajes de tus invitados --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    
    {{-- Header de la tarjeta --}}
    <div class="px-4 md:px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-slate-800">
                Mensajes de tus invitados
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Mensajes dejados al confirmar asistencia.
            </p>
        </div>

        @if($guestMessages->count() > 0)
            <span class="self-start sm:self-center inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 whitespace-nowrap">
                {{ $guestMessages->count() }} mensaje{{ $guestMessages->count() === 1 ? '' : 's' }}
            </span>
        @endif
    </div>

    @if($guestMessages->isEmpty())
        {{-- Estado vacío --}}
        <div class="px-6 py-8 text-center sm:text-left text-sm text-slate-500">
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="p-2 bg-slate-50 rounded-full">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <span>Aún no hay mensajes. Cuando tus invitados escriban algo, aparecerá aquí.</span>
            </div>
        </div>
    @else
        {{-- Lista con scroll --}}
        <ul class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto">
            @foreach($guestMessages as $msg)
                <li class="px-4 md:px-6 py-4 hover:bg-slate-50 transition-colors duration-150">
                    
                    {{-- Contenedor Flex: Columna en móvil, Fila en PC --}}
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 sm:gap-4">
                        
                        {{-- Bloque Izquierdo: Nombre + Badge + Mensaje --}}
                        <div class="flex-1 min-w-0"> {{-- min-w-0 evita que el flex se rompa con texto largo --}}
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <p class="text-sm font-bold text-slate-800 truncate">
                                    {{ $msg->full_name }}
                                </p>
                                
                                {{-- Badges --}}
                                @if($msg->status === 'confirmed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-100 text-emerald-700">
                                        Asistirá
                                    </span>
                                @elseif($msg->status === 'declined')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-700">
                                        No Asistirá
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line break-words">
                                {{ $msg->message_to_couple }}
                            </p>
                        </div>

                        {{-- Bloque Derecho: Fecha (se va abajo en móvil) --}}
                        <div class="mt-2 sm:mt-0 flex-shrink-0">
                            <p class="text-[11px] text-slate-400 sm:text-right">
                                {{ $msg->updated_at->format('d M') }} <span class="hidden sm:inline">|</span> 
                                <span class="sm:block text-slate-300 sm:text-slate-400">{{ $msg->updated_at->format('H:i') }}</span>
                            </p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

@endsection