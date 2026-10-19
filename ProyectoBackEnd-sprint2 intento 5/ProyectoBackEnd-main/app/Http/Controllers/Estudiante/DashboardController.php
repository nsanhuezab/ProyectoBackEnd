<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $estudiante = $request->user()->estudiante?->load('carrera');

        $postulaciones = $estudiante?->postulaciones()->with('oferta.empresa')->latest()->get() ?? collect();
        $practica = $estudiante?->practicas()->with(['empresa', 'evaluaciones'])->latest('fecha_inicio')->first();

        return view('estudiante.dashboard', [
            'estudiante' => $estudiante,
            'practica' => $practica,
            'postulaciones' => $postulaciones->take(5),
            'stats' => [
                'total' => $postulaciones->count(),
                'pendientes' => $postulaciones->where('estado', 'pendiente')->count(),
                'aceptadas' => $postulaciones->where('estado', 'aceptada')->count(),
                'ofertas' => Oferta::abiertas()->count(),
            ],
        ]);
    }
}
