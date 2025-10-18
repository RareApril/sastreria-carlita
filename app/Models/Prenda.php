<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prenda extends Model
{
    protected $fillable = [
        'categoria_vestimenta_id', 'codigo', 'nombre',
        'talla', 'color', 'precio_alquiler', 'stock',
        'descripcion', 'imagen', 'marca', 'genero'
    ];    

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaVestimenta::class, 'categoria_vestimenta_id');
    }

    public function detalleReservas(): HasMany
    {
        return $this->hasMany(DetalleReserva::class);
    }

    // NUEVA RELACIÓN: tallas asociadas a la prenda
    public function tallas(): HasMany
    {
        return $this->hasMany(Talla::class);
    }
}
