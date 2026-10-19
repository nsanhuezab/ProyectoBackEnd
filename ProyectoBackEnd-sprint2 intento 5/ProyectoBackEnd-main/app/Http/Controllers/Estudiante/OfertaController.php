<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Models\Postulacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * "Explorar Ofertas": catálogo de ofertas abiertas y postulación.
 */
class OfertaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $modalidad = $request->query('modalidad');
        $estudiante = $request->user()->estudiante;

        $ofertas = Oferta::abiertas()->with('empresa')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.addcslashes($q, '%_\\').'%';
                $query->where(fn ($w) => $w->where('titulo', 'like', $like)
                    ->orWhere('area', 'like', $like)
                    ->orWhereHas('empresa', fn ($e) => $e->where('razon_social', 'like', $like)));
            })
            ->when(array_key_exists((string) $modalidad, Oferta::MODALIDADES), fn ($query) => $query->where('modalidad', $modalidad))
            ->latest()->paginate(9)->withQueryString();

        view()->share('searchUrl', route('estudiante.ofertas.index'));

        return view('estudiante.ofertas.index', [
            'ofertas' => $ofertas,
            'q' => $q,
            'modalidad' => $modalidad,
            'postuladas' => $estudiante ? $estudiante->postulaciones()->pluck('oferta_id')->all() : [],
        ]);
    }

    public function show(Request $request, string $id): View
    {
        $oferta = Oferta::abiertas()->with('empresa')->findOrFail($id);
        $estudiante = $request->user()->estudiante;

        return view('estudiante.ofertas.show', [
            'oferta' => $oferta,
            'estudiante' => $estudiante,
            'yaPostulo' => $estudiante?->postulaciones()->where('oferta_id', $oferta->id)->exists() ?? false,
        ]);
    }

    public function postular(Request $request, string $id): RedirectResponse
    {
        $oferta = Oferta::abiertas()->findOrFail($id);
        $estudiante = $request->user()->estudiante;

        if (! $estudiante) {
            return back()->with('error', 'Tu perfil de estudiante aún no está registrado. Contacta a coordinación.');
        }

        $data = $request->validate(['mensaje' => ['nullable', 'string', 'max:2000']], [], ['mensaje' => 'mensaje']);

        if ($estudiante->postulaciones()->where('oferta_id', $oferta->id)->exists()) {
            return back()->with('error', 'Ya postulaste a esta oferta.');
        }

        Postulacion::create([
            'oferta_id' => $oferta->id,
            'estudiante_id' => $estudiante->id,
            'mensaje' => $data['mensaje'] ?? null,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('estudiante.postulaciones.index')
            ->with('status', 'Postulación enviada correctamente.');
    }
}
