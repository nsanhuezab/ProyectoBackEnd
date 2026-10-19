<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Evaluacion;
use App\Models\Practica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EvaluacionController extends CrudController
{
    protected function modelClass(): string
    {
        return Evaluacion::class;
    }

    protected function routeBase(): string
    {
        return 'gestion.evaluaciones';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Evaluaciones', 'singular' => 'evaluación', 'g' => 'a',
            'eyebrow' => 'Gestión y evaluación de prácticas',
            'description' => 'Notas y comentarios asociados a cada práctica (escala 1,0 a 7,0).',
            'nuevo' => 'Nueva evaluación',
        ];
    }

    protected function with(): array
    {
        return ['practica.estudiante.user', 'practica.empresa', 'evaluador'];
    }

    protected function searchable(): array
    {
        return ['comentario', 'practica.estudiante.user.name', 'practica.empresa.razon_social'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Estudiante', 'value' => fn ($e) => $e->practica?->estudiante?->user?->name ?? '—'],
            ['label' => 'Empresa', 'value' => fn ($e) => $e->practica?->empresa?->razon_social ?? '—', 'hide' => 'lg'],
            ['label' => 'Tipo', 'value' => fn ($e) => Evaluacion::TIPOS[$e->tipo] ?? $e->tipo, 'hide' => 'lg'],
            ['label' => 'Nota', 'value' => fn ($e) => number_format((float) $e->nota, 1, ',', '.')],
            ['label' => 'Fecha', 'value' => fn ($e) => $e->fecha->format('d/m/Y'), 'hide' => 'xl'],
            ['label' => 'Evaluador', 'value' => fn ($e) => $e->evaluador?->name ?? '—', 'hide' => 'xl'],
        ];
    }

    protected function defaults(Request $request): array
    {
        return [
            'practica_id' => $request->query('practica'),
            'fecha' => now()->toDateString(),
            'tipo' => 'coordinador',
        ];
    }

    protected function beforeSave(array $data, ?Model $model, Request $request): array
    {
        if (! $model) {
            $data['evaluador_id'] = $request->user()->id;
        }

        return $data;
    }

    protected function fields(?Model $model = null): array
    {
        $practicas = Practica::with(['estudiante.user', 'empresa'])->latest('id')->get()
            ->mapWithKeys(fn ($p) => [$p->id => ($p->estudiante?->user?->name ?? 'Sin estudiante').' — '.($p->empresa?->razon_social ?? 'Sin empresa')])->all();

        return [
            ['name' => 'practica_id', 'label' => 'Práctica', 'type' => 'select', 'options' => $practicas, 'placeholder' => 'Selecciona una práctica', 'full' => true, 'rules' => ['required', 'exists:practicas,id']],
            ['name' => 'tipo', 'label' => 'Tipo de evaluación', 'type' => 'select', 'options' => Evaluacion::TIPOS, 'rules' => ['required', Rule::in(array_keys(Evaluacion::TIPOS))]],
            ['name' => 'nota', 'label' => 'Nota', 'type' => 'number', 'attrs' => ['min' => 1, 'max' => 7, 'step' => '0.1'], 'hint' => 'Escala de 1,0 a 7,0', 'rules' => ['required', 'numeric', 'between:1,7']],
            ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'full' => true, 'rules' => ['required', 'date']],
            ['name' => 'comentario', 'label' => 'Comentario', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:3000']],
        ];
    }
}
