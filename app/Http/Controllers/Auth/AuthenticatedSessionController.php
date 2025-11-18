<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail; // NUEVO
use App\Mail\AdminOtpCodeMail;        // NUEVO
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;                    // NUEVO

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        // Validar que es admin y tiene MFA activado
        if ($user->role === 'admin' && $user->mfa_enabled) {
            // Generar OTP
            $otp = rand(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(5);
            $user->save();

            // Enviar OTP por email
            Mail::to($user->email)->send(new AdminOtpCodeMail($otp));

            // Redirigir a la vista para ingreso de OTP (crea esa vista después)
            return redirect()->route('admin.otp.prompt');
        }

        // Si es admin pero sin MFA, accede directo al dashboard de admin
        if ($user->role === 'admin') {
            return redirect(route('admin.dashboard'));
        }

        // Usuario normal, acceso normal
        return redirect(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
