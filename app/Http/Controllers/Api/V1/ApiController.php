<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Utilidades comunes de la API REST v1.
 */
abstract class ApiController extends Controller
{
    protected function perPage(Request $request, int $default = 10): int
    {
        return min(max((int) $request->input('per_page', $default), 1), 50);
    }

    protected function paginated(LengthAwarePaginator $page, Closure $map): JsonResponse
    {
        return response()->json([
            'data' => collect($page->items())->map($map)->values(),
            'meta' => [
                'total' => $page->total(),
                'page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    protected function notFound(string $what): JsonResponse
    {
        return response()->json(['message' => "$what no encontrada."], 404);
    }

    protected function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['Administrador', 'Super Administrador']);
    }

    protected function fullName(?User $user): ?string
    {
        return $user ? trim($user->name.' '.$user->last_name) : null;
    }
}
