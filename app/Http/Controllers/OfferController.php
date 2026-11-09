<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\OfferRequest;
use App\Models\Career;
use App\Models\Company;
use App\Models\Offer;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{
    use HasDataTable;

    /**
     * Mantenedor de ofertas. La misma vista sirve a tres roles:
     * - Administrador: ve y gestiona todas, y puede aprobarlas.
     * - Coordinador: ve las de su carrera y solo puede aprobarlas o rechazarlas.
     * - Empresa: ve y gestiona solo las suyas.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCompany = (bool) $user?->hasRole('Empresa');
        $ownCompany = $isCompany ? Company::where('owner_user_id', $user->id)->first() : null;

        $query = Offer::query()
            ->with(['company:id,razon_social', 'careers:id,codigo,nombre'])
            ->withCount([
                'applications',
                'applications as assigned_count' => fn ($applications) => $applications->whereHas('practice'),
            ])
            ->latest();

        if ($isCompany) {
            $query->where('empresa_id', $ownCompany?->id ?? 0);
        }
        if ($user?->hasRole('Coordinador')) {
            $query->whereHas('careers', fn ($career) => $career->whereKey($user->career_id));
        }

        $offers = $query->get();

        $counts = [
            'total' => $offers->count(),
            'pendientes' => $offers->where('estado', 'Pendiente')->count(),
            'aprobadas' => $offers->where('estado', 'Aprobada')->count(),
            'rechazadas' => $offers->where('estado', 'Rechazada')->count(),
        ];

        $can = [
            'manage' => (bool) $user?->hasAnyRole(['Empresa', 'Administrador', 'Super Administrador']),
            'approve' => (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador', 'Coordinador']),
            'chooseCompany' => ! $isCompany,
        ];

        $companies = $isCompany ? collect() : Company::query()->orderBy('razon_social')->get(['id', 'razon_social']);
        $careers = Career::query()->where('activa', true)->orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        return view('offers.index', compact('offers', 'counts', 'can', 'companies', 'careers', 'isCompany', 'ownCompany'));
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $company = $this->companyFor($request, (int) $data['empresa_id']);

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

            return $offer;
        });

        $this->notifyPendingReview($offer, 'Nueva oferta por aprobar');

        return back()->with('success', "Oferta «{$offer->titulo}» creada. Queda pendiente de aprobación.");
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOfferManagement($request, $offer);
        $data = $request->validated();
        $company = $this->companyFor($request, (int) $data['empresa_id']);

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

        $this->notifyPendingReview($offer, 'Oferta modificada por aprobar');

        return back()->with('success', "Oferta «{$offer->titulo}» actualizada. Vuelve a quedar pendiente de aprobación.");
    }

    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOfferManagement($request, $offer);

        $applications = $offer->applications()->count();
        if ($applications > 0) {
            return back()->with('error', "No se puede eliminar «{$offer->titulo}» porque tiene {$applications} postulación(es).");
        }

        $offer->delete();

        return back()->with('success', "Oferta «{$offer->titulo}» eliminada correctamente.");
    }

    /**
     * Aprueba o rechaza una oferta (Administrador o Coordinador de una de sus carreras).
     */
    public function approve(Request $request, Offer $offer): RedirectResponse
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

        $message = $validated['estado'] === 'Aprobada'
            ? "Oferta «{$offer->titulo}» aprobada. Ya es visible para los estudiantes."
            : "Oferta «{$offer->titulo}» rechazada.";

        $offer->loadMissing('company.owner');
        Notifier::send(
            $offer->company?->owner,
            $validated['estado'] === 'Aprobada' ? 'Oferta aprobada' : 'Oferta rechazada',
            $validated['estado'] === 'Aprobada'
                ? "Tu oferta «{$offer->titulo}» fue aprobada y ya la ven los estudiantes."
                : "Tu oferta «{$offer->titulo}» fue rechazada por la coordinación.",
            route('offers.index'),
        );

        return back()->with('success', $message);
    }

    /**
     * Avisa a los coordinadores de las carreras de la oferta que hay una oferta por revisar.
     */
    private function notifyPendingReview(Offer $offer, string $title): void
    {
        Notifier::send(
            Notifier::coordinatorsOf($offer->careers()->pluck('careers.id')),
            $title,
            "«{$offer->titulo}» de {$offer->empresa} espera tu aprobación.",
            route('offers.index'),
        );
    }

    /**
     * Datos en JSON para la vista "Explorar ofertas" del estudiante (solo ofertas aprobadas).
     */
    public function studentData(Request $request): JsonResponse
    {
        $query = Offer::query()
            ->with(['company:id,razon_social', 'careers:id,nombre'])
            ->where('estado', 'Aprobada');

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
            transform: fn (Offer $offer) => $this->format($offer),
            defaultSort: 'id',
            defaultDir: 'desc',
            extraSearch: fn ($query, $like) => $query
                ->orWhereHas('company', fn ($company) => $company->where('razon_social', 'like', $like))
                ->orWhereHas('careers', fn ($career) => $career->where('nombre', 'like', $like)),
        );
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
        abort_unless($request->user()->hasAnyRole(['Empresa', 'Administrador', 'Super Administrador']), 403);
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

    /**
     * @return array<string, mixed>
     */
    private function format(Offer $offer): array
    {
        return [
            'id' => $offer->id,
            'titulo' => $offer->titulo,
            'empresa' => $offer->empresa,
            'empresa_id' => $offer->empresa_id,
            'vacantes' => (int) $offer->vacantes,
            'estado' => $offer->estado,
            'area' => $offer->area,
            'perfil' => $offer->perfil,
            'nivel_requerido' => $offer->nivel_requerido,
            'modalidad' => $offer->modalidad,
            'duracion_semanas' => $offer->duracion_semanas,
            'horario' => $offer->horario,
            'remuneracion' => $offer->remuneracion,
            'habilidades' => $offer->habilidades ?? [],
            'ubicacion' => $offer->ubicacion,
            'career_ids' => $offer->careers->pluck('id')->all(),
            'carreras' => $offer->careers->pluck('nombre')->all(),
        ];
    }
}
