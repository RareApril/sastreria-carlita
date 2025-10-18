<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no está autenticado o no es admin, redirige al dashboard común
        if (! $request->user() || $request->user()->role !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder al área de administrador.');
        }
        return $next($request);
    }
}
