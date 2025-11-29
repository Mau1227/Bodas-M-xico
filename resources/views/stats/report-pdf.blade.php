<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de invitados</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1, h2 {
            margin: 0 0 8px 0;
        }
        .header {
            margin-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de invitados</h1>
        <h2>{{ $event->bride_name }} & {{ $event->groom_name }}</h2>
        @if($event->wedding_date)
            <p>Fecha: {{ \Carbon\Carbon::parse($event->wedding_date)->format('d/m/Y') }}</p>
        @endif
        <p>Total invitados: {{ $guests->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invitado</th>
                <th>Email</th>
                <th>Estatus</th>
                <th>Pases asignados</th>
                <th>Pases confirmados</th>
                <th>Restricciones</th>
                <th>Mensaje</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guests as $guest)
                <tr>
                    <td>{{ $guest->full_name }}</td>
                    <td>{{ $guest->email }}</td>
                    <td>{{ $guest->status }}</td>
                    <td>{{ $guest->max_companions + 1 }}</td>
                    <td>{{ 1 + (int) $guest->confirmed_companions }}</td>
                    <td>{{ $guest->dietary_restrictions }}</td>
                    <td>{{ $guest->message_to_couple }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
