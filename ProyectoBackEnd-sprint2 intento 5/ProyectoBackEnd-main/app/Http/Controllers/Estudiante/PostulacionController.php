<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * "Mis Postulaciones".
 */
class PostulacionController extends Controller
{
    public function index(Request $request): View
    {
        $estudiante = $request->user()->estudiante;

        $postulaciones = $estudiante
            ? $estudiante->postulaciones()->with('oferta.empresa')->latest()->paginate(10)
            : null;

        return view('estudiante.postulaciones', ['postulaciones' => $postulaciones]);
    }

    public function retirar(Request $request, string $id): RedirectResponse
    {
        $postulacion = $request->user()->estudiante?->postulaciones()->findOrFail($id);
        abort_if(! $postulacion, 404);

        if ($postulacion->estado !== 'pendiente') {
            return back()->with('error', 'Solo puedes retirar postulaciones pendientes.');
        }

        $postulacion->update(['estado' => 'retirada']);

        return back()->with('status', 'Postulación retirada.');
    }
}
