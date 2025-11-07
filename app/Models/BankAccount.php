<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'bank_name',
        'account_number',
        'clabe',
        'account_holder',
    ];

    // Relación: Una cuenta bancaria pertenece a un Evento
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}