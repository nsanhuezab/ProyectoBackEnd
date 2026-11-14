<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Autenticación de la API privada mediante token Bearer.
 */
class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        [$token, $plain] = ApiToken::issue($user, $credentials['device_name'] ?? 'api');

        return response()->json([
            'token' => $plain,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at?->toIso8601String(),
            'user' => $this->userData($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userData($request->user())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('api_token')?->delete();

        return response()->json(['message' => 'Sesión cerrada. El token fue revocado.']);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'career_id' => $user->career_id,
            'roles' => $user->getRoleNames()->values()->all(),
        ];
    }
}
