<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    public const CREATED_AT = 'sent_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'guest_id',
        'email_type',
        'recipient_email',
        'subject',
        'status',
        'error_message',
    ];

    // Relación: El log pertenece a un Usuario (quien lo envió)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: El log pertenece a un Invitado (a quien se envió)
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}