<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Empresa;
use App\Models\User;
use App\Rules\RutChileno;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmpresaController extends CrudController
{
    protected function modelClass(): string
    {
        return Empresa::class;
    }

    protected function routeBase(): string
    {
        return 'admin.empresas';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Empresas', 'singular' => 'empresa', 'g' => 'a',
            'eyebrow' => 'Panel de administración',
            'description' => 'Gestiona las empresas que ofrecen prácticas profesionales.',
            'nuevo' => 'Nueva empresa',
        ];
    }

    protected function query(Request $request): Builder
    {
        return Empresa::query()->withCount('ofertas');
    }

    protected function searchable(): array
    {
        return ['razon_social', 'rut', 'giro', 'contacto_nombre', 'email'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Razón social', 'value' => fn ($e) => $e->razon_social],
            ['label' => 'RUT', 'value' => fn ($e) => $e->rut, 'hide' => 'lg'],
            ['label' => 'Contacto', 'value' => fn ($e) => $e->contacto_nombre ?? '—', 'hide' => 'lg'],
            ['label' => 'Correo', 'value' => fn ($e) => $e->email, 'hide' => 'xl'],
            ['label' => 'Ofertas', 'value' => fn ($e) => $e->ofertas_count, 'hide' => 'xl'],
            ['label' => 'Estado', 'value' => fn ($e) => $e->estado, 'badge' => Empresa::ESTADOS],
        ];
    }

    protected function fields(?Model $model = null): array
    {
        return [
            ['name' => 'razon_social', 'label' => 'Razón social', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'rut', 'label' => 'RUT', 'type' => 'rut', 'placeholder' => '76.123.456-K', 'rules' => ['required', new RutChileno, Rule::unique('empresas', 'rut')->ignore($model?->id)]],
            ['name' => 'giro', 'label' => 'Giro', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            ['name' => 'contacto_nombre', 'label' => 'Nombre de contacto', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            ['name' => 'email', 'label' => 'Correo', 'type' => 'email', 'rules' => ['required', 'email', 'max:255']],
            ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'rules' => ['nullable', 'string', 'max:20']],
            ['name' => 'direccion', 'label' => 'Dirección', 'type' => 'text', 'full' => true, 'rules' => ['nullable', 'string', 'max:255']],
            [
                'name' => 'user_id', 'label' => 'Usuario asociado', 'type' => 'select',
                'options' => User::where('role', 'empresa')->orderBy('name')->pluck('name', 'id')->all(),
                'placeholder' => 'Sin usuario asociado',
                'hint' => 'Cuenta (rol Empresa) que podrá publicar ofertas.',
                'rules' => ['nullable', 'exists:users,id', Rule::unique('empresas', 'user_id')->ignore($model?->id)],
            ],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => Empresa::ESTADOS, 'rules' => ['required', Rule::in(array_keys(Empresa::ESTADOS))]],
        ];
    }
}
