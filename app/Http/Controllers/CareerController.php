<?php

namespace App\Http\Controllers;

use App\Http\Requests\CareerRequest;
use App\Models\Career;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Mantenedor de carreras.
 */
class CareerController extends Controller
{
    /**
     * Lista de carreras con sus totales.
     */
    public function index(): View
    {
        $careers = Career::query()
            ->withCount(['students', 'offers', 'users'])
            ->orderBy('nombre')
            ->get();

        $counts = [
            'total' => $careers->count(),
            'activas' => $careers->where('activa', true)->count(),
            'inactivas' => $careers->where('activa', false)->count(),
        ];

        return view('careers.index', compact('careers', 'counts'));
    }

    /**
     * Carreras para los select de otros formularios.
     */
    public function options(): JsonResponse
    {
        return response()->json(
            Career::orderBy('nombre')->get(['id', 'nombre'])
        );
    }

    public function store(CareerRequest $request): RedirectResponse
    {
        $career = Career::create($request->validated());

        return redirect()
            ->route('careers.index')
            ->with('success', "Carrera «{$career->nombre}» creada correctamente.");
    }

    public function update(CareerRequest $request, Career $career): RedirectResponse
    {
        $career->update($request->validated());

        return redirect()
            ->route('careers.index')
            ->with('success', "Carrera «{$career->nombre}» actualizada correctamente.");
    }

    /**
     * Elimina la carrera si no tiene estudiantes, ofertas ni usuarios asociados.
     */
    public function destroy(Career $career): RedirectResponse
    {
        $career->loadCount(['students', 'offers', 'users']);

        if ($career->students_count > 0 || $career->offers_count > 0 || $career->users_count > 0) {
            return redirect()
                ->route('careers.index')
                ->with('error', "No se puede eliminar «{$career->nombre}» porque tiene {$career->students_count} estudiante(s), {$career->offers_count} oferta(s) y {$career->users_count} usuario(s) asociados. Puedes marcarla como inactiva.");
        }

        $career->delete();

        return redirect()
            ->route('careers.index')
            ->with('success', "Carrera «{$career->nombre}» eliminada correctamente.");
    }
}
