<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UseLocalDemoUser
{
    /**
     * Modo demo (solo local): si no hay sesión, entra como el primer administrador.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app()->environment('local') && config('app.demo_mode'), 404);

        // Si hay una sesión iniciada, se usa esa
        if (Auth::check()) {
            return $next($request);
        }

        $user = User::whereHas('roles', fn ($roles) => $roles->where('name', 'Administrador'))->orderBy('id')->first()
            ?? (new User)->forceFill([
                'id' => 0,
                'name' => 'Usuario de demostración',
                'last_name' => 'Local',
                'email' => 'demo@localhost.test',
            ]);

        Auth::setUser($user);
        $request->attributes->set('demo_user', true);

        return $next($request);
    }
}
