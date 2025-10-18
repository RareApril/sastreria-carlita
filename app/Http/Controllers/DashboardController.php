<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Obtener reservas del usuario (adapta según tu modelo)
        // Si no tienes modelo de reservas aún, puedes usar datos de ejemplo:
        $reservas = [
            [
                'id' => 1,
                'prenda_nombre' => 'Terno Clásico Negro',
                'talla' => 'M',
                'color' => 'Negro',
                'fecha_reserva' => '2025-10-10',
                'estado' => 'activa'
            ],
            [
                'id' => 2,
                'prenda_nombre' => 'Terno Elegante Azul',
                'talla' => 'L',
                'color' => 'Azul',
                'fecha_reserva' => '2025-09-25',
                'estado' => 'completada'
            ]
        ];
        
        // Cuando tengas modelo real, usa algo como:
        // $reservas = $user->reservas()->with('prenda')->get();
        
        return Inertia::render('Dashboard', [
            'reservas' => $reservas
        ]);
    }
}
