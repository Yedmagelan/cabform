<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Learner\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Mettre à jour le profil.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        $user->update($request->only('first_name', 'last_name', 'phone'));

        // Profil (bio)
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => $request->bio]
        );

        // Avatar
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Mettre à jour la double authentification.
     */
    public function update2fa(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'two_factor_enabled' => $request->has('two_factor_enabled'),
        ]);

        return back()->with('success', 'Paramètres de double authentification mis à jour.');
    }
}
