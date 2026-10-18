<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $practice = Practice::orderByDesc('id')->firstOrFail();
        $coordinator = User::role('Coordinador')->firstOrFail();
        $supervisor = User::role('Empresa')->firstOrFail();

        Evaluation::updateOrCreate(
            ['practice_id' => $practice->id, 'tipo' => 'supervisor'],
            ['evaluator_id' => $supervisor->id, 'calificacion' => 6.5, 'competencias' => ['Responsabilidad', 'Adaptabilidad'], 'comentarios' => 'Buen desempeno durante la practica.'],
        );
        Evaluation::updateOrCreate(
            ['practice_id' => $practice->id, 'tipo' => 'coordinador'],
            ['evaluator_id' => $coordinator->id, 'calificacion' => 6.0, 'competencias' => ['Analisis', 'Comunicacion'], 'comentarios' => 'Cumple los objetivos academicos.'],
        );

        $practice->loadMissing('application.student.career', 'evaluations');
        $practice->update([
            'estado' => 'Finalizada',
            'calificacion_final' => $practice->calculateFinalGrade(),
            'finalizada_at' => now(),
            'contratado' => true,
        ]);
        $practice->application()->update(['estado' => 'Finalizada']);
    }
}
