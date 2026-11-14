<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API pública 1: ofertas de práctica aprobadas.
 */
class PublicOfferController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'career_id' => ['nullable', 'integer'],
            'modalidad' => ['nullable', 'string', 'max:40'],
            'area' => ['nullable', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:120'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        $query = $this->baseQuery();

        if ($request->filled('career_id')) {
            $query->whereHas('careers', fn ($c) => $c->whereKey($request->integer('career_id')));
        }
        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->string('modalidad')->toString());
        }
        if ($request->filled('area')) {
            $query->where('area', 'like', '%'.$request->string('area')->toString().'%');
        }
        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', 'like', '%'.$request->string('ubicacion')->toString().'%');
        }
        if ($request->filled('search')) {
            $like = '%'.$request->string('search')->toString().'%';
            $query->where(function ($q) use ($like) {
                $q->where('titulo', 'like', $like)
                    ->orWhere('empresa', 'like', $like)
                    ->orWhere('area', 'like', $like)
                    ->orWhereHas('company', fn ($c) => $c->where('razon_social', 'like', $like));
            });
        }

        $page = $query->orderByDesc('id')->paginate($this->perPage($request));

        return $this->paginated($page, fn (Offer $offer) => $this->format($offer));
    }

    public function show(int $id): JsonResponse
    {
        $offer = $this->baseQuery()->find($id);

        if (! $offer) {
            return $this->notFound('Oferta');
        }

        return response()->json(['data' => $this->format($offer, true)]);
    }

    private function baseQuery()
    {
        return Offer::query()
            ->with(['company:id,razon_social,rubro', 'careers:id,nombre'])
            ->withCount(['applications as cupos_ocupados' => fn ($q) => $q->whereHas('practice')])
            ->where('estado', 'Aprobada');
    }

    private function format(Offer $offer, bool $detail = false): array
    {
        $data = [
            'id' => $offer->id,
            'titulo' => $offer->titulo,
            'empresa' => $offer->company?->razon_social ?? $offer->empresa,
            'area' => $offer->area,
            'modalidad' => $offer->modalidad,
            'ubicacion' => $offer->ubicacion,
            'duracion_semanas' => $offer->duracion_semanas,
            'vacantes' => (int) $offer->vacantes,
            'vacantes_disponibles' => max((int) $offer->vacantes - (int) $offer->cupos_ocupados, 0),
            'carreras' => $offer->careers->map(fn ($c) => ['id' => $c->id, 'nombre' => $c->nombre])->all(),
        ];

        if ($detail) {
            $data += [
                'perfil' => $offer->perfil,
                'nivel_requerido' => $offer->nivel_requerido,
                'promedio_minimo' => $offer->min_gpa !== null ? (float) $offer->min_gpa : null,
                'horario' => $offer->horario,
                'remuneracion' => $offer->remuneracion !== null ? (float) $offer->remuneracion : null,
                'habilidades' => $offer->habilidades ?? [],
                'rubro_empresa' => $offer->company?->rubro,
            ];
        }

        return $data;
    }
}
