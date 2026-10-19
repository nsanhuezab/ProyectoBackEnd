<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Empresa;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Oferta;
use App\Models\Postulacion;
use App\Models\Practica;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Dashboard general. Estudiantes y empresas son enviados a su propio panel.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'estudiante') {
            return redirect()->route('estudiante.dashboard');
        }

        if ($user->role === 'empresa') {
            return redirect()->route('empresa.dashboard');
        }

        return view('dashboard', [
            'modulos' => [
                ['Usuarios', User::count(), 'admin.usuarios.index', 'users', $user->isAdmin()],
                ['Carreras', Carrera::count(), 'admin.carreras.index', 'academic-cap', true],
                ['Empresas', Empresa::count(), 'admin.empresas.index', 'building', true],
                ['Estudiantes', Estudiante::count(), 'admin.estudiantes.index', 'users', true],
                ['Ofertas de práctica', Oferta::count(), 'admin.ofertas.index', 'briefcase', true],
                ['Postulaciones', Postulacion::count(), 'gestion.postulaciones.index', 'document', true],
                ['Prácticas', Practica::count(), 'gestion.practicas.index', 'clipboard-check', true],
                ['Evaluaciones', Evaluacion::count(), 'gestion.evaluaciones.index', 'star', true],
            ],
        ]);
    }
}
