<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\OfferRequest;
use App\Models\Company;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Para conectar la tabla real de ofertas: cambiar el modelo/consulta en index()
 * y ajustar format(). El resto (búsqueda, orden, paginación) no se toca.
 */
class OfferController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        $query = Offer::query()->with(['company:id,razon_social', 'careers:id,nombre']);
        $user = $request->user();

        if ($user?->hasRole('Empresa')) {
            $query->whereHas('company', fn ($company) => $company->where('owner_user_id', $user->id));
        }
        if ($user?->hasRole('Coordinador')) {
            $query->whereHas('careers', fn ($career) => $career->whereKey($user->career_id));
        }
        if ($request->filled('career_id')) {
            $query->whereHas('careers', fn ($career) => $career->whereKey($request->integer('career_id')));
        }
        if ($request->filled('area')) {
            $query->where('area', $request->string('area')->toString());
        }
        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->string('modalidad')->toString());
        }
        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', 'like', '%'.$request->string('ubicacion')->toString().'%');
        }
        if ($user?->hasRole('Estudiante')) {
            $query->where('estado', 'Aprobada');
        }

        return $this->dataTableResponse(
            $request,
            $query,
            searchable: ['titulo', 'empresa', 'estado'],
            sortable: [
                'id' => 'id',
                'titulo' => 'titulo',
                'empresa' => 'empresa',
                'vacantes' => 'vacantes',
                'estado' => 'estado',
            ],
            transform: fn (Offer $o) => $this->format($o),
            defaultSort: 'id',
            defaultDir: 'desc',
            extraSearch: fn ($query, $like) => $query
                ->orWhereHas('company', fn ($company) => $company->where('razon_social', 'like', $like))
                ->orWhereHas('careers', fn ($career) => $career->where('nombre', 'like', $like)),
        );
    }

    public function store(OfferRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = $this->companyFor($request, $data['company_id']);
        $offer = DB::transaction(function () use ($request, $data, $company): Offer {
            $offer = Offer::create(array_merge(
                Arr::except($data, ['career_ids', 'empresa']),
                [
                    'empresa' => $company->razon_social,
                    'created_by' => $request->user()->id,
                    'estado' => 'Pendiente',
                ],
            ));
            $offer->careers()->sync($data['career_ids']);

            return $offer->load(['company', 'careers']);
        });

        return response()->json($this->format($offer), 201);
    }

    public function update(OfferRequest $request, Offer $offer): JsonResponse
    {
        $data = $request->validated();
        $company = $this->companyFor($request, $data['company_id']);
        $this->authorizeOfferManagement($request, $offer);

        DB::transaction(function () use ($data, $company, $offer): void {
            $offer->update(array_merge(
                Arr::except($data, ['career_ids', 'empresa']),
                [
                    'empresa' => $company->razon_social,
                    'estado' => 'Pendiente',
                    'approved_by' => null,
                    'approved_at' => null,
                ],
            ));
            $offer->careers()->sync($data['career_ids']);
        });

        return response()->json($this->format($offer->refresh()->load(['company', 'careers'])));
    }

    public function destroy(Request $request, Offer $offer): Response
    {
        $this->authorizeOfferManagement($request, $offer);
        abort_if($offer->applications()->exists(), 409, 'No puedes eliminar una oferta con postulaciones.');
        $offer->delete();

        return response()->noContent();
    }

    public function approve(Request $request, Offer $offer): JsonResponse
    {
        $this->authorizeOfferApproval($request, $offer);
        $validated = $request->validate([
            'estado' => ['required', Rule::in(['Aprobada', 'Rechazada'])],
        ]);
        $offer->update([
            'estado' => $validated['estado'],
            'approved_by' => $request->user()->id,
            'approved_at' => $validated['estado'] === 'Aprobada' ? now() : null,
        ]);

        return response()->json($this->format($offer->refresh()->load(['company', 'careers'])));
    }

    private function companyFor(Request $request, int $companyId): Company
    {
        $company = Company::findOrFail($companyId);
        if ($request->user()->hasRole('Empresa')) {
            abort_unless($company->owner_user_id === $request->user()->id, 403);
        }

        return $company;
    }

    private function authorizeOfferManagement(Request $request, Offer $offer): void
    {
        abort_unless(
            $request->user()->hasAnyRole(['Empresa', 'Administrador', 'Super Administrador']),
            403
        );
        if ($request->user()->hasRole('Empresa')) {
            abort_unless($offer->company?->owner_user_id === $request->user()->id, 403);
        }
    }

    private function authorizeOfferApproval(Request $request, Offer $offer): void
    {
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador', 'Coordinador']), 403);
        if ($request->user()->hasRole('Coordinador')) {
            abort_unless($offer->careers()->whereKey($request->user()->career_id)->exists(), 403);
        }
    }

    private function format(Offer $o): array
    {
        return [
            'id' => $o->id,
            'titulo' => $o->titulo,
            'empresa' => $o->empresa,
            'company_id' => $o->company_id,
            'vacantes' => (int) $o->vacantes,
            'estado' => $o->estado,
            'area' => $o->area,
            'perfil' => $o->perfil,
            'nivel_requerido' => $o->nivel_requerido,
            'modalidad' => $o->modalidad,
            'duracion_semanas' => $o->duracion_semanas,
            'horario' => $o->horario,
            'remuneracion' => $o->remuneracion,
            'habilidades' => $o->habilidades ?? [],
            'ubicacion' => $o->ubicacion,
            'career_ids' => $o->careers->pluck('id')->all(),
            'carreras' => $o->careers->pluck('nombre')->all(),
        ];
    }
}
