<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Career;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API pública 2: directorio de carreras y empresas (sin datos de contacto privados).
 */
class PublicDirectoryController extends ApiController
{
    public function careers(): JsonResponse
    {
        $careers = Career::query()
            ->where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre']);

        return response()->json(['data' => $careers]);
    }

    public function companies(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'max:120'],
            'rubro' => ['nullable', 'string', 'max:120'],
        ]);

        $query = $this->baseQuery();

        if ($request->filled('search')) {
            $like = '%'.$request->string('search')->toString().'%';
            $query->where(fn ($q) => $q->where('razon_social', 'like', $like)->orWhere('rubro', 'like', $like));
        }
        if ($request->filled('rubro')) {
            $query->where('rubro', 'like', '%'.$request->string('rubro')->toString().'%');
        }

        $page = $query->orderBy('razon_social')->paginate($this->perPage($request));

        return $this->paginated($page, fn (Company $company) => $this->format($company));
    }

    public function company(int $id): JsonResponse
    {
        $company = $this->baseQuery()
            ->with(['reviews' => fn ($q) => $q->latest()->limit(10)])
            ->find($id);

        if (! $company) {
            return $this->notFound('Empresa');
        }

        $data = $this->format($company);
        $data['ofertas'] = $company->offers()
            ->where('estado', 'Aprobada')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'titulo', 'modalidad', 'ubicacion'])
            ->all();
        $data['resenas'] = $company->reviews->map(fn ($r) => [
            'valoracion' => $r->rating,
            'comentario' => $r->comment,
            'fecha' => $r->created_at?->toDateString(),
        ])->all();

        return response()->json(['data' => $data]);
    }

    private function baseQuery()
    {
        return Company::query()
            ->withCount([
                'offers as ofertas_aprobadas' => fn ($q) => $q->where('estado', 'Aprobada'),
                'reviews as total_resenas',
            ])
            ->withAvg('reviews as rating_promedio', 'rating');
    }

    private function format(Company $company): array
    {
        return [
            'id' => $company->id,
            'razon_social' => $company->razon_social,
            'rubro' => $company->rubro,
            'sitio_web' => $company->sitio_web,
            'direccion' => $company->direccion,
            'rating_promedio' => $company->rating_promedio !== null ? round((float) $company->rating_promedio, 1) : null,
            'total_resenas' => (int) $company->total_resenas,
            'ofertas_aprobadas' => (int) $company->ofertas_aprobadas,
        ];
    }
}
