<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPractices;
use App\Http\Requests\PracticeRequest;
use App\Models\Application;
use App\Models\Offer;
use App\Models\Practice;
use App\Models\PracticeEvaluation;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prácticas: mantenedor, cambio de estado y acta.
 */
class PracticeController extends Controller
{
    use AuthorizesPractices;

    /**
     * Lista de prácticas. El coordinador solo ve las de su carrera.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCoordinator = (bool) $user?->hasRole('Coordinador');
        $byCareer = fn (Builder $student) => $student->where('career_id', $user->career_id);
        $states = config('practices.states');

        $query = Practice::query()
            ->with([
                'application.student.user:id,name,last_name,email',
                'application.student.career:id,codigo,nombre',
                'application.offer:id,titulo,empresa,empresa_id',
                'application.offer.company:id,razon_social',
            ])
            ->withCount(['logs', 'evaluations'])
            ->latest();

        if ($isCoordinator) {
            $query->whereHas('application.student', $byCareer);
        }

        $practices = $query->get();

        $counts = ['total' => $practices->count()];
        foreach ($states as $state) {
            $counts[$state] = $practices->where('estado', $state)->count();
        }

        // Postulaciones pendientes para el modal "Nueva práctica"
        $pendingApplications = Application::query()
            ->with(['student.user:id,name,last_name', 'offer:id,titulo,empresa'])
            ->where('status', 'Postulado')
            ->when($isCoordinator, fn ($applications) => $applications->whereHas('student', $byCareer))
            ->orderByDesc('matching_score')
            ->get();

        $canDelete = $this->isAdmin($user);

        return view('practices.index', compact('practices', 'counts', 'pendingApplications', 'canDelete', 'states'));
    }

    /**
     * Detalle de la práctica.
     */
    public function show(Request $request, Practice $practice): View
    {
        $this->loadPracticeRelations($practice);
        $user = $request->user();
        abort_unless($this->canViewPractice($user, $practice), 403);

        $can = [
            'evaluateSupervisor' => $this->canEvaluate($user, $practice, PracticeEvaluation::SUPERVISOR),
            'evaluateCoordinator' => $this->canEvaluate($user, $practice, PracticeEvaluation::COORDINATOR),
            'manage' => $this->isAdmin($user) || $this->isCareerCoordinator($user, $practice),
            'seeStudentContact' => ! $this->isCompanySupervisor($user, $practice) || $this->isAdmin($user),
        ];

        return view('practices.show', [
            'practice' => $practice,
            'states' => config('practices.states'),
            'competencies' => config('practices.competencies'),
            'can' => $can,
        ]);
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

            $practice = Practice::create([
                'application_id' => $application->id,
                'assigned_by' => $request->user()->id,
                'fecha_inicio' => $request->validated('fecha_inicio'),
                'fecha_termino' => $request->validated('fecha_termino'),
                'estado' => 'Asignado',
            ]);

            Notifier::send(
                $application->student?->user,
                'Práctica asignada',
                "Quedaste asignado a la práctica «{$offer->titulo}».",
                route('student.practice.index'),
            );

            return null;
        });

        if ($error) {
            return back()->withInput()->with('error', $error);
        }

        return back()->with('success', 'Práctica creada en estado «Asignado».');
    }

    /**
     * Actualiza las fechas y si el estudiante fue contratado.
     */
    public function update(PracticeRequest $request, Practice $practice): RedirectResponse
    {
        $this->authorizeManagement($request, $practice);

        $data = $request->safe()->only(['fecha_inicio', 'fecha_termino']);
        if ($request->has('hired')) {
            $data['hired'] = $request->filled('hired') ? $request->boolean('hired') : null;
        }
        $practice->update($data);

        return back()->with('success', 'Datos de la práctica actualizados.');
    }

    /**
     * Elimina la práctica y devuelve la postulación a "Postulado".
     */
    public function destroy(Request $request, Practice $practice): RedirectResponse
    {
        abort_unless($this->isAdmin($request->user()), 403);

        DB::transaction(function () use ($practice): void {
            $practice->application()->update(['status' => 'Postulado', 'reviewed_by' => null, 'decision_at' => null]);
            $practice->delete();
        });

        return back()->with('success', 'Práctica eliminada. La postulación volvió a quedar pendiente.');
    }

    /**
     * Avanza la práctica al siguiente estado. Para pasar a "Evaluado" se necesitan las dos rúbricas.
     */
    public function transition(Request $request, Practice $practice): RedirectResponse
    {
        $this->authorizeManagement($request, $practice);
        $states = config('practices.states');
        $validated = $request->validate([
            'estado' => ['required', Rule::in(array_slice($states, 1))],
        ]);

        $current = array_search($practice->estado, $states, true);
        $next = $current === false ? null : ($states[$current + 1] ?? null);

        if ($next !== $validated['estado']) {
            return back()->with('error', 'La transición solicitada no está permitida.');
        }

        if ($next === 'Evaluado') {
            $finalGrade = $practice->calculateFinalGrade();
            if ($finalGrade === null) {
                return back()->with('error', 'Para pasar a «Evaluado» faltan rúbricas: deben evaluar el supervisor de la empresa y el coordinador.');
            }
            $practice->final_grade = $finalGrade;
        }

        if ($next === 'Finalizada') {
            $practice->fecha_termino ??= today();
        }

        $this->moveTo($practice, $next);

        return back()->with('success', "La práctica pasó a «{$next}».");
    }

    /**
     * Descarga el acta de cierre en PDF (requiere la nota final).
     */
    public function acta(Request $request, Practice $practice): Response
    {
        $this->loadPracticeRelations($practice);
        abort_unless($this->canViewPractice($request->user(), $practice), 403);

        if ($practice->final_grade === null) {
            return back()->with('error', 'El acta se genera cuando la práctica tiene sus dos evaluaciones y su nota final.');
        }

        $data = [
            'practice' => $practice,
            'competencies' => config('practices.competencies'),
            'generatedAt' => now(),
        ];
        $fileName = 'acta-cierre-practica-'.$practice->id.'.pdf';

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('practices.acta', $data)->download($fileName);
        }

        // Respaldo si no está DomPDF: versión para imprimir
        return response()->view('practices.acta', $data + ['printable' => true]);
    }

    /**
     * Cambia el estado de la práctica y de su postulación, y avisa al estudiante.
     */
    private function moveTo(Practice $practice, string $state): void
    {
        DB::transaction(function () use ($practice, $state): void {
            $practice->estado = $state;
            $practice->save();
            $practice->application()->update(['status' => $state]);
        });

        $practice->loadMissing('application.student.user', 'application.offer');
        Notifier::send(
            $practice->application?->student?->user,
            'Tu práctica cambió de estado',
            "Tu práctica «{$practice->application?->offer?->titulo}» ahora está en «{$state}».",
            route('student.practice.index'),
        );
    }

    private function authorizeManagement(Request $request, Practice $practice): void
    {
        $practice->loadMissing('application.student');
        abort_unless($this->isAdmin($request->user()) || $this->isCareerCoordinator($request->user(), $practice), 403);
    }
}
