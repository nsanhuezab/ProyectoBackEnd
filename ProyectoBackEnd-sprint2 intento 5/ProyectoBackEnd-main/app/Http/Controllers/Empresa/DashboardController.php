<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Postulacion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $empresa = $request->user()->empresa;

        $postulaciones = $empresa
            ? Postulacion::with(['oferta', 'estudiante.user', 'estudiante.carrera'])
                ->whereHas('oferta', fn ($q) => $q->where('empresa_id', $empresa->id))
            : null;

        return view('empresa.dashboard', [
            'empresa' => $empresa,
            'stats' => [
                'ofertas' => $empresa?->ofertas()->count() ?? 0,
                'abiertas' => $empresa?->ofertas()->where('estado', 'abierta')->count() ?? 0,
                'postulaciones' => $postulaciones?->count() ?? 0,
                'practicantes' => $empresa?->practicas()->where('estado', 'en_curso')->count() ?? 0,
            ],
            'recientes' => $postulaciones ? (clone $postulaciones)->latest()->limit(6)->get() : collect(),
        ]);
    }
}
