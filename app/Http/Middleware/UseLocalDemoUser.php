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
     * Modo demo (solo en local): entra como el primer Administrador de la base de datos
     * para que los permisos y botones funcionen igual que con una sesión real.
     * Si todavía no hay administrador (base vacía), usa un usuario de demostración sin guardar.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app()->environment('local') && config('app.demo_mode'), 404);

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
