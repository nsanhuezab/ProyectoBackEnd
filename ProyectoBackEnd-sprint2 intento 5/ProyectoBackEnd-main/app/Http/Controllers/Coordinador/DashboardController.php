<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Postulacion;
use App\Models\Practica;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('coordinador.dashboard', [
            'stats' => [
                'en_curso' => Practica::where('estado', 'en_curso')->count(),
                'pendientes' => Postulacion::where('estado', 'pendiente')->count(),
                'sin_practica' => Estudiante::whereDoesntHave('practicas')->count(),
                'evaluaciones_mes' => Evaluacion::whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->count(),
            ],
            'practicas' => Practica::with(['estudiante.user', 'empresa'])->where('estado', 'en_curso')->orderBy('fecha_termino')->limit(6)->get(),
            'promedio' => Evaluacion::avg('nota'),
        ]);
    }
}
