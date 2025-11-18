<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminOtpCodeMail;
use App\Models\User;
use Carbon\Carbon;

class AdminOtpController extends Controller
{
    // Muestra la vista donde el admin ingresa el OTP
    public function prompt(Request $request)
    {
        return inertia('Auth/OtpPrompt', [
            'email' => auth()->user()->email,
        ]);
    }

    // Valida el OTP recibido
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|digits:6',
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !$user->isAdmin() || !$user->mfa_enabled) {
            return back()->withErrors(['Código inválido o MFA desactivado.']);
        }

        if ($user->otp_expires_at < now() || $user->otp_code !== $request->input('otp_code')) {
            return back()->withErrors(['El código está vencido o es incorrecto.']);
        }

        // OTP correcto: limpia y accede
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    // Reenvía el código OTP al admin
    public function resend(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if (!$user || !$user->isAdmin() || !$user->mfa_enabled) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        Mail::to($user->email)->send(new AdminOtpCodeMail($otp));
        return response()->json(['message' => 'Código reenviado']);
    }
}
