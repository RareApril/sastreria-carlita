<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prenda;
use App\Models\CategoriaVestimenta;

class PrendaSeeder extends Seeder
{
    public function run()
    {
        $categoriaId = CategoriaVestimenta::where('nombre', 'Ternos')->first()->id;

        $prendas = [
            [
                'imagen' => '3.png',
                'nombre' => 'Saco marrón crema',
                'precio' => 60,
                'stock' => 1,
                'tallas' => ['M', 'L']
            ],
            [
                'imagen' => '4.png',
                'nombre' => 'Saco blanco crema',
                'precio' => 50,
                'stock' => 1,
                'tallas' => ['S']
            ],
            [
                'imagen' => '5.png',
                'nombre' => 'Saco gris con rayas',
                'precio' => 60,
                'stock' => 1,
                'tallas' => ['M', 'L']
            ],
            [
                'imagen' => '6.png',
                'nombre' => 'Saco azul militar',
                'precio' => 55,
                'stock' => 2,
                'tallas' => ['M', 'L']
            ],
            [
                'imagen' => '7.png',
                'nombre' => 'Saco azul gris con rayas',
                'precio' => 50,
                'stock' => 1,
                'tallas' => ['S', 'M', 'L']
            ],
            [
                'imagen' => '8.png',
                'nombre' => 'Saco crema claro',
                'precio' => 60,
                'stock' => 1,
                'tallas' => ['M', 'L']
            ],
            [
                'imagen' => '9.png',
                'nombre' => 'Saco azul noche',
                'precio' => 60,
                'stock' => 1,
                'tallas' => ['M', 'L']
            ],
        ];

        $codigo = 1;
        foreach ($prendas as $p) {
            foreach ($p['tallas'] as $talla) {
                Prenda::create([
                    'categoria_vestimenta_id' => $categoriaId,
                    'codigo' => 'TERNO-' . str_pad($codigo++, 3, '0', STR_PAD_LEFT),
                    'nombre' => $p['nombre'],
                    'talla' => $talla,
                    'color' => '',
                    'precio_alquiler' => $p['precio'],
                    'stock' => $p['stock'],
                    'descripcion' => 'Incluye pantalón, corbata y bolsa para guardar. Opción de zapatos +S/15.',
                    'imagen' => '/storage/prendas/' . $p['imagen']
                ]);
            }
        }
    }
}
