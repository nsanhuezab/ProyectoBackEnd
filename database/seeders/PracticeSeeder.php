<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Offer;
use App\Models\Practice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * - Juan: práctica "En Curso" en Tech Solutions (con bitácora).
     * - María: práctica "Finalizada" en el Banco (evaluada, contratada y con evaluación de la empresa).
     */
    public function run(): void
    {
        $coordinator = User::role('Coordinador')->firstOrFail();

        $practices = [
            [
                'student' => 20261001, 'offer' => 'Desarrollador Backend Laravel', 'estado' => 'En Curso',
                'fecha_inicio' => now()->subDays(35), 'fecha_termino' => now()->addDays(77), 'hired' => null,
            ],
            [
                'student' => 20261002, 'offer' => 'Analista de Datos Jr', 'estado' => 'Finalizada',
                'fecha_inicio' => now()->subDays(120), 'fecha_termino' => now()->subDays(36), 'hired' => true,
            ],
        ];

        foreach ($practices as $data) {
            $application = Application::query()
                ->where('student_id', Student::where('id_student', $data['student'])->value('id'))
                ->where('offer_id', Offer::where('titulo', $data['offer'])->value('id'))
                ->first();

            if (! $application) {
                continue;
            }

            $practice = Practice::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'assigned_by' => $coordinator->id,
                    'fecha_inicio' => $data['fecha_inicio']->toDateString(),
                    'fecha_termino' => $data['fecha_termino']->toDateString(),
                    'estado' => $data['estado'],
                    'hired' => $data['hired'],
                ],
            );

            $application->update([
                'status' => $practice->estado,
                'reviewed_by' => $coordinator->id,
                'decision_at' => $data['fecha_inicio']->copy()->subDays(5),
            ]);
        }
    }
}
