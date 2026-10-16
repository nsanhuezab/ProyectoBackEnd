<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\OfferRequest;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Para conectar la tabla real de ofertas: cambiar el modelo/consulta en index()
 * y ajustar format(). El resto (búsqueda, orden, paginación) no se toca.
 */
class OfferController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            $request,
            Offer::query(),
            searchable: ['titulo', 'empresa', 'estado'],
            sortable: [
                'id' => 'id',
                'titulo' => 'titulo',
                'empresa' => 'empresa',
                'vacantes' => 'vacantes',
                'estado' => 'estado',
            ],
            transform: fn (Offer $o) => $this->format($o),
            defaultSort: 'id',
            defaultDir: 'desc',
        );
    }

    public function store(OfferRequest $request): JsonResponse
    {
        $offer = Offer::create($request->validated());

        return response()->json($this->format($offer), 201);
    }

    public function update(OfferRequest $request, Offer $offer): JsonResponse
    {
        $offer->update($request->validated());

        return response()->json($this->format($offer));
    }

    public function destroy(Offer $offer): Response
    {
        $offer->delete();

        return response()->noContent();
    }

    private function format(Offer $o): array
    {
        return [
            'id' => $o->id,
            'titulo' => $o->titulo,
            'empresa' => $o->empresa,
            'vacantes' => (int) $o->vacantes,
            'estado' => $o->estado,
        ];
    }
}
