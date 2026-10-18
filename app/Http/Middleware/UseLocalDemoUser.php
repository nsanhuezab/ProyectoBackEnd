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
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app()->environment('local') && config('app.demo_mode'), 404);

        $user = (new User)->forceFill([
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
