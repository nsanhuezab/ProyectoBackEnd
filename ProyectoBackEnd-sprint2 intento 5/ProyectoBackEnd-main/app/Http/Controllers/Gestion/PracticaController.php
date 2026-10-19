<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Empresa;
use App\Models\Estudiante;
use App\Models\Oferta;
use App\Models\Postulacion;
use App\Models\Practica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PracticaController extends CrudController
{
    protected function modelClass(): string
    {
        return Practica::class;
    }

    protected function routeBase(): string
    {
        return 'gestion.practicas';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Prácticas', 'singular' => 'práctica', 'g' => 'a',
            'eyebrow' => 'Gestión y evaluación de prácticas',
            'description' => 'Registro de las prácticas profesionales en curso, pendientes y finalizadas.',
            'nuevo' => 'Nueva práctica',
        ];
    }

    protected function with(): array
    {
        return ['estudiante.user', 'empresa'];
    }

    protected function searchable(): array
    {
        return ['supervisor', 'estudiante.user.name', 'estudiante.matricula', 'empresa.razon_social'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Estudiante', 'value' => fn ($p) => $p->estudiante?->user?->name ?? '—'],
            ['label' => 'Empresa', 'value' => fn ($p) => $p->empresa?->razon_social ?? '—'],
            ['label' => 'Supervisor', 'value' => fn ($p) => $p->supervisor, 'hide' => 'lg'],
            ['label' => 'Período', 'value' => fn ($p) => $p->fecha_inicio->format('d/m/Y').' – '.$p->fecha_termino->format('d/m/Y'), 'hide' => 'xl'],
            ['label' => 'Estado', 'value' => fn ($p) => $p->estado, 'badge' => Practica::ESTADOS],
        ];
    }

    public function rowActions(Model $model): array
    {
        return [
            ['label' => 'Evaluar', 'url' => route('gestion.evaluaciones.create', ['practica' => $model->id]), 'method' => 'GET', 'style' => 'primary'],
        ];
    }

    protected function defaults(Request $request): array
    {
        $defaults = ['estado' => 'pendiente', 'horas_totales' => 320];

        $postulacion = $request->filled('postulacion')
            ? Postulacion::with('oferta')->find($request->query('postulacion'))
            : null;

        if ($postulacion && $postulacion->oferta) {
            $defaults = array_merge($defaults, [
                'estudiante_id' => $postulacion->estudiante_id,
                'oferta_id' => $postulacion->oferta_id,
                'empresa_id' => $postulacion->oferta->empresa_id,
                'horas_totales' => $postulacion->oferta->horas,
            ]);
        }

        return $defaults;
    }

    protected function fields(?Model $model = null): array
    {
        $estudiantes = Estudiante::with('user')->get()->sortBy(fn ($e) => $e->user?->name)
            ->mapWithKeys(fn ($e) => [$e->id => ($e->user?->name ?? 'Sin usuario').' ('.$e->matricula.')'])->all();

        return [
            ['name' => 'estudiante_id', 'label' => 'Estudiante', 'type' => 'select', 'options' => $estudiantes, 'placeholder' => 'Selecciona un estudiante', 'rules' => ['required', 'exists:estudiantes,id']],
            ['name' => 'empresa_id', 'label' => 'Empresa', 'type' => 'select', 'options' => Empresa::orderBy('razon_social')->pluck('razon_social', 'id')->all(), 'placeholder' => 'Selecciona una empresa', 'rules' => ['required', 'exists:empresas,id']],
            ['name' => 'oferta_id', 'label' => 'Oferta de origen', 'type' => 'select', 'options' => Oferta::orderBy('titulo')->pluck('titulo', 'id')->all(), 'placeholder' => 'Sin oferta asociada', 'rules' => ['nullable', 'exists:ofertas,id']],
            ['name' => 'supervisor', 'label' => 'Supervisor en la empresa', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
            ['name' => 'fecha_inicio', 'label' => 'Fecha de inicio', 'type' => 'date', 'rules' => ['required', 'date']],
            ['name' => 'fecha_termino', 'label' => 'Fecha de término', 'type' => 'date', 'rules' => ['required', 'date', 'after:fecha_inicio']],
            ['name' => 'horas_totales', 'label' => 'Horas totales', 'type' => 'number', 'attrs' => ['min' => 1], 'rules' => ['required', 'integer', 'min:1', 'max:2000']],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => Practica::ESTADOS, 'rules' => ['required', Rule::in(array_keys(Practica::ESTADOS))]],
            ['name' => 'observaciones', 'label' => 'Observaciones', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:3000']],
        ];
    }
}
