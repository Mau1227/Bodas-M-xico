<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'description',
        'preview_image_url',
        'is_premium',
        'is_active',
    ];

    // Para manejar los booleanos
    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relación: Una Plantilla puede ser usada por muchos Eventos
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}