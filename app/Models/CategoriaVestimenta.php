<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaVestimenta extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function prendas(): HasMany
    {
        return $this->hasMany(Prenda::class);
    }
}
