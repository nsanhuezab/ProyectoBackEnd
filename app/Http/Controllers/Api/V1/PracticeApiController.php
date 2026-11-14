<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Practice;
use App\Models\PracticeLog;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API privada 2: prácticas, evaluaciones y bitácoras.
 */
class PracticeApiController extends ApiController
{
    /** Estados en que el estudiante puede registrar bitácora. */
    private const LOG_STATES = ['En Curso', 'Informe Entregado'];

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'estado' => ['nullable', 'string', 'max:40'],
        ]);

        $query = $this->scoped($request->user())->with($this->relations());

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado')->toString());
        }

        $page = $query->orderByDesc('id')->paginate($this->perPage($request));

        return $this->paginated($page, fn (Practice $p) => $this->format($p));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $practice = $this->scoped($request->user())
            ->with([...$this->relations(), 'logs', 'evaluations', 'review'])
            ->find($id);

        if (! $practice) {
            return $this->notFound('Práctica');
        }

        $data = $this->format($practice);
        $data['horas_registradas'] = (int) $practice->logs->sum('hours');
        $data['nota_final_calculada'] = $practice->calculateFinalGrade();
        $data['evaluaciones'] = $practice->evaluations->map(fn ($e) => [
            'rol' => $e->evaluator_role,
            'nota' => (float) $e->grade,
            'puntajes' => $e->scores,
            'comentarios' => $e->comments,
        ])->all();
        $data['resena_empresa'] = $practice->review
            ? ['valoracion' => $practice->review->rating, 'comentario' => $practice->review->comment]
            : null;

        return response()->json(['data' => $data]);
    }

    public function logs(Request $request, int $id): JsonResponse
    {
        $practice = $this->scoped($request->user())->with('logs')->find($id);

        if (! $practice) {
            return $this->notFound('Práctica');
        }

        return response()->json([
            'data' => $practice->logs->sortByDesc('period_start')->values()->map(fn (PracticeLog $l) => $this->formatLog($l))->all(),
            'meta' => ['total' => $practice->logs->count(), 'horas_totales' => (int) $practice->logs->sum('hours')],
        ]);
    }

    /**
     * Funcionalidad crítica: el estudiante registra su bitácora quincenal.
     */
    public function storeLog(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $practice = $this->scoped($user)->with('application.student.user', 'application.offer')->find($id);

        if (! $practice) {
            return $this->notFound('Práctica');
        }
        if ($practice->application?->student?->user_id !== $user->id) {
            return response()->json(['message' => 'Solo el estudiante de la práctica puede registrar su bitácora.'], 403);
        }
        if (! in_array($practice->estado, self::LOG_STATES, true)) {
            return response()->json(['message' => 'La bitácora se registra mientras la práctica está «En Curso» o con el informe entregado.'], 409);
        }

        $maxDays = (int) config('practices.log_period_days', 14) + 2;

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
        ]);

        $log = $practice->logs()->create($validated);

        Notifier::send(
            Notifier::coordinatorsOf([$practice->application?->student?->career_id]),
            'Nueva bitácora registrada',
            ($this->fullName($user) ?: 'Un estudiante').' registró un avance en «'.$practice->application?->offer?->titulo.'».',
            route('practices.show', $practice),
        );

        return response()->json(['data' => $this->formatLog($log)], 201);
    }

    // ------------------------------------------------------------------ helpers

    private function scoped(User $user): Builder
    {
        $query = Practice::query();

        return match (true) {
            $this->isAdmin($user) => $query,
            $user->hasRole('Coordinador') => $query->whereHas('application.student', fn ($s) => $s->where('career_id', $user->career_id)),
            $user->hasRole('Empresa') => $query->whereHas('application.offer.company', fn ($c) => $c->where('owner_user_id', $user->id)),
            $user->hasRole('Estudiante') => $query->whereHas('application.student', fn ($s) => $s->where('user_id', $user->id)),
            default => abort(403, 'Tu rol no tiene acceso a las prácticas.'),
        };
    }

    private function relations(): array
    {
        return ['application.student.user:id,name,last_name', 'application.student.career:id,nombre', 'application.offer:id,titulo,empresa,empresa_id', 'application.offer.company:id,razon_social'];
    }

    private function format(Practice $practice): array
    {
        $application = $practice->application;

        return [
            'id' => $practice->id,
            'estado' => $practice->estado,
            'fecha_inicio' => $practice->fecha_inicio?->toDateString(),
            'fecha_termino' => $practice->fecha_termino?->toDateString(),
            'nota_final' => $practice->final_grade !== null ? (float) $practice->final_grade : null,
            'contratado' => (bool) $practice->hired,
            'estudiante' => [
                'nombre' => $this->fullName($application?->student?->user),
                'carrera' => $application?->student?->career?->nombre,
            ],
            'oferta' => [
                'id' => $application?->offer?->id,
                'titulo' => $application?->offer?->titulo,
                'empresa' => $application?->offer?->company?->razon_social ?? $application?->offer?->empresa,
            ],
        ];
    }

    private function formatLog(PracticeLog $log): array
    {
        return [
            'id' => $log->id,
            'desde' => $log->period_start?->toDateString(),
            'hasta' => $log->period_end?->toDateString(),
            'horas' => $log->hours,
            'actividades' => $log->activities,
            'aprendizajes' => $log->learnings,
            'dificultades' => $log->difficulties,
        ];
    }
}
