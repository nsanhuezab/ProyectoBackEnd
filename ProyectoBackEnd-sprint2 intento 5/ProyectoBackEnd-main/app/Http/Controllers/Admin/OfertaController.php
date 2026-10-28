<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DefinesOfertas;
use App\Http\Controllers\Crud\CrudController;
use App\Models\Oferta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OfertaController extends CrudController
{
    use DefinesOfertas;

    protected function modelClass(): string
    {
        return Oferta::class;
    }

    protected function routeBase(): string
    {
        return 'admin.ofertas';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Ofertas de práctica', 'singular' => 'oferta', 'g' => 'a',
            'eyebrow' => 'Panel de administración',
            'description' => 'Todas las ofertas publicadas por las empresas.',
            'nuevo' => 'Nueva oferta',
        ];
    }

    protected function query(Request $request): Builder
    {
        return Oferta::query()->with('empresa')->withCount('postulaciones');
    }

    protected function searchable(): array
    {
        return ['titulo', 'area', 'empresa.razon_social'];
    }

    protected function columns(): array
    {
        return $this->ofertaColumns(true);
    }

    protected function fields(?Model $model = null): array
    {
        return $this->ofertaFields(true);
    }
}
