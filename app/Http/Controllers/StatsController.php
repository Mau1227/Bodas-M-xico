<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user  = Auth::user();
        $event = $user->event ?? $user->events()->first();

        if (! $event) {
            return view('stats.index', [
                'event'          => null,
                'totalInvitados' => 0,
                'confirmados'    => 0,
                'noAsisten'      => 0,
                'pendientes'     => 0,
                'tasaRespuesta'  => 0,
                'timelineLabels' => [],
                'timelineData'   => [],
            ]);
        }

        $guests = $event->guests;

        $totalInvitados = $guests->count();
        $confirmados    = $guests->where('status', 'confirmed')->count();
        $noAsisten      = $guests->where('status', 'declined')->count();
        $pendientes     = $guests->where('status', 'pending')->count();

        $totalRespondieron = $confirmados + $noAsisten;
        $tasaRespuesta     = $totalInvitados > 0
            ? round($totalRespondieron * 100 / $totalInvitados, 1)
            : 0;

        // 🔢 Línea de tiempo: cuántos se han ido confirmando por día
        $timeline = $guests
            ->whereIn('status', ['confirmed', 'declined'])
            ->groupBy(function ($guest) {
                return $guest->updated_at->format('Y-m-d');
            })
            ->map->count()
            ->sortKeys();

        $timelineLabels = $timeline->keys()->values()->all();  // ['2025-11-20', '2025-11-21', ...]
        $timelineData   = $timeline->values()->all();           // [3, 7, 9, ...]

        return view('stats.index', compact(
            'event',
            'totalInvitados',
            'confirmados',
            'noAsisten',
            'pendientes',
            'tasaRespuesta',
            'timelineLabels',
            'timelineData',
        ));
    }
}
