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
        $application = Application::query()->orderBy('id')->first();

        if (! $application) {
            return;
        }

        $practice = Practice::updateOrCreate(
            ['application_id' => $application->id],
            [
                'assigned_by' => $coordinator->id,
                'fecha_inicio' => now()->subDays(7)->toDateString(),
                'fecha_termino' => now()->addDays(105)->toDateString(),
                'estado' => 'Asignado',
            ],
        );
        $application->update(['status' => $practice->estado]);
    }
}
