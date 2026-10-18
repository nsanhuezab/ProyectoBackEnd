<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\PracticeRequest;
use App\Models\Application;
use App\Models\Offer;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PracticeController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        $query = Practice::query()->with([
            'application.student.user:id,name,last_name',
            'application.student.career:id,nombre',
            'application.offer.company:id,razon_social',
        ])->withCount(['logs', 'evaluations']);
        $user = $request->user();

        if ($user?->hasRole('Estudiante')) {
            $query->whereHas('application.student', fn (Builder $student) => $student->where('user_id', $user->id));
        } elseif ($user?->hasRole('Empresa')) {
            $query->whereHas('application.offer.company', fn (Builder $company) => $company->where('owner_user_id', $user->id));
        } elseif ($user?->hasRole('Coordinador')) {
            $query->whereHas('application.student', fn (Builder $student) => $student->where('career_id', $user->career_id));
        }

        return $this->dataTableResponse(
            $request,
            $query,
            searchable: ['estado'],
            sortable: [
                'id' => 'id',
                'estado' => 'estado',
                'fecha_inicio' => 'fecha_inicio',
                'fecha_termino' => 'fecha_termino',
            ],
            transform: fn (Practice $practice) => $this->format($practice),
            defaultSort: 'id',
            defaultDir: 'desc',
            extraSearch: fn ($query, $like) => $query
                ->orWhereHas('application.student.user', fn ($student) => $student->where('name', 'like', $like))
                ->orWhereHas('application.offer', fn ($offer) => $offer->where('titulo', 'like', $like)),
        );
    }

    public function store(PracticeRequest $request): JsonResponse
    {
        $practice = DB::transaction(function () use ($request): Practice {
            $application = Application::query()->lockForUpdate()->findOrFail($request->validated('application_id'));
            $offer = Offer::query()->lockForUpdate()->findOrFail($application->offer_id);
            if ($request->user()->hasRole('Coordinador')) {
                abort_unless($application->student()->where('career_id', $request->user()->career_id)->exists(), 403);
            }
            abort_unless($application->estado === 'Postulado', 409, 'Solo puedes asignar una postulación pendiente.');
            abort_if($offer->applications()->whereHas('practice')->count() >= $offer->vacantes, 409, 'La oferta ya completó sus cupos.');

            $application->update([
                'estado' => 'Asignado',
                'reviewed_by' => $request->user()->id,
                'decision_at' => now(),
            ]);

            return Practice::create([
                'application_id' => $application->id,
                'assigned_by' => $request->user()->id,
                'fecha_inicio' => $request->validated('fecha_inicio'),
                'fecha_termino' => $request->validated('fecha_termino'),
                'estado' => 'Asignado',
            ]);
        });

        return response()->json($this->format($practice->load(['application.student.user', 'application.student.career', 'application.offer.company'])), 201);
    }

    public function update(PracticeRequest $request, Practice $practice): JsonResponse
    {
        $this->authorizePractice($request, $practice);
        $practice->update($request->safe()->only(['fecha_inicio', 'fecha_termino', 'contratado']));

        return response()->json($this->format($practice->refresh()->load(['application.student.user', 'application.student.career', 'application.offer.company'])));
    }

    public function destroy(Request $request, Practice $practice): Response
    {
        $this->authorizePractice($request, $practice);
        abort_if($practice->logs()->exists() || $practice->evaluations()->exists(), 409, 'No puedes eliminar una práctica con seguimiento o evaluaciones.');
        $practice->application()->update(['estado' => 'Postulado', 'reviewed_by' => null, 'decision_at' => null]);
        $practice->delete();

        return response()->noContent();
    }

    public function transition(Request $request, Practice $practice): JsonResponse
    {
        $this->authorizePractice($request, $practice);
        $validated = $request->validate([
            'estado' => ['required', Rule::in(['En Curso', 'Informe Entregado', 'Evaluado', 'Finalizada'])],
        ]);
        $transitions = [
            'Asignado' => 'En Curso',
            'En Curso' => 'Informe Entregado',
            'Informe Entregado' => 'Evaluado',
            'Evaluado' => 'Finalizada',
        ];
        abort_unless(($transitions[$practice->estado] ?? null) === $validated['estado'], 409, 'La transición solicitada no está permitida.');

        if ($validated['estado'] === 'Evaluado') {
            abort_unless($practice->evaluations()->pluck('tipo')->unique()->count() >= 2, 409, 'Se requieren las evaluaciones del supervisor y del coordinador.');
            $grade = $practice->calculateFinalGrade();
            abort_if($grade === null, 409, 'No fue posible calcular la nota final para la carrera del estudiante.');
            $practice->calificacion_final = $grade;
        }
        if ($validated['estado'] === 'Finalizada') {
            abort_if($practice->calificacion_final === null, 409, 'La práctica requiere nota final antes de cerrarse.');
            $practice->finalizada_at = now();
        }

        $practice->estado = $validated['estado'];
        $practice->save();
        $practice->application()->update(['estado' => $validated['estado']]);

        return response()->json($this->format($practice->refresh()->load(['application.student.user', 'application.student.career', 'application.offer.company'])));
    }

    private function format(Practice $practice): array
    {
        return [
            'id' => $practice->id,
            'application_id' => $practice->application_id,
            'estudiante' => trim(($practice->application?->student?->user?->name ?? '').' '.($practice->application?->student?->user?->last_name ?? '')),
            'carrera' => $practice->application?->student?->career?->nombre,
            'oferta' => $practice->application?->offer?->titulo,
            'empresa' => $practice->application?->offer?->company?->razon_social,
            'fecha_inicio' => $practice->fecha_inicio?->toDateString(),
            'fecha_termino' => $practice->fecha_termino?->toDateString(),
            'estado' => $practice->estado,
            'calificacion_final' => $practice->calificacion_final,
            'contratado' => $practice->contratado,
            'bitacoras_count' => $practice->logs_count ?? $practice->logs()->count(),
            'evaluaciones_count' => $practice->evaluations_count ?? $practice->evaluations()->count(),
            'acta_url' => route('practices.acta', $practice),
        ];
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
