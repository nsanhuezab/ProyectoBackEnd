<?php

namespace App\Http\Controllers\Gestion;

use App\Http\Controllers\Crud\CrudController;
use App\Models\Estudiante;
use App\Models\Oferta;
use App\Models\Postulacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostulacionController extends CrudController
{
    protected function modelClass(): string
    {
        return Postulacion::class;
    }

    protected function routeBase(): string
    {
        return 'gestion.postulaciones';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Postulaciones', 'singular' => 'postulación', 'g' => 'a',
            'eyebrow' => 'Gestión y evaluación de prácticas',
            'description' => 'Revisa y administra las postulaciones de los estudiantes a las ofertas.',
            'nuevo' => 'Nueva postulación',
        ];
    }

    protected function with(): array
    {
        return ['oferta.empresa', 'estudiante.user'];
    }

    protected function searchable(): array
    {
        return ['estudiante.user.name', 'estudiante.matricula', 'oferta.titulo', 'oferta.empresa.razon_social'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Estudiante', 'value' => fn ($p) => $p->estudiante?->user?->name ?? '—'],
            ['label' => 'Oferta', 'value' => fn ($p) => $p->oferta?->titulo ?? '—'],
            ['label' => 'Empresa', 'value' => fn ($p) => $p->oferta?->empresa?->razon_social ?? '—', 'hide' => 'lg'],
            ['label' => 'Fecha', 'value' => fn ($p) => $p->created_at?->format('d/m/Y'), 'hide' => 'xl'],
            ['label' => 'Estado', 'value' => fn ($p) => $p->estado, 'badge' => Postulacion::ESTADOS],
        ];
    }

    public function rowActions(Model $model): array
    {
        if ($model->estado === 'pendiente') {
            return [
                ['label' => 'Aceptar', 'url' => route('gestion.postulaciones.estado', $model), 'method' => 'PATCH', 'data' => ['estado' => 'aceptada'], 'style' => 'success'],
                ['label' => 'Rechazar', 'url' => route('gestion.postulaciones.estado', $model), 'method' => 'PATCH', 'data' => ['estado' => 'rechazada'], 'style' => 'danger'],
            ];
        }

        if ($model->estado === 'aceptada' && $model->estudiante && ! $model->estudiante->practicas()->where('oferta_id', $model->oferta_id)->exists()) {
            return [
                ['label' => 'Crear práctica', 'url' => route('gestion.practicas.create', ['postulacion' => $model->id]), 'method' => 'GET', 'style' => 'primary'],
            ];
        }

        return [];
    }

    protected function fields(?Model $model = null): array
    {
        $ofertas = Oferta::with('empresa')->orderBy('titulo')->get()
            ->mapWithKeys(fn ($o) => [$o->id => $o->titulo.' — '.($o->empresa?->razon_social ?? 'Sin empresa')])->all();

        $estudiantes = Estudiante::with('user')->get()->sortBy(fn ($e) => $e->user?->name)
            ->mapWithKeys(fn ($e) => [$e->id => ($e->user?->name ?? 'Sin usuario').' ('.$e->matricula.')'])->all();

        return [
            ['name' => 'oferta_id', 'label' => 'Oferta', 'type' => 'select', 'options' => $ofertas, 'placeholder' => 'Selecciona una oferta', 'rules' => ['required', 'exists:ofertas,id']],
            [
                'name' => 'estudiante_id', 'label' => 'Estudiante', 'type' => 'select', 'options' => $estudiantes, 'placeholder' => 'Selecciona un estudiante',
                'rules' => fn ($m) => [
                    'required', 'exists:estudiantes,id',
                    Rule::unique('postulaciones', 'estudiante_id')->where('oferta_id', request('oferta_id'))->ignore($m?->id),
                ],
            ],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => Postulacion::ESTADOS, 'full' => true, 'rules' => ['required', Rule::in(array_keys(Postulacion::ESTADOS))]],
            ['name' => 'mensaje', 'label' => 'Mensaje', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:2000']],
        ];
    }

    protected function defaults(Request $request): array
    {
        return ['estado' => 'pendiente'];
    }

    /** Aceptar / rechazar una postulación (coordinador o administrador). */
    public function estado(Request $request, string $record): RedirectResponse
    {
        $postulacion = Postulacion::findOrFail($record);
        $data = $request->validate(['estado' => ['required', Rule::in(['aceptada', 'rechazada'])]]);

        $postulacion->update($data);

        return back()->with('status', 'Postulación '.$data['estado'].'.');
    }
}
