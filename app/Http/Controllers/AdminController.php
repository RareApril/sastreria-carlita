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
    public function analisis(Request $request)
    {
        // === 1. Lógica para rango de fechas segun filtro === //
        $filtro = $request->get('filtro', '30days');
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
    
        if ($filtro === '7days') {
            $start = now()->subDays(6)->startOfDay();
        } elseif ($filtro === '24hours') {
            $start = now()->subDay()->startOfDay();
        } elseif ($filtro === '3months') {
            $start = now()->subMonths(3)->startOfMonth();
        } elseif ($filtro === '12months') {
            $start = now()->subMonths(12)->startOfMonth();
        } elseif ($filtro === 'month') {
            $start = now()->startOfMonth();
        } elseif ($filtro === 'quarter') {
            $start = now()->subMonths(3)->startOfMonth();
        } elseif ($filtro === 'year') {
            $start = now()->startOfYear();
        } elseif ($filtro === 'all') {
            $firstUsuario = \App\Models\User::orderBy('created_at')->first();
            $start = $firstUsuario ? $firstUsuario->created_at->startOfDay() : now()->startOfDay();
        } elseif ($filtro === 'custom') {
            $start = Carbon::parse($request->get('fecha_inicio'))->startOfDay();
            $end = Carbon::parse($request->get('fecha_fin'))->endOfDay();
        }
    
        // === 2. Extrae fechas segun tipo de rango === //
        $period = \Carbon\CarbonPeriod::create($start, $end);
        $labels = [];
        foreach ($period as $date) {
            $labels[] = $date->format('d M');
        }
    
        // === 3. Usuarios y Reservas agrupados por fecha === //
        $usuarios = \App\Models\User::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    
        $usuariosData = [];
        foreach ($period as $date) {
            $usuariosData[] = (int) ($usuarios->firstWhere('fecha', $date->format('Y-m-d'))->total ?? 0);
        }
    
        $reservas = \App\Models\Reserva::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    
        $reservasData = [];
        foreach ($period as $date) {
            $reservasData[] = (int) ($reservas->firstWhere('fecha', $date->format('Y-m-d'))->total ?? 0);
        }
    
        // === 4. Arma el formato Chart.js === //
        $usuariosChart = [
            "labels" => $labels,
            "datasets" => [[
                "label" => "Usuarios registrados",
                "backgroundColor" => "rgba(124,58,237,0.18)",
                "borderColor" => "#7c3aed",
                "data" => $usuariosData,
                "tension" => 0.3,
                "fill" => true,
                "pointBackgroundColor" => "#7c3aed"
            ]]
        ];
        $reservasChart = [
            "labels" => $labels,
            "datasets" => [[
                "label" => "Reservas realizadas",
                "backgroundColor" => "rgba(37,99,235,0.18)",
                "borderColor" => "#2563eb",
                "data" => $reservasData,
                "tension" => 0.3,
                "fill" => true,
                "pointBackgroundColor" => "#2563eb"
            ]]
        ];
    
        return inertia('Admin/Analisis', [
            'usuariosChart' => $usuariosChart,
            'reservasChart' => $reservasChart,
            'filtro' => $filtro
        ]);
    } 
}
