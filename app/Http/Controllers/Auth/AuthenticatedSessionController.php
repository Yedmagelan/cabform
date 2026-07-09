<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('Les informations d\'identification fournies ne correspondent pas à nos enregistrements.')])->onlyInput('email');
        }

        $user = Auth::user();

        // 2FA check
        if ($user->two_factor_enabled) {
            $code = rand(100000, 999999);

            // Log out user for now and save details in session
            Auth::logout();

            session([
                '2fa_user_id' => $user->id,
                '2fa_code' => $code,
                '2fa_expires_at' => now()->addMinutes(15),
                '2fa_remember' => $request->boolean('remember'),
            ]);

            \Illuminate\Support\Facades\Log::info("Code 2FA pour l'utilisateur {$user->email} : {$code}");

            return redirect()->route('two-factor.index');
        }

        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AuditLog::log('login', $user, null, null, 'Connexion utilisateur');

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isInstructor()) {
            return redirect()->intended(route('instructor.dashboard'));
        }
        return redirect()->intended(route('learner.dashboard'));
    }

    public function destroy(Request $request)
    {
        AuditLog::log('logout', Auth::user(), null, null, 'Déconnexion utilisateur');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
