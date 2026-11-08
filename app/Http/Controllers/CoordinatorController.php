<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinatorRequest;
use App\Models\Career;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CoordinatorController extends Controller
{
    private const ROLE = 'Coordinador';

    /**
     * Mantenedor de coordinadores: usuarios con rol Coordinador y su carrera.
     */
    public function index(): View
    {
        $coordinators = User::role(self::ROLE)
            ->with('career:id,codigo,nombre')
            ->orderBy('name')
            ->get();

        $counts = [
            'total' => $coordinators->count(),
            'carreras' => $coordinators->pluck('career_id')->filter()->unique()->count(),
            'sin_carrera' => $coordinators->whereNull('career_id')->count(),
        ];

        $careers = Career::query()->orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        return view('coordinators.index', compact('coordinators', 'counts', 'careers'));
    }

    public function store(CoordinatorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['nombre'],
            'last_name' => $data['apellido'] ?? null,
            'email' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'career_id' => $data['career_id'],
            'password' => Str::random(32),
        ]);
        $user->assignRole(self::ROLE);

        return back()->with('success', "Coordinador «{$user->name}» registrado correctamente.");
    }

    public function update(CoordinatorRequest $request, User $coordinator): RedirectResponse
    {
        $this->ensureCoordinator($coordinator);
        $data = $request->validated();

        $coordinator->update([
            'name' => $data['nombre'],
            'last_name' => $data['apellido'] ?? null,
            'email' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'career_id' => $data['career_id'],
        ]);

        return back()->with('success', "Coordinador «{$coordinator->name}» actualizado correctamente.");
    }

    public function destroy(User $coordinator): RedirectResponse
    {
        $this->ensureCoordinator($coordinator);
        $coordinator->delete();

        return back()->with('success', "Coordinador «{$coordinator->name}» eliminado correctamente.");
    }

    private function ensureCoordinator(User $user): void
    {
        abort_unless($user->hasRole(self::ROLE), 404);
    }
}
