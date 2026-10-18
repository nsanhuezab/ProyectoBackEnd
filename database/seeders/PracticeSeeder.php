<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinator = User::role('Coordinador')->firstOrFail();

        foreach (Application::orderBy('id')->get()->values() as $index => $application) {
            $practice = Practice::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'assigned_by' => $coordinator->id,
                    'fecha_inicio' => now()->subDays($index === 0 ? 7 : 45)->toDateString(),
                    'fecha_termino' => now()->addDays($index === 0 ? 105 : -2)->toDateString(),
                    'estado' => $index === 0 ? 'En Curso' : 'Informe Entregado',
                    'contratado' => false,
                ],
            );
            $application->update(['estado' => $practice->estado]);
        }
    }
}
