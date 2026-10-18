<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EvaluationController extends Controller
{
    use HasDataTable;

    public function index(Request $request, Practice $practice): JsonResponse
    {
        $this->authorizePracticeAccess($request, $practice, null);

        return $this->dataTableResponse(
            $request,
            $practice->evaluations()->with('evaluator:id,name,last_name'),
            searchable: ['tipo', 'comentarios'],
            sortable: ['id' => 'id', 'tipo' => 'tipo', 'calificacion' => 'calificacion'],
            transform: fn (Evaluation $evaluation) => $this->format($evaluation),
            defaultSort: 'tipo',
        );
    }

    public function store(EvaluationRequest $request, Practice $practice): JsonResponse
    {
        $validated = $request->validated();
        $this->authorizePracticeAccess($request, $practice, $validated['tipo']);
        abort_unless(in_array($practice->estado, ['Informe Entregado', 'Evaluado'], true), 409, 'La práctica debe tener su informe entregado antes de evaluarse.');

        $evaluation = $practice->evaluations()->updateOrCreate(
            ['tipo' => $validated['tipo']],
            [
                'evaluator_id' => $request->user()->id,
                'calificacion' => $validated['calificacion'],
                'competencias' => $validated['competencias'] ?? [],
                'comentarios' => $validated['comentarios'] ?? null,
            ],
        )->load('evaluator:id,name,last_name');

        return response()->json($this->format($evaluation), 201);
    }

    public function update(EvaluationRequest $request, Practice $practice, Evaluation $evaluation): JsonResponse
    {
        abort_unless($evaluation->practice_id === $practice->id, 404);
        $validated = $request->validated();
        $this->authorizePracticeAccess($request, $practice, $validated['tipo']);
        abort_unless($evaluation->tipo === $validated['tipo'], 422, 'No se puede cambiar el tipo de evaluador.');
        $evaluation->update([
            'calificacion' => $validated['calificacion'],
            'competencias' => $validated['competencias'] ?? [],
            'comentarios' => $validated['comentarios'] ?? null,
            'evaluator_id' => $request->user()->id,
        ]);

        return response()->json($this->format($evaluation->refresh()->load('evaluator:id,name,last_name')));
    }

    public function destroy(Request $request, Practice $practice, Evaluation $evaluation): Response
    {
        abort_unless($evaluation->practice_id === $practice->id, 404);
        $this->authorizePracticeAccess($request, $practice, $evaluation->tipo);
        $evaluation->delete();
        $practice->update(['calificacion_final' => null]);

        return response()->noContent();
    }

    private function authorizePracticeAccess(Request $request, Practice $practice, ?string $type): void
    {
        $user = $request->user();
        $isCoordinator = $user->hasRole('Coordinador')
            && $practice->application()->whereHas('student', fn (Builder $student) => $student->where('career_id', $user->career_id))->exists();
        $isSupervisor = $user->hasRole('Empresa')
            && $practice->application()->whereHas('offer.company', fn (Builder $company) => $company->where('owner_user_id', $user->id))->exists();
        $isStudent = $user->hasRole('Estudiante')
            && $practice->application()->whereHas('student', fn (Builder $student) => $student->where('user_id', $user->id))->exists();

        if ($type === 'coordinador') {
            abort_unless($isCoordinator || $user->hasAnyRole(['Administrador', 'Super Administrador']), 403);
            return;
        }
        if ($type === 'supervisor') {
            abort_unless($isSupervisor || $user->hasAnyRole(['Administrador', 'Super Administrador']), 403);
            return;
        }
        if ($type === 'estudiante') {
            abort_unless($isStudent && $practice->estado === 'Finalizada', 403);
            return;
        }

        abort_unless($isCoordinator || $isSupervisor || $isStudent || $user->hasAnyRole(['Administrador', 'Super Administrador']), 403);
    }

    private function format(Evaluation $evaluation): array
    {
        return [
            'id' => $evaluation->id,
            'practice_id' => $evaluation->practice_id,
            'tipo' => $evaluation->tipo,
            'calificacion' => (float) $evaluation->calificacion,
            'competencias' => $evaluation->competencias ?? [],
            'comentarios' => $evaluation->comentarios,
            'evaluador' => trim(($evaluation->evaluator?->name ?? '').' '.($evaluation->evaluator?->last_name ?? '')),
        ];
    }
}
