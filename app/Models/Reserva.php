<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    protected $fillable = [
        'user_id', 'evento_id', 'fecha_inicio', 'fecha_fin',
        'estado', 'total', 'codigo'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function detalleReservas(): HasMany
    {
        return $this->hasMany(DetalleReserva::class);
    }
}
