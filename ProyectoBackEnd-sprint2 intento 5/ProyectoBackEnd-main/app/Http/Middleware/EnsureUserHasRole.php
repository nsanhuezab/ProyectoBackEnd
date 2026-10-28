<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso: ->middleware('role:coordinador,empresa')
 * El administrador siempre tiene acceso.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless(
            $user && $user->activo && ($user->isAdmin() || in_array($user->role, $roles, true)),
            403,
            'No tienes permisos para acceder a esta sección.'
        );

        return $next($request);
    }
}
