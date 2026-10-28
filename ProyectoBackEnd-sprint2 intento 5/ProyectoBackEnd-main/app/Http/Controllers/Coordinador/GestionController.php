<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Postulacion;
use App\Models\Practica;
use Illuminate\View\View;

/**
 * Bandeja de trabajo del coordinador: lo que requiere su revisión.
 */
class GestionController extends Controller
{
    public function __invoke(): View
    {
        return view('coordinador.gestion', [
            'postulaciones' => Postulacion::with(['oferta.empresa', 'estudiante.user'])
                ->where('estado', 'pendiente')->latest()->limit(10)->get(),
            'sinEvaluar' => Practica::with(['estudiante.user', 'empresa'])
                ->whereIn('estado', ['en_curso', 'finalizada'])
                ->whereDoesntHave('evaluaciones')->orderBy('fecha_termino')->limit(10)->get(),
        ]);
    }
}
