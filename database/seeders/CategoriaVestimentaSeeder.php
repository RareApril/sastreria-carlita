<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaVestimenta;

class CategoriaVestimentaSeeder extends Seeder
{
    public function run()
    {
        CategoriaVestimenta::create([
            'nombre' => 'Ternos',
            'descripcion' => 'Ternos para caballero con pantalón, corbata y bolsa de guardar. Opción de zapatos por +S/15.'
        ]);
    }
}
