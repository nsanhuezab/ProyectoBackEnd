<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Postulacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostulacionController extends Controller
{
    /** Aceptar o rechazar una postulación recibida por la empresa. */
    public function estado(Request $request, string $id): RedirectResponse
    {
        $empresaId = $request->user()->empresa?->id;

        $postulacion = Postulacion::whereHas('oferta', fn ($q) => $q->where('empresa_id', $empresaId ?? 0))
            ->findOrFail($id);

        $data = $request->validate(['estado' => ['required', Rule::in(['aceptada', 'rechazada'])]]);

        $postulacion->update($data);

        return back()->with('status', 'Postulación '.$data['estado'].'.');
    }
}
