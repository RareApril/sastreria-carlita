<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Agrega el estado actual de MFA y si es admin
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'isAdmin' => $request->user()->isAdmin(),
            'mfa_enabled' => $request->user()->mfa_enabled,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Si admin, permite cambiar MFA
        if ($user->isAdmin() && $request->has('mfa_enabled')) {
            $user->mfa_enabled = (bool) $request->input('mfa_enabled');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Permite activar/desactivar MFA desde el perfil del admin.
     */
    public function toggleMfa(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            $user->mfa_enabled = !$user->mfa_enabled;
            $user->save();
            return Redirect::route('profile.edit')->with('status', 'MFA modificado');
        }
        return Redirect::route('profile.edit')->withErrors(['No tienes permiso para cambiar MFA.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
