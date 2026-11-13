<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\PracticeEvaluation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeEvaluationSeeder extends Seeder
{
    /**
     * Rúbricas del supervisor y del coordinador para las prácticas ya evaluadas.
     */
    public function run(): void
    {
        $supervisor = User::where('email', 'empresa@gmail.com')->first();
        $coordinator = User::where('email', 'cvalenzuela@ucsc.cl')->first();
        $competencies = array_keys(config('practices.competencies'));

        $scoresByRole = [
            PracticeEvaluation::SUPERVISOR => [6.5, 6.0, 6.3, 6.8, 6.2, 6.0],
            PracticeEvaluation::COORDINATOR => [6.0, 5.8, 6.0, 6.2, 6.5, 5.7],
        ];

        foreach (Practice::whereIn('estado', ['Evaluado', 'Finalizada'])->get() as $practice) {
            foreach ($scoresByRole as $role => $values) {
                $scores = array_combine($competencies, array_slice($values, 0, count($competencies)));

                PracticeEvaluation::updateOrCreate(
                    ['practice_id' => $practice->id, 'evaluator_role' => $role],
                    [
                        'evaluator_id' => ($role === PracticeEvaluation::SUPERVISOR ? $supervisor : $coordinator)?->id,
                        'scores' => $scores,
                        'grade' => round(array_sum($scores) / count($scores), 1),
                        'comments' => $role === PracticeEvaluation::SUPERVISOR
                            ? 'Excelente disposición y compromiso con el equipo.'
                            : 'Buen informe final y bitácora completa.',
                    ],
                );
            }

            $practice->unsetRelation('evaluations');
            $practice->update(['final_grade' => $practice->calculateFinalGrade()]);
        }
    }
}
