<?php

namespace App\Http\Controllers\Estudiante;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

/**
 * "Seguimiento": avance de la práctica y evaluaciones del estudiante.
 */
class SeguimientoController extends Controller
{
    public function __invoke(Request $request): View
    {
        $estudiante = $request->user()->estudiante;

        $practicas = $estudiante
            ? $estudiante->practicas()->with(['empresa', 'evaluaciones.evaluador'])->latest('fecha_inicio')->get()
            : collect();

        $notas = $practicas->flatMap->evaluaciones->pluck('nota');

        return view('estudiante.seguimiento', [
            'estudiante' => $estudiante,
            'practicas' => $practicas,
            'promedio' => $notas->isNotEmpty() ? $notas->avg() : null,
        ]);
    }
}
