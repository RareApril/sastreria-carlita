<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Reserva;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Estadísticas reales
        $totalUsuarios = User::count();
        $reservasPendientes = Reserva::where('estado', 'pendiente')->count();
        $reservasHoy = Reserva::whereDate('created_at', Carbon::today())->count();

        // Usuarios recientes (últimos 5)
        $usuariosRecientes = User::latest()->take(5)->get(['id','name','email','created_at']);

        // Actividad reciente (últimos movimientos de reservas y registros)
        $actividadReciente = Reserva::latest()->with('user')->take(6)->get()
            ->map(function ($r) {
                return [
                    'tipo'    => 'reserva',
                    'usuario' => $r->user->name ?? 'N/A',
                    'accion'  => "Nueva reserva RES-{$r->id}",
                    'tiempo'  => $r->created_at->diffForHumans(),
                    'color'   => 'blue',
                ];
            })
            ->concat(
                User::latest()->take(3)->get()
                    ->map(function ($u) {
                        return [
                            'tipo'    => 'usuario',
                            'usuario' => $u->name,
                            'accion'  => 'Se registró en el sistema',
                            'tiempo'  => $u->created_at->diffForHumans(),
                            'color'   => 'green',
                        ];
                    })
            )
            ->sortByDesc('tiempo')
            ->take(6)
            ->values();

        return Inertia::render('Admin/Dashboard', [
            'auth' => [
                'user' => $user,
            ],
            'stats' => [
                'totalUsuarios'      => $totalUsuarios,
                'reservasPendientes' => $reservasPendientes,
                'reservasHoy'        => $reservasHoy,
            ],
            'usuariosRecientes' => $usuariosRecientes,
            'actividadReciente' => $actividadReciente,
        ]);
    }
}
