<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifier que l'utilisateur a un des rôles requis.
     * Usage: middleware('role:administrateur,gestionnaire')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
