<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\PracticeLogRequest;
use App\Models\Practice;
use App\Models\PracticeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PracticeLogController extends Controller
{
    use HasDataTable;

    public function index(Request $request, Practice $practice): JsonResponse
    {
        $this->authorizePracticeAccess($request, $practice);

        return $this->dataTableResponse(
            $request,
            $practice->logs()->with('author:id,name,last_name'),
            searchable: ['avance'],
            sortable: ['id' => 'id', 'fecha' => 'fecha'],
            transform: fn (PracticeLog $log) => $this->format($log),
            defaultSort: 'fecha',
            defaultDir: 'desc',
        );
    }

    public function store(PracticeLogRequest $request, Practice $practice): JsonResponse
    {
        $this->authorizePracticeAccess($request, $practice);
        abort_unless($practice->estado === 'En Curso', 409, 'La práctica debe estar En Curso para registrar avances.');
        $validated = $request->validated();
        $previousLog = $practice->logs()->where('author_id', $request->user()->id)->latest('fecha')->first();

        if ($previousLog && $previousLog->fecha->addDays(14)->isAfter($validated['fecha'])) {
            throw ValidationException::withMessages(['fecha' => 'Debes dejar al menos 14 días entre registros de bitácora.']);
        }

        $log = $practice->logs()->create([
            ...$validated,
            'author_id' => $request->user()->id,
        ])->load('author:id,name,last_name');

        return response()->json($this->format($log), 201);
    }

    public function update(PracticeLogRequest $request, Practice $practice, PracticeLog $log): JsonResponse
    {
        $this->authorizePracticeAccess($request, $practice);
        abort_unless($log->practice_id === $practice->id, 404);
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador', 'Coordinador']), 403);
        $log->update($request->validated());

        return response()->json($this->format($log->refresh()->load('author:id,name,last_name')));
    }

    public function destroy(Request $request, Practice $practice, PracticeLog $log): Response
    {
        abort_unless($log->practice_id === $practice->id, 404);
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador', 'Coordinador']), 403);
        $log->delete();

        return response()->noContent();
    }

    private function authorizePracticeAccess(Request $request, Practice $practice): void
    {
        $user = $request->user();
        $isStudent = $user->hasRole('Estudiante')
            && $practice->application()->whereHas('student', fn (Builder $student) => $student->where('user_id', $user->id))->exists();
        $isCoordinator = $user->hasRole('Coordinador')
            && $practice->application()->whereHas('student', fn (Builder $student) => $student->where('career_id', $user->career_id))->exists();

        abort_unless($isStudent || $isCoordinator || $user->hasAnyRole(['Administrador', 'Super Administrador']), 403);
    }

    private function format(PracticeLog $log): array
    {
        return [
            'id' => $log->id,
            'practice_id' => $log->practice_id,
            'fecha' => $log->fecha->toDateString(),
            'avance' => $log->avance,
            'competencias' => $log->competencias ?? [],
            'autor' => trim(($log->author?->name ?? '').' '.($log->author?->last_name ?? '')),
        ];
    }
}
