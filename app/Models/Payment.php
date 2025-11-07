<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'status',
        'plan_type',
        'plan_duration_months',
        'metadata',
    ];

    // Para manejar el campo JSON correctamente
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // Relación: Un pago pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}