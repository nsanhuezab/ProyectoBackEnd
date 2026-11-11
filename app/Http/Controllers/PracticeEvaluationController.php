<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesPractices;
use App\Models\Practice;
use App\Models\PracticeEvaluation;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Rúbricas de evaluación del supervisor y del coordinador.
 */
class PracticeEvaluationController extends Controller
{
    use AuthorizesPractices;

    public function store(Request $request, Practice $practice): RedirectResponse
    {
        $this->loadPracticeRelations($practice);
        $competencies = array_keys(config('practices.competencies'));
        $min = config('practices.min_grade');
        $max = config('practices.max_grade');

        $validated = $request->validate([
            'evaluator_role' => ['required', Rule::in([PracticeEvaluation::SUPERVISOR, PracticeEvaluation::COORDINATOR])],
            'scores' => ['required', 'array'],
            ...collect($competencies)->mapWithKeys(fn (string $key) => [
                "scores.$key" => ['required', 'numeric', "min:$min", "max:$max"],
            ])->all(),
            'comments' => ['nullable', 'string', 'max:2000'],
        ], [], collect(config('practices.competencies'))->mapWithKeys(fn (string $label, string $key) => ["scores.$key" => $label])->all());

        abort_unless($this->canEvaluate($request->user(), $practice, $validated['evaluator_role']), 403);

        $scores = collect($competencies)->mapWithKeys(fn (string $key) => [$key => round((float) $validated['scores'][$key], 1)]);
        $grade = round($scores->avg(), 1);

        DB::transaction(function () use ($practice, $validated, $scores, $grade, $request): void {
            PracticeEvaluation::updateOrCreate(
                ['practice_id' => $practice->id, 'evaluator_role' => $validated['evaluator_role']],
                [
                    'evaluator_id' => $request->user()->id > 0 ? $request->user()->id : null,
                    'scores' => $scores->all(),
                    'grade' => $grade,
                    'comments' => $validated['comments'] ?? null,
                ],
            );

            // Con las dos rúbricas se calcula la nota final y la práctica pasa a "Evaluado".
            $practice->unsetRelation('evaluations');
            $finalGrade = $practice->calculateFinalGrade();
            if ($finalGrade !== null) {
                $practice->final_grade = $finalGrade;
                if ($practice->estado === 'Informe Entregado') {
                    $practice->estado = 'Evaluado';
                    $practice->application()->update(['status' => 'Evaluado']);
                }
                $practice->save();
            }
        });

        $label = $validated['evaluator_role'] === PracticeEvaluation::SUPERVISOR ? 'del supervisor' : 'del coordinador';
        $message = "Evaluación {$label} guardada con nota ".number_format($grade, 1, ',', '').'.';

        if ($practice->final_grade !== null) {
            $message .= ' Nota final de la práctica: '.number_format((float) $practice->final_grade, 1, ',', '').'.';
            Notifier::send(
                $practice->application?->student?->user,
                'Tu práctica fue evaluada',
                "Tu práctica «{$practice->application?->offer?->titulo}» tiene nota final ".number_format((float) $practice->final_grade, 1, ',', '').'.',
                route('student.practice.index'),
            );
        }

        return back()->with('success', $message);
    }
}
