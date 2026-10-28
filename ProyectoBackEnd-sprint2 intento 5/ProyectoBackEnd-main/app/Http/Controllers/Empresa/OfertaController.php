<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Concerns\DefinesOfertas;
use App\Http\Controllers\Crud\CrudController;
use App\Models\Oferta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * "Publicar Oferta": la empresa gestiona solo sus propias ofertas.
 */
class OfertaController extends CrudController
{
    use DefinesOfertas;

    protected function modelClass(): string
    {
        return Oferta::class;
    }

    protected function routeBase(): string
    {
        return 'empresa.ofertas';
    }

    protected function labels(): array
    {
        return [
            'plural' => 'Mis ofertas', 'singular' => 'oferta', 'g' => 'a',
            'eyebrow' => 'Panel de empresa',
            'description' => 'Publica y administra las ofertas de práctica de tu empresa.',
            'nuevo' => 'Publicar oferta',
        ];
    }

    private function empresaId(Request $request): ?int
    {
        return $request->user()->empresa?->id;
    }

    protected function query(Request $request): Builder
    {
        return Oferta::query()->withCount('postulaciones')
            ->where('empresa_id', $this->empresaId($request) ?? 0);
    }

    protected function searchable(): array
    {
        return ['titulo', 'area'];
    }

    protected function columns(): array
    {
        return $this->ofertaColumns(false);
    }

    protected function fields(?Model $model = null): array
    {
        return $this->ofertaFields(false);
    }

    protected function beforeSave(array $data, ?Model $model, Request $request): array
    {
        $empresaId = $this->empresaId($request);

        abort_unless($empresaId, 403, 'Tu usuario no está asociado a una empresa. Contacta al administrador.');

        $data['empresa_id'] = $empresaId;

        return $data;
    }

    public function canCreate(): bool
    {
        return (bool) auth()->user()?->empresa;
    }
}
