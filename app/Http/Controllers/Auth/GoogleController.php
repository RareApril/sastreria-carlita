<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        // Si no existe el usuario, lo crea automático
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                // Genera password random (jamás lo usará)
                'password' => bcrypt(uniqid())
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard');
    }
}
