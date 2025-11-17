<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    // Lista + formulario
    public function index()
    {
        $user = Auth::user();

        // por ahora: un evento por usuario; si luego son varios, aquí ponemos un selector
        $event = $user->event ?? $user->events()->first();

        if (!$event) {
            abort(404, 'No se encontró evento para este usuario.');
        }

        $guests = $event->guests()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('guests.index', compact('event', 'guests'));
    }

    // Alta manual (Opción A)
    public function store(Request $request)
    {
        $user  = Auth::user();
        $event = $user->event ?? $user->events()->firstOrFail();

        $validated = $request->validate([
            'full_name'      => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'max_companions' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $validated['max_companions'] = $validated['max_companions'] ?? 0;
        $validated['event_id'] = $event->id;
        $validated['invitation_token'] = Str::random(24);  // token para URL única

        Guest::create($validated);

        return redirect()
            ->route('guests.index')
            ->with('status', 'Invitado agregado correctamente.');
    }

    // Actualizar datos del invitado
    public function update(Request $request, Guest $guest)
    {
        $user  = Auth::user();
        $event = $user->event ?? $user->events()->firstOrFail();

        // Seguridad: que el invitado sea de este evento
        if ($guest->event_id !== $event->id) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name'          => ['required', 'string', 'max:255'],
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'max_companions'     => ['nullable', 'integer', 'min:0', 'max:10'],
            'status'             => ['nullable', 'in:pending,confirmed,declined'],
            'dietary_restrictions' => ['nullable', 'string'],
            'message_to_couple'    => ['nullable', 'string'],
        ]);

        $validated['max_companions'] = $validated['max_companions'] ?? 0;

        $guest->update($validated);

        return redirect()
            ->route('guests.index')
            ->with('status', 'Invitado actualizado.');
    }

    // Eliminar
    public function destroy(Guest $guest)
    {
        $user  = Auth::user();
        $event = $user->event ?? $user->events()->firstOrFail();

        if ($guest->event_id !== $event->id) {
            abort(403);
        }

        $guest->delete();

        return redirect()
            ->route('guests.index')
            ->with('status', 'Invitado eliminado.');
    }

    // Importación masiva CSV (Opción B)
    public function import(Request $request)
    {
        $user  = Auth::user();
        $event = $user->event ?? $user->events()->firstOrFail();

        // Luego aquí le metemos restricción de plan PREMIUM si quieres
        $request->validate([
            'file' => ['required', 'file'], // luego podemos volver a poner mimes
        ]);

        $path = $request->file('file')->getRealPath();

        if (! $handle = fopen($path, 'r')) {
            return back()->withErrors(['file' => 'No se pudo leer el archivo.']);
        }

        // Leemos primera línea como texto para detectar delimitador
        $firstLine = fgets($handle);

        // Detectar delimitador: ; o ,
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        // Regresamos el puntero al inicio del archivo
        rewind($handle);

        // Leer encabezado
        $header = fgetcsv($handle, 0, $delimiter);

        if (! $header || count($header) === 0) {
            fclose($handle);
            return back()->withErrors(['file' => 'El archivo CSV no tiene encabezados válidos.']);
        }

        // Normalizamos encabezados (trim + lowercase)
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        $created = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Saltar filas vacías
            if (count(array_filter($row, fn($v) => $v !== null && trim($v) !== '')) === 0) {
                continue;
            }

            // Si la fila no tiene el mismo número de columnas, la brincamos
            if (count($row) !== count($header)) {
                // Opcional: aquí podrías loguear el problema
                continue;
            }

            $data = array_combine($header, $row);

            // full_name es obligatorio
            if (empty($data['full_name'])) {
                continue;
            }

            Guest::create([
                'event_id'         => $event->id,
                'full_name'        => $data['full_name'],
                'email'            => $data['email'] ?? null,
                'phone'            => $data['phone'] ?? null,
                'max_companions'   => isset($data['max_companions']) ? (int) $data['max_companions'] : 0,
                'invitation_token' => Str::random(24),
            ]);

            $created++;
        }

        fclose($handle);

        return redirect()
            ->route('guests.index')
            ->with('status', "Importación completada. Se agregaron {$created} invitado(s).");
    }

    public function template()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_invitados.csv"',
        ];

        $content = implode("\n", [
            'full_name,email,phone,max_companions',
            'Ejemplo Invitado,ejemplo@correo.com,9991112233,1',
        ]) . "\n";

        return response($content, 200, $headers);
    }


}
