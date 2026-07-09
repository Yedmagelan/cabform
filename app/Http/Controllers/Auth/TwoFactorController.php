<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    /**
     * Display the 2FA verification form.
     */
    public function index()
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    /**
     * Verify the 2FA code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $userId = session('2fa_user_id');
        $code = session('2fa_code');
        $expiresAt = session('2fa_expires_at');

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['2fa_user_id', '2fa_code', '2fa_expires_at', '2fa_remember']);
            return redirect()->route('login')->with('error', 'Le code de vérification a expiré. Veuillez vous reconnecter.');
        }

        if ($request->code == $code) {
            // Log in the user
            Auth::loginUsingId($userId, session('2fa_remember', false));

            // Clear session values
            session()->forget(['2fa_user_id', '2fa_code', '2fa_expires_at', '2fa_remember']);

            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            AuditLog::log('login', $user, null, null, 'Connexion utilisateur (2FA)');

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }
            if ($user->isInstructor()) {
                return redirect()->intended(route('instructor.dashboard'));
            }
            return redirect()->intended(route('learner.dashboard'));
        }

        return back()->withErrors(['code' => 'Le code de double authentification est invalide.']);
    }
}
