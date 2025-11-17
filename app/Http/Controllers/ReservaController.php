<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Prenda;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReservaController extends Controller
{
    // Ver reservas del usuario (Dashboard usuario)
    public function indexUsuario() {
        $user = auth()->user();
        $reservas = Reserva::with(['detalleReservas.prenda'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        return Inertia::render('Dashboard', [
            'user' => $user,
            'reservas' => $reservas,
            'csrf_token' => csrf_token() 
        ]);
    }

    // Mostrar formulario para reservar un terno
    public function create(Prenda $prenda) {
        $fechasOcupadas = Reserva::whereHas('detalleReservas', function($q) use ($prenda) {
                $q->where('prenda_id', $prenda->id);
            })
            ->where('estado', '!=', 'cancelada')
            ->get(['fecha_inicio', 'fecha_fin'])
            ->map(function($reserva) {
                // sumamos 2 días extra en frontend también si se usa este array 
                return [
                    'inicio' => $reserva->fecha_inicio,
                    'fin' => Carbon::parse($reserva->fecha_fin)->addDays(2)->toDateString()
                ];
            });

        return Inertia::render('ReservarTerno', [
            'prenda' => $prenda,
            'fechasOcupadas' => $fechasOcupadas
        ]);
    }

    // Crear una nueva reserva
    public function store(Request $request) {
        $data = $request->validate([
            'prenda_id'     => 'required|exists:prendas,id',
            'talla'         => 'required|string',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicio'
        ]);

        // VALIDACIÓN del solape considerando 2 días extra después del fin de cada reserva abierta
        $existeReserva = Reserva::whereHas('detalleReservas', function ($q) use ($data) {
                $q->where('prenda_id', $data['prenda_id']);
            })
            ->where('estado', '!=', 'cancelada')
            ->get()
            ->contains(function ($reserva) use ($data) {
                $inicioOcupado = Carbon::parse($reserva->fecha_inicio);
                $finOcupado = Carbon::parse($reserva->fecha_fin)->addDays(2); // sumamos los 2 días por mantenimiento

                $nuevoInicio = Carbon::parse($data['fecha_inicio']);
                $nuevoFin = Carbon::parse($data['fecha_fin']);

                // Retorna TRUE si solapa, es decir, si el rango solicitado toca el rango ocupado (con días extra)
                return $nuevoInicio <= $finOcupado && $nuevoFin >= $inicioOcupado;
            });

        if ($existeReserva) {
            return back()->withErrors(['Las fechas seleccionadas (incluyendo días de mantenimiento) ya están ocupadas para esta prenda. Por favor elige otro rango.']);
        }

        // Generar código único automático
        $codigo = 'RES-' . str_pad((Reserva::count() + 1), 5, '0', STR_PAD_LEFT);

        // Creamos la reserva
        $reserva = Reserva::create([
            'user_id'      => auth()->id(),
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => 'pendiente',
            'total'        => 0, 
            'codigo'       => $codigo
        ]);

        // Detalle de reservas
        DetalleReserva::create([
            'reserva_id'      => $reserva->id,
            'prenda_id'       => $data['prenda_id'],
            'talla'           => $data['talla'],
            'precio_alquiler' => Prenda::find($data['prenda_id'])->precio_alquiler, 
        ]);
        
        return redirect()->route('dashboard')->with('success', 'Reserva creada correctamente');
    }

    // Cancelar por usuario (si pendiente)
    public function cancelar(Reserva $reserva) {
        // Verificar que sea el dueño de la reserva
        if ($reserva->user_id !== auth()->id()) {
            abort(403, 'No puedes cancelar esta reserva');
        }
        
        if ($reserva->estado === 'pendiente') {
            $reserva->update(['estado' => 'cancelada']);
        }
        return back();
    }
    
    // ADMIN: Ver todas
    public function indexAdmin() {
        $reservas = Reserva::with(['detalleReservas.prenda','user'])
            ->orderByDesc('created_at')
            ->get();
    
        return Inertia::render('Admin/Reservas', [
            'reservas' => $reservas,
            'csrf_token' => csrf_token()
        ]);
    }

    // ADMIN: Aprobar
    public function aprobar(Reserva $reserva) {
        $reserva->update(['estado'=>'aprobada']);
        return back();
    }

    // ADMIN: Rechazar
    public function rechazar(Reserva $reserva) {
        $reserva->update(['estado'=>'rechazada']);
        return back();
    }
}
