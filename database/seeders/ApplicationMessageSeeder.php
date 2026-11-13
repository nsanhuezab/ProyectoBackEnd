<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationMessageSeeder extends Seeder
{
    /**
     * Conversación de ejemplo en la primera postulación.
     */
    public function run(): void
    {
        $application = Application::with('student')->orderBy('id')->first();
        $coordinator = User::where('email', 'rsoto@ucsc.cl')->first();

        if (! $application || ! $coordinator) {
            return;
        }

        $messages = [
            [$application->student->user_id, 'Hola, ¿la práctica permite trabajar algunos días de forma remota?'],
            [$coordinator->id, 'Hola Juan, sí: la modalidad es híbrida, con tres días presenciales en Concepción.'],
        ];

        foreach ($messages as [$userId, $body]) {
            ApplicationMessage::firstOrCreate(
                ['application_id' => $application->id, 'user_id' => $userId, 'body' => $body],
                ['application_status' => 'Postulado'],
            );
        }
    }
}
