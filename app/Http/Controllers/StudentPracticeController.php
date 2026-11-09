<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPractices;
use App\Models\CompanyReview;
use App\Models\Practice;
use App\Models\PracticeLog;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * "Mi práctica": el estudiante sigue su práctica, registra la bitácora quincenal y evalúa a la empresa.
 */
class StudentPracticeController extends Controller
{
    use AuthorizesPractices;

    /**
     * Estados en los que el estudiante puede registrar bitácora.
     */
    private const LOG_STATES = ['En Curso', 'Informe Entregado'];

    /**
     * Estados en los que el estudiante puede evaluar a la empresa.
     */
    private const REVIEW_STATES = ['Evaluado', 'Finalizada'];

    public function index(Request $request): View
    {
        $practices = Practice::query()
            ->whereHas('application.student', fn ($student) => $student->where('user_id', $request->user()->id))
            ->with([
                'application.student.career',
                'application.offer.company',
                'evaluations',
                'logs',
                'review',
            ])
            ->latest()
            ->get();

        return view('student.practice', [
            'practices' => $practices,
            'states' => config('practices.states'),
            'logStates' => self::LOG_STATES,
            'reviewStates' => self::REVIEW_STATES,
            'periodDays' => config('practices.log_period_days'),
        ]);
    }

    /**
     * Registra un avance quincenal en la bitácora.
     */
    public function storeLog(Request $request, Practice $practice): RedirectResponse
    {
        $this->authorizeStudent($request, $practice);

        if (! in_array($practice->estado, self::LOG_STATES, true)) {
            return back()->with('error', 'La bitácora se registra mientras la práctica está «En Curso» o con el informe entregado.');
        }

        $maxDays = config('practices.log_period_days') + 2;
        $validated = $request->validate([
            'period_start' => ['required', 'date', 'before_or_equal:today'],
            'period_end' => [
                'required', 'date', 'after_or_equal:period_start',
                function (string $attribute, mixed $value, \Closure $fail) use ($request, $maxDays): void {
                    $start = strtotime((string) $request->input('period_start'));
                    $end = strtotime((string) $value);
                    if ($start && $end && ($end - $start) / 86400 > $maxDays) {
                        $fail('Cada registro cubre como máximo una quincena.');
                    }
                },
            ],
            'hours' => ['required', 'integer', 'min:1', 'max:120'],
            'activities' => ['required', 'string', 'min:20', 'max:3000'],
            'learnings' => ['nullable', 'string', 'max:3000'],
            'difficulties' => ['nullable', 'string', 'max:3000'],
        ], [], [
            'period_start' => 'inicio del período',
            'period_end' => 'fin del período',
            'hours' => 'horas trabajadas',
            'activities' => 'actividades realizadas',
            'learnings' => 'aprendizajes',
            'difficulties' => 'dificultades',
        ]);

        $practice->logs()->create($validated);

        $practice->loadMissing('application.student.user', 'application.offer');
        Notifier::send(
            Notifier::coordinatorsOf([$practice->application?->student?->career_id]),
            'Nueva bitácora registrada',
            trim(($practice->application?->student?->user?->name ?? 'Un estudiante').' '.($practice->application?->student?->user?->last_name ?? ''))." registró un avance en «{$practice->application?->offer?->titulo}».",
            route('practices.show', $practice),
        );

        return back()->with('success', 'Avance registrado en la bitácora.');
    }

    public function destroyLog(Request $request, PracticeLog $log): RedirectResponse
    {
        $this->authorizeStudent($request, $log->practice);

        if (! in_array($log->practice->estado, self::LOG_STATES, true)) {
            return back()->with('error', 'Ya no se puede modificar la bitácora de esta práctica.');
        }

        $log->delete();

        return back()->with('success', 'Registro de bitácora eliminado.');
    }

    /**
     * El estudiante evalúa a la empresa (indicador de calidad del centro de práctica).
     */
    public function storeReview(Request $request, Practice $practice): RedirectResponse
    {
        $this->authorizeStudent($request, $practice);

        if (! in_array($practice->estado, self::REVIEW_STATES, true)) {
            return back()->with('error', 'Podrás evaluar a la empresa cuando tu práctica esté evaluada.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ], [], ['rating' => 'calificación', 'comment' => 'comentario']);

        CompanyReview::updateOrCreate(
            ['practice_id' => $practice->id],
            [
                'company_id' => $practice->application->offer->empresa_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ],
        );

        return back()->with('success', '¡Gracias! Tu evaluación de la empresa quedó registrada.');
    }

    private function authorizeStudent(Request $request, Practice $practice): void
    {
        $practice->loadMissing('application.student', 'application.offer');
        abort_unless($this->isPracticeStudent($request->user(), $practice), 403);
    }
}
