<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\PracticeLog;
use Illuminate\Database\Seeder;

class PracticeLogSeeder extends Seeder
{
    /**
     * Bitácoras quincenales de ejemplo para cada práctica iniciada.
     */
    public function run(): void
    {
        $entries = [
            ['Inducción al equipo, configuración del entorno de desarrollo y revisión de la arquitectura del sistema.', 'Flujo de trabajo con Git y revisiones de código.', 'Adaptarme a la documentación interna.'],
            ['Desarrollo de endpoints para el módulo de reportes y pruebas unitarias.', 'Buenas prácticas de testing.', null],
            ['Ajustes solicitados por el supervisor y preparación del informe de práctica.', 'Comunicación con usuarios finales.', 'Coordinar tiempos con otras áreas.'],
            ['Cierre de tareas pendientes y presentación de resultados al equipo.', 'Presentar resultados de forma clara.', null],
        ];

        foreach (Practice::whereIn('estado', ['En Curso', 'Informe Entregado', 'Evaluado', 'Finalizada'])->get() as $practice) {
            $start = $practice->fecha_inicio ?? now()->subDays(28);
            $periods = $practice->estado === 'En Curso' ? 2 : 4;

            for ($i = 0; $i < $periods; $i++) {
                $periodStart = $start->copy()->addDays(14 * $i);
                [$activities, $learnings, $difficulties] = $entries[$i];

                PracticeLog::updateOrCreate(
                    ['practice_id' => $practice->id, 'period_start' => $periodStart->toDateString()],
                    [
                        'period_end' => $periodStart->copy()->addDays(13)->toDateString(),
                        'hours' => 80,
                        'activities' => $activities,
                        'learnings' => $learnings,
                        'difficulties' => $difficulties,
                    ],
                );
            }
        }
    }
}
