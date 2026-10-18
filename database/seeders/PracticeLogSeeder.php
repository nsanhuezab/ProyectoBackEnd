<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::where('email', 'juan@ucsc.cl')->firstOrFail();

        foreach (Practice::orderBy('id')->get() as $index => $practice) {
            $date = now()->subDays($index === 0 ? 1 : 15)->toDateString();
            $practice->logs()->updateOrCreate(
                ['fecha' => $date],
                [
                    'author_id' => $student->id,
                    'avance' => 'Se completaron las tareas planificadas y se revisaron los resultados con el equipo supervisor.',
                    'competencias' => ['Comunicacion', 'Trabajo en equipo'],
                ],
            );
        }
    }
}
