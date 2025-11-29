<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuestsExport implements FromCollection, WithHeadings
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function collection()
    {
        return $this->event->guests()
            ->select(
                'full_name',
                'email',
                'status',
                'max_companions',
                'confirmed_companions',
                'dietary_restrictions',
                'message_to_couple',
                'invitation_sent_at',
                'updated_at'
            )
            ->get()
            ->map(function ($guest) {
                return [
                    'Nombre'               => $guest->full_name,
                    'Email'                => $guest->email,
                    'Estatus'              => $guest->status,
                    'Pases_asignados'      => $guest->max_companions + 1,
                    'Pases_confirmados'    => 1 + (int) $guest->confirmed_companions,
                    'Restricciones'        => $guest->dietary_restrictions,
                    'Mensaje_a_los_novios' => $guest->message_to_couple,
                    'Invitación_enviada'   => optional($guest->invitation_sent_at)->format('Y-m-d H:i'),
                    'Última_actualización' => $guest->updated_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Email',
            'Estatus',
            'Pases asignados',
            'Pases confirmados',
            'Restricciones',
            'Mensaje a los novios',
            'Invitación enviada',
            'Última actualización',
        ];
    }
}
