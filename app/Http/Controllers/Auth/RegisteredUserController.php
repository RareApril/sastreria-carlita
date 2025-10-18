<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:35',
                // Solo letras y espacios (a-z, A-Z, tildes, ñ)
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u'
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
                // Email mínimo 4 caracteres antes y después de @, termina en al menos dos letras
                'regex:/^.{4,}@.{4,}\.[a-zA-Z]{2,}$/'
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Mensajes personalizados
            'name.regex' => 'El nombre solo debe contener letras y espacios (sin números ni símbolos).',
            'name.max' => 'El nombre no debe tener más de 35 caracteres.',
            'email.regex' => 'El correo debe ser real (ejemplo: usuario.real@dominio.com).'
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        event(new Registered($user));
        Auth::login($user);
    
        return redirect(route('dashboard', absolute: false));
    }    
}