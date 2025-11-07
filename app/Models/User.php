<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name', // Cambiado de 'name'
        'email',
        'password',
        'phone',
        'plan_type',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // Laravel lo usa, aunque no esté en tu DB (es bueno dejarlo)
        'email_verification_token',
        'reset_password_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'email_verified_at' => 'datetime', // Comentamos el de por defecto
            'email_verified' => 'boolean', // Agregamos el tuyo
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
        ];
    }

    // --- RELACIONES ---

    // Un Usuario (Novio) tiene muchos Eventos
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Un Usuario (Novio) tiene muchos Pagos
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}