@extends('layouts.app')

@section('content')

{{-- Tarjeta: Mensajes de tus invitados --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-100">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-slate-800">
                Mensajes de tus invitados
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Estos son los mensajes que han dejado al confirmar su asistencia.
            </p>
        </div>
        @if($guestMessages->count() > 0)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                {{ $guestMessages->count() }} mensaje{{ $guestMessages->count() === 1 ? '' : 's' }}
            </span>
        @endif
    </div>

    @if($guestMessages->isEmpty())
        <div class="px-6 py-5 text-sm text-slate-500">
            Aún no hay mensajes. Cuando tus invitados confirmen y escriban algo, aparecerá aquí 💌
        </div>
    @else
        <ul class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            @foreach($guestMessages as $msg)
                <li class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $msg->full_name }}
                                @if($msg->status === 'confirmed')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                                        Asistirá
                                    </span>
                                @elseif($msg->status === 'declined')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700">
                                        No asistirá
                                    </span>
                                @endif
                            </p>
                            <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">
                                {{ $msg->message_to_couple }}
                            </p>
                        </div>
                        <p class="text-xs text-slate-400 whitespace-nowrap">
                            {{ $msg->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection