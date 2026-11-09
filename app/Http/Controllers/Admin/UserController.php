<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        // /dashboard es el inicio por defecto de Laravel: quien no administra va a su propia página.
        $user = $request->user();
        if (! $user?->hasAnyRole(['Administrador', 'Super Administrador'])) {
            return redirect()->route(match (true) {
                (bool) $user?->hasRole('Estudiante') => 'student.offers.view',
                (bool) $user?->hasRole('Coordinador') => 'applications.index',
                (bool) $user?->hasRole('Empresa') => 'offers.index',
                default => 'profile.edit',
            });
        }

        $this->authorizeUserManagement($request);

        $isSuperAdministrator = $this->isSuperAdministrator($request);
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->when(! $isSuperAdministrator, fn ($query) => $query->where('name', '!=', 'Super Administrador'))
            ->orderBy('name')
            ->get();

        $users = User::query()
            ->with('roles')
            ->orderByDesc('id')
            ->get();

        return view('dashboard', [
            'users' => $users,
            'roles' => $roles,
            'showInternalId' => $isSuperAdministrator,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeUserManagement($request);
        $this->normalizeRut($request);

        $validated = $request->validate($this->userRules($request));
        $role = Role::query()->findOrFail($validated['role_id']);
        unset($validated['role_id'], $validated['password_confirmation']);

        $user = User::query()->create($validated);
        $user->syncRoles([$role]);

        return to_route('dashboard')->with('status', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeUserManagement($request);
        $this->authorizeTargetUser($request, $user);
        $this->normalizeRut($request);

        $validated = $request->validate($this->userRules($request, $user));
        $role = Role::query()->findOrFail($validated['role_id']);
        unset($validated['role_id'], $validated['password_confirmation'], $validated['user_id']);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles([$role]);

        return to_route('dashboard')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeUserManagement($request);
        $this->authorizeTargetUser($request, $user);

        abort_if($request->user()?->is($user), 403, 'No puedes eliminar tu propia cuenta.');

        if ($user->hasRole('Super Administrador')) {
            abort_if(User::role('Super Administrador')->count() <= 1, 403, 'Debe quedar al menos un Super Administrador.');
        }

        $user->syncRoles([]);
        $user->delete();

        return to_route('dashboard')->with('status', 'Usuario eliminado correctamente.');
    }

    private function authorizeUserManagement(Request $request): void
    {
        abort_unless($request->user()?->hasAnyRole(['Administrador', 'Super Administrador']), 403);
    }

    private function authorizeTargetUser(Request $request, User $user): void
    {
        if ($user->hasRole('Super Administrador')) {
            abort_unless($this->isSuperAdministrator($request), 403);
        }
    }

    private function isSuperAdministrator(Request $request): bool
    {
        if (app()->environment('local') && config('app.demo_mode')) {
            return false;
        }

        return $request->user()?->hasRole('Super Administrador') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function userRules(Request $request, ?User $user = null): array
    {
        $isSuperAdministrator = $this->isSuperAdministrator($request);
        $roleRule = Rule::exists('roles', 'id')->where(function ($query) use ($isSuperAdministrator): void {
            $query->where('guard_name', 'web');

            if (! $isSuperAdministrator) {
                $query->where('name', '!=', 'Super Administrador');
            }
        });
        $rutRule = Rule::unique('users', 'rut');
        $emailRule = Rule::unique('users', 'email');

        if ($user !== null) {
            $rutRule->ignore($user);
            $emailRule->ignore($user);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:12', 'regex:/^\d{7,8}-[\dK]$/i', $rutRule],
            'gender' => ['required', Rule::in(['Femenino', 'Masculino', 'No binario', 'Prefiero no decir'])],
            'age' => ['required', 'integer', 'between:1,120'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'password' => $user === null ? ['required', 'string', 'min:8', 'confirmed'] : ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => $user === null ? ['required', 'string'] : ['nullable', 'string'],
            'role_id' => ['required', 'integer', $roleRule],
        ];
    }

    private function normalizeRut(Request $request): void
    {
        $rut = $request->input('rut');

        if (is_string($rut)) {
            $request->merge(['rut' => mb_strtoupper((string) preg_replace('/[.\s]/', '', $rut))]);
        }
    }
}
