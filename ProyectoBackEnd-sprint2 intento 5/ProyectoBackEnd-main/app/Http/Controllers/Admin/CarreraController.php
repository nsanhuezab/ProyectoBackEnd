<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Carrera;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarreraController extends CrudController
{
    protected function modelClass(): string
    {
        return Carrera::class;
    }

    protected function routeBase(): string
    {
        return 'admin.carreras';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Carreras', 'singular' => 'carrera', 'g' => 'a',
            'eyebrow' => 'Panel de administración',
            'description' => 'Administra las carreras que participan en el proceso de prácticas.',
            'nuevo' => 'Nueva carrera',
        ];
    }

    protected function query(Request $request): Builder
    {
        return Carrera::query()->withCount('estudiantes');
    }

    protected function searchable(): array
    {
        return ['nombre', 'codigo', 'facultad'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nombre', 'value' => fn ($c) => $c->nombre],
            ['label' => 'Código', 'value' => fn ($c) => $c->codigo],
            ['label' => 'Facultad', 'value' => fn ($c) => $c->facultad ?? '—', 'hide' => 'lg'],
            ['label' => 'Estudiantes', 'value' => fn ($c) => $c->estudiantes_count, 'hide' => 'lg'],
        ];
    }

    protected function fields(?Model $model = null): array
    {
        return [
            ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'codigo', 'label' => 'Código', 'type' => 'text', 'rules' => ['required', 'string', 'max:20', Rule::unique('carreras', 'codigo')->ignore($model?->id)]],
            ['name' => 'facultad', 'label' => 'Facultad', 'type' => 'text', 'full' => true, 'rules' => ['nullable', 'string', 'max:255']],
        ];
    }
}
