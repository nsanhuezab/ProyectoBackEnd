<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Empresa;
use App\Models\Oferta;
use Illuminate\Validation\Rule;

/**
 * Campos y columnas compartidos por el CRUD de ofertas del administrador
 * y el de "Publicar Oferta" de la empresa.
 */
trait DefinesOfertas
{
    protected function ofertaFields(bool $withEmpresa): array
    {
        $fields = [];

        if ($withEmpresa) {
            $fields[] = [
                'name' => 'empresa_id', 'label' => 'Empresa', 'type' => 'select',
                'options' => Empresa::orderBy('razon_social')->pluck('razon_social', 'id')->all(),
                'placeholder' => 'Selecciona una empresa',
                'rules' => ['required', 'exists:empresas,id'],
            ];
        }

        return array_merge($fields, [
            ['name' => 'titulo', 'label' => 'Título', 'type' => 'text', 'rules' => ['required', 'string', 'max:255'], 'full' => ! $withEmpresa],
            ['name' => 'area', 'label' => 'Área', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
            ['name' => 'modalidad', 'label' => 'Modalidad', 'type' => 'select', 'options' => Oferta::MODALIDADES, 'rules' => ['required', Rule::in(array_keys(Oferta::MODALIDADES))]],
            ['name' => 'cupos', 'label' => 'Cupos', 'type' => 'number', 'attrs' => ['min' => 1], 'rules' => ['required', 'integer', 'min:1', 'max:100']],
            ['name' => 'horas', 'label' => 'Horas de práctica', 'type' => 'number', 'attrs' => ['min' => 1], 'rules' => ['required', 'integer', 'min:1', 'max:2000']],
            ['name' => 'fecha_limite', 'label' => 'Fecha límite de postulación', 'type' => 'date', 'rules' => ['nullable', 'date']],
            ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => Oferta::ESTADOS, 'rules' => ['required', Rule::in(array_keys(Oferta::ESTADOS))]],
            ['name' => 'descripcion', 'label' => 'Descripción', 'type' => 'textarea', 'full' => true, 'rules' => ['required', 'string', 'max:5000']],
            ['name' => 'requisitos', 'label' => 'Requisitos', 'type' => 'textarea', 'full' => true, 'rules' => ['nullable', 'string', 'max:5000']],
        ]);
    }

    protected function ofertaColumns(bool $withEmpresa): array
    {
        $columns = [['label' => 'Título', 'value' => fn ($o) => $o->titulo]];

        if ($withEmpresa) {
            $columns[] = ['label' => 'Empresa', 'value' => fn ($o) => $o->empresa?->razon_social ?? '—', 'hide' => 'lg'];
        }

        return array_merge($columns, [
            ['label' => 'Modalidad', 'value' => fn ($o) => Oferta::MODALIDADES[$o->modalidad] ?? $o->modalidad, 'hide' => 'xl'],
            ['label' => 'Cupos', 'value' => fn ($o) => $o->cupos, 'hide' => 'lg'],
            ['label' => 'Postulaciones', 'value' => fn ($o) => $o->postulaciones_count, 'hide' => 'xl'],
            ['label' => 'Cierre', 'value' => fn ($o) => $o->fecha_limite?->format('d/m/Y') ?? '—', 'hide' => 'xl'],
            ['label' => 'Estado', 'value' => fn ($o) => $o->estado, 'badge' => Oferta::ESTADOS],
        ]);
    }
}
