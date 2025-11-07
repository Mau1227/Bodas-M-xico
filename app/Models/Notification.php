<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Tu tabla solo tiene 'created_at'
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'related_guest_id',
    ];

    // Para manejar el booleano
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    // Relación: Una notificación pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una notificación puede estar relacionada a un Invitado
    public function relatedGuest()
    {
        // Especificamos la llave foránea porque no es el estándar 'guest_id'
        return $this->belongsTo(Guest::class, 'related_guest_id');
    }
}