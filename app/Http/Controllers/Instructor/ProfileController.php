<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $profile = $user->profile ?? new Profile();
        $courses = $user->courses()->published()->get();

        return view('instructor.profile.show', compact('user', 'profile', 'courses'));
    }

    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile ?? new Profile();
        $notifPrefs = $profile->interests['notification_preferences'] ?? [];

        return view('instructor.profile.edit', compact('user', 'profile', 'notifPrefs'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string',
            'linkedin_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'expertises' => 'nullable|array',
            'locale' => 'required|in:fr,en',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $avatarPath]);
        }

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'locale' => $validated['locale'],
        ]);

        // Préserver d'autres champs dans interests
        $interests = $profile->interests ?? [];
        $interests['expertises'] = $validated['expertises'] ?? [];

        $profile->update([
            'bio' => $validated['bio'],
            'linkedin_url' => $validated['linkedin_url'],
            'website_url' => $validated['website_url'],
            'interests' => $interests,
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updateSecurity(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    public function toggle2fa(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'two_factor_enabled' => !$user->two_factor_enabled,
        ]);

        return response()->json([
            'success' => true,
            'enabled' => $user->two_factor_enabled,
            'message' => $user->two_factor_enabled ? 'Authentification 2FA activée.' : 'Authentification 2FA désactivée.'
        ]);
    }

    public function logoutOtherDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Le mot de passe saisi est incorrect.');
        }

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Toutes les autres sessions actives ont été déconnectées.');
    }
}
