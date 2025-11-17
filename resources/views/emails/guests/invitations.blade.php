<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Invitación a la boda</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="20" cellspacing="0" style="background:#ffffff; border-radius:8px;">
                    <tr>
                        <td>
                            <h1 style="margin-top:0; color:#4b2e83;">¡Hola {{ $guest->full_name }}!</h1>

                            <p>
                                @if(!empty($event->welcome_message))
                                    {{ $event->welcome_message }}
                                @else
                                    Nos llena de alegría compartir contigo un día muy especial.
                                @endif
                            </p>

                            <p>
                                <strong>{{ $event->title ?? 'Nuestra boda' }}</strong><br>
                                @if(!empty($event->date))
                                    Fecha: <strong>{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</strong><br>
                                @endif
                                @if(!empty($event->location))
                                    Lugar: <strong>{{ $event->location }}</strong>
                                @endif
                            </p>

                            <p style="margin:20px 0;">
                                <a href="{{ $url }}"
                                   style="background:#4b2e83; color:#ffffff; text-decoration:none; padding:12px 24px; border-radius:999px; font-weight:bold;">
                                    Ver mi invitación y confirmar asistencia
                                </a>
                            </p>

                            <p style="font-size:12px; color:#666;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $url }}">{{ $url }}</a>
                            </p>

                            <p>Con cariño,<br>
                                {{ $event->title ?? 'Los novios' }}</p>
                        </td>
                    </tr>
                </table>
                <p style="margin-top:15px; font-size:11px; color:#999;">Enviado con FestLink 💌</p>
            </td>
        </tr>
    </table>
</body>
</html>
