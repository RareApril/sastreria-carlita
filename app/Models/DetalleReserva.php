<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleReserva extends Model
{
    protected $fillable = [
        'reserva_id', 'prenda_id', 'cantidad', 'precio_alquiler'
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function prenda(): BelongsTo
    {
        return $this->belongsTo(Prenda::class);
    }
}
