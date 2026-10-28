<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Crud\CrudController;
use App\Models\User;
use App\Rules\RutChileno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class UsuarioController extends CrudController
{
    protected function modelClass(): string
    {
        return User::class;
    }

    protected function routeBase(): string
    {
        return 'admin.usuarios';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Usuarios', 'singular' => 'usuario', 'g' => 'o',
            'eyebrow' => 'Panel de administración',
            'description' => 'Consulta y administra las cuentas de usuario del sistema.',
            'nuevo' => 'Nuevo usuario',
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'email', 'rut'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nombre', 'value' => fn ($u) => $u->name],
            ['label' => 'RUT', 'value' => fn ($u) => $u->rut ?? '—', 'hide' => 'lg'],
            ['label' => 'Correo', 'value' => fn ($u) => $u->email, 'hide' => 'lg'],
            ['label' => 'Rol', 'value' => fn ($u) => $u->role, 'badge' => User::ROLES],
            ['label' => 'Estado', 'value' => fn ($u) => $u->activo ? 'activo' : 'inactivo', 'badge' => ['activo' => 'Activo', 'inactivo' => 'Inactivo'], 'hide' => 'xl'],
        ];
    }

    public function canDelete(Model $model): bool
    {
        return $model->id !== auth()->id();
    }

    protected function fields(?Model $model = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Nombre completo', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'rut', 'label' => 'RUT', 'type' => 'rut', 'placeholder' => '12.345.678-5', 'rules' => ['required', new RutChileno, Rule::unique('users', 'rut')->ignore($model?->id)]],
            ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'rules' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($model?->id)]],
            ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'rules' => ['nullable', 'string', 'max:20']],
            ['name' => 'role', 'label' => 'Rol', 'type' => 'select', 'options' => User::ROLES, 'rules' => ['required', Rule::in(array_keys(User::ROLES))]],
            ['name' => 'activo', 'label' => 'Estado', 'type' => 'select', 'options' => [1 => 'Activo', 0 => 'Inactivo'], 'rules' => ['required', 'boolean']],
            [
                'name' => 'password', 'label' => 'Contraseña', 'type' => 'password', 'full' => true,
                'hint' => $model ? 'Déjala en blanco para mantener la actual.' : 'Mínimo 8 caracteres.',
                'rules' => fn ($m) => $m ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            ],
        ];
    }
}
