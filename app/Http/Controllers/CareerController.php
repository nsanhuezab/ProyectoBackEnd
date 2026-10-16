<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CareerController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            $request,
            Career::query(),
            searchable: ['nombre', 'codigo'],
            sortable: [
                'id' => 'id',
                'nombre' => 'nombre',
                'codigo' => 'codigo',
                'supervisor' => 'supervisor',
                'coordinador' => 'coordinador',
                'activa' => 'activa',
            ],
            transform: fn (Career $c) => $this->format($c),
            defaultSort: 'id',
            defaultDir: 'desc',
        );
    }

    public function options(): JsonResponse
    {
        return response()->json(
            Career::orderBy('nombre')->get(['id', 'nombre'])
        );
    }

    public function store(CareerRequest $request): JsonResponse
    {
        $career = Career::create($request->validated());

        return response()->json($this->format($career), 201);
    }

    public function update(CareerRequest $request, Career $career): JsonResponse
    {
        $career->update($request->validated());

        return response()->json($this->format($career));
    }

    public function destroy(Career $career): Response
    {
        $career->delete();

        return response()->noContent();
    }

    private function format(Career $c): array
    {
        return [
            'id' => $c->id,
            'nombre' => $c->nombre,
            'codigo' => $c->codigo,
            'supervisor' => (float) $c->supervisor,
            'coordinador' => (float) $c->coordinador,
            'activa' => (bool) $c->activa,
        ];
    }
}
