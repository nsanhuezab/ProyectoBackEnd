<?php

namespace App\Http\Controllers;

use App\Http\Requests\PracticeRequest;
use App\Models\Application;
use App\Models\Offer;
use App\Models\Practice;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PracticeController extends Controller
{
    /**
     * Flujo de estados de una práctica: cada estado solo puede avanzar al siguiente.
     */
    public const STATES = ['Asignado', 'En Curso', 'Informe Entregado', 'Finalizada'];

    /**
     * Prácticas para Administrador (todas) y Coordinador (solo las de su carrera).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCoordinator = (bool) $user?->hasRole('Coordinador');
        $byCareer = fn (Builder $student) => $student->where('career_id', $user->career_id);

        $query = Practice::query()
            ->with([
                'application.student.user:id,name,last_name,email',
                'application.student.career:id,codigo,nombre',
                'application.offer:id,titulo,empresa,empresa_id',
                'application.offer.company:id,razon_social',
            ])
            ->latest();

        if ($isCoordinator) {
            $query->whereHas('application.student', $byCareer);
        }

        $practices = $query->get();

        $counts = ['total' => $practices->count()];
        foreach (self::STATES as $state) {
            $counts[$state] = $practices->where('estado', $state)->count();
        }

        // Postulaciones pendientes que se pueden convertir en práctica desde "Nueva práctica".
        $pendingApplications = Application::query()
            ->with(['student.user:id,name,last_name', 'offer:id,titulo,empresa'])
            ->where('status', 'Postulado')
            ->when($isCoordinator, fn ($applications) => $applications->whereHas('student', $byCareer))
            ->orderByDesc('matching_score')
            ->get();

        $canDelete = (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador']);
        $states = self::STATES;

        return view('practices.index', compact('practices', 'counts', 'pendingApplications', 'canDelete', 'states'));
    }

    /**
     * Crea una práctica a partir de una postulación pendiente.
     */
    public function store(PracticeRequest $request): RedirectResponse
    {
        $error = DB::transaction(function () use ($request): ?string {
            $application = Application::query()->lockForUpdate()->findOrFail($request->validated('application_id'));
            $offer = Offer::query()->lockForUpdate()->findOrFail($application->offer_id);

            if ($request->user()->hasRole('Coordinador')) {
                abort_unless($application->student()->where('career_id', $request->user()->career_id)->exists(), 403);
            }
            if ($application->status !== 'Postulado') {
                return 'Solo puedes asignar una postulación pendiente.';
            }
            if ($offer->applications()->whereHas('practice')->count() >= $offer->vacantes) {
                return "La oferta «{$offer->titulo}» ya completó sus cupos.";
            }

            $application->update([
                'status' => 'Asignado',
                'reviewed_by' => $request->user()->id,
                'decision_at' => now(),
            ]);

            Practice::create([
                'application_id' => $application->id,
                'assigned_by' => $request->user()->id,
                'fecha_inicio' => $request->validated('fecha_inicio'),
                'fecha_termino' => $request->validated('fecha_termino'),
                'estado' => 'Asignado',
            ]);

            return null;
        });

        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        return back()->with('success', 'Práctica creada en estado «Asignado».');
    }

    /**
     * Solo se editan las fechas; el estado avanza con transition().
     */
    public function update(PracticeRequest $request, Practice $practice): RedirectResponse
    {
        $this->authorizePractice($request, $practice);
        $practice->update($request->safe()->only(['fecha_inicio', 'fecha_termino']));

        return back()->with('success', 'Fechas de la práctica actualizadas.');
    }

    /**
     * Elimina la práctica y devuelve la postulación a "Postulado".
     */
    public function destroy(Request $request, Practice $practice): RedirectResponse
    {
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador']), 403);

        DB::transaction(function () use ($practice): void {
            $practice->application()->update(['status' => 'Postulado', 'reviewed_by' => null, 'decision_at' => null]);
            $practice->delete();
        });

        return back()->with('success', 'Práctica eliminada. La postulación volvió a quedar pendiente.');
    }

    /**
     * Avanza la práctica al siguiente estado del flujo.
     */
    public function transition(Request $request, Practice $practice): RedirectResponse
    {
        $this->authorizePractice($request, $practice);
        $validated = $request->validate([
            'estado' => ['required', Rule::in(array_slice(self::STATES, 1))],
        ]);

        $current = array_search($practice->estado, self::STATES, true);
        $next = $current === false ? null : (self::STATES[$current + 1] ?? null);

        if ($next !== $validated['estado']) {
            return back()->with('error', 'La transición solicitada no está permitida.');
        }

        if ($next === 'Finalizada') {
            $practice->fecha_termino ??= today();
        }

        DB::transaction(function () use ($practice, $next): void {
            $practice->estado = $next;
            $practice->save();
            $practice->application()->update(['status' => $next]);
        });

        return back()->with('success', "La práctica pasó a «{$next}».");
    }

    private function authorizePractice(Request $request, Practice $practice): void
    {
        $user = $request->user();
        if ($user->hasAnyRole(['Administrador', 'Super Administrador'])) {
            return;
        }

        abort_unless(
            $user->hasRole('Coordinador')
                && $practice->application()->whereHas('student', fn (Builder $student) => $student->where('career_id', $user->career_id))->exists(),
            403,
        );
    }
}
