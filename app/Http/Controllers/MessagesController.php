<?php
namespace App\Http\Controllers;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // todos los eventos del usuario (como ya lo tenías)
        $events = Event::where('user_id', $user->id)->get();

        // por ahora usamos el primer evento como "evento activo" del dashboard
        $event = $events->first();

        // valores por defecto por si aún no hay evento
        $guestMessages = $event->guests()
        ->whereNotNull('message_to_couple')
        ->where('message_to_couple', '!=', '')
        ->orderByDesc('updated_at')
        ->get(['id', 'full_name', 'status', 'message_to_couple', 'updated_at']);

        return view('messages.index', [
        'event'          => $event,
        'guestMessages'  => $guestMessages,
        // ... lo que ya tuvieras
        ]);
    }
}
