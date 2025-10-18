<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
