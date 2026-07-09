<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === 'pending') {
            $allowedRoutes = [
                'learner.pending-activation',
                'learner.profile',
                'learner.profile.update',
                'checkout',
                'payment.initiate',
                'payment.success',
                'payment.return',
                'payment.cancel',
                'logout',
            ];

            if (!$request->routeIs($allowedRoutes)) {
                return redirect()->route('learner.pending-activation');
            }
        }

        return $next($request);
    }
}
