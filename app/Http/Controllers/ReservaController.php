<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\DetalleReserva;
use App\Models\Prenda;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'csrf_token' => csrf_token() // <- AGREGA ESTA LÍNEA
        ]);
    }
    // NUEVO: Mostrar formulario para reservar un terno
    public function create(Prenda $prenda) {
        return Inertia::render('ReservarTerno', [
            'prenda' => $prenda
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

        // Generar código único automático
        $codigo = 'RES-' . str_pad((Reserva::count() + 1), 5, '0', STR_PAD_LEFT);

        // Creamos la reserva
        $reserva = Reserva::create([
            'user_id'      => auth()->id(),
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin'    => $data['fecha_fin'],
            'estado'       => 'pendiente',
            'total'        => 0, // Opcional: puedes calcularlo después
            'codigo'       => $codigo
        ]);

        // Agregar el detalle a detalle_reservas
        DetalleReserva::create([
            'reserva_id'      => $reserva->id,
            'prenda_id'       => $data['prenda_id'],
            'talla'           => $data['talla'],
            'precio_alquiler' => Prenda::find($data['prenda_id'])->precio_alquiler, // <- agrega esta línea
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
    
        return Inertia::render('Admin/Reservas', [  // <-- AQUÍ
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
