<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class EstudianteController extends CrudController
{
    protected function modelClass(): string
    {
        return Estudiante::class;
    }

    protected function routeBase(): string
    {
        return 'admin.estudiantes';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Estudiantes', 'singular' => 'estudiante', 'g' => 'o',
            'eyebrow' => 'Panel de administración',
            'description' => 'Perfiles académicos de los estudiantes que realizan práctica.',
            'nuevo' => 'Nuevo estudiante',
        ];
    }

    protected function with(): array
    {
        return ['user', 'carrera'];
    }

    protected function searchable(): array
    {
        return ['matricula', 'user.name', 'user.rut', 'carrera.nombre'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Nombre', 'value' => fn ($e) => $e->user?->name ?? '—'],
            ['label' => 'Matrícula', 'value' => fn ($e) => $e->matricula],
            ['label' => 'Carrera', 'value' => fn ($e) => $e->carrera?->nombre ?? '—', 'hide' => 'lg'],
            ['label' => 'Semestre', 'value' => fn ($e) => $e->semestre, 'hide' => 'xl'],
            ['label' => 'Teléfono', 'value' => fn ($e) => $e->telefono ?? '—', 'hide' => 'xl'],
        ];
    }

    protected function fields(?Model $model = null): array
    {
        $usuarios = User::where('role', 'estudiante')
            ->where(function ($q) use ($model) {
                $q->whereDoesntHave('estudiante');

                if ($model) {
                    $q->orWhere('id', $model->user_id);
                }
            })
            ->orderBy('name')->pluck('name', 'id')->all();

        return [
            [
                'name' => 'user_id', 'label' => 'Usuario', 'type' => 'select', 'options' => $usuarios,
                'placeholder' => 'Selecciona un usuario con rol Estudiante',
                'hint' => 'Solo aparecen usuarios con rol Estudiante sin perfil.',
                'rules' => ['required', 'exists:users,id', Rule::unique('estudiantes', 'user_id')->ignore($model?->id)],
            ],
            [
                'name' => 'carrera_id', 'label' => 'Carrera', 'type' => 'select',
                'options' => Carrera::orderBy('nombre')->pluck('nombre', 'id')->all(),
                'placeholder' => 'Selecciona una carrera',
                'rules' => ['required', 'exists:carreras,id'],
            ],
            ['name' => 'matricula', 'label' => 'Matrícula', 'type' => 'text', 'rules' => ['required', 'string', 'max:20', Rule::unique('estudiantes', 'matricula')->ignore($model?->id)]],
            ['name' => 'semestre', 'label' => 'Semestre', 'type' => 'number', 'attrs' => ['min' => 1, 'max' => 14], 'rules' => ['required', 'integer', 'between:1,14']],
            ['name' => 'telefono', 'label' => 'Teléfono', 'type' => 'tel', 'full' => true, 'rules' => ['nullable', 'string', 'max:20']],
        ];
    }
}
