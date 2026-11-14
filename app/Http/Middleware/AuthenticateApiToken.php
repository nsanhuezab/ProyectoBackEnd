<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentica la API privada con  Authorization: Bearer <token>.
 */
class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();

        if (! $plain) {
            return response()->json(['message' => 'Token no enviado. Usa el encabezado Authorization: Bearer <token>.'], 401);
        }

        $token = ApiToken::with('user')->where('token_hash', hash('sha256', $plain))->first();

        if (! $token || ! $token->user || ($token->expires_at && $token->expires_at->isPast())) {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($token->user);
        $request->setUserResolver(fn () => $token->user);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}
