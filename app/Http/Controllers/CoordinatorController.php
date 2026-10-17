<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\CoordinatorRequest;
use App\Models\Career;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Para conectar la tabla real de coordinadores: cambiar la consulta en index()
 * y el método format(). La búsqueda, el orden y la paginación no se tocan.
 */
class CoordinatorController extends Controller
{
    use HasDataTable;

    private const ROLE = 'Coordinador';

    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            $request,
            User::role(self::ROLE)->with('career:id,nombre'),
            searchable: ['name', 'email', 'telefono'],
            sortable: [
                'id' => 'id',
                'nombre' => 'name',
                'correo' => 'email',
                'telefono' => 'telefono',
                'carrera' => fn ($q, $dir) => $q->orderBy(
                    Career::select('nombre')->whereColumn('careers.id', 'users.career_id'),
                    $dir
                ),
            ],
            transform: fn (User $u) => $this->format($u),
            defaultSort: 'id',
            defaultDir: 'desc',
            extraSearch: fn ($q, $like) => $q->orWhereHas(
                'career',
                fn ($c) => $c->where('nombre', 'like', $like)
            ),
        );
    }

    public function store(CoordinatorRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['nombre'],
            'email' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'career_id' => $data['career_id'],
            'password' => Str::random(32),
        ]);
        $user->assignRole(self::ROLE);

        return response()->json($this->format($user->load('career:id,nombre')), 201);
    }

    public function update(CoordinatorRequest $request, User $coordinator): JsonResponse
    {
        $this->ensureCoordinator($coordinator);
        $data = $request->validated();

        $coordinator->update([
            'name' => $data['nombre'],
            'email' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'career_id' => $data['career_id'],
        ]);

        return response()->json($this->format($coordinator->load('career:id,nombre')));
    }

    public function destroy(User $coordinator): Response
    {
        $this->ensureCoordinator($coordinator);
        $coordinator->delete();

        return response()->noContent();
    }

    private function ensureCoordinator(User $user): void
    {
        abort_unless($user->hasRole(self::ROLE), 404);
    }

    private function format(User $u): array
    {
        return [
            'id' => $u->id,
            'nombre' => $u->name,
            'correo' => $u->email,
            'telefono' => $u->telefono,
            'career_id' => $u->career_id,
            'carrera' => $u->career?->nombre,
        ];
    }
}
