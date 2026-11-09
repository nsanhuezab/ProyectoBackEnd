<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Offer;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::where('id_student', 20261001)->firstOrFail();
        // El PDF lo genera DemoCvSeeder, que se ejecuta después de este seeder.
        $cvPath = 'cvs/demo/cv-juan-perez.pdf';

        foreach (Offer::orderBy('id')->get() as $index => $offer) {
            Application::updateOrCreate(
                ['student_id' => $student->id, 'offer_id' => $offer->id],
                [
                    'status' => 'Postulado',
                    'cv_path' => $cvPath,
                    'motivation' => 'Me interesa aportar mis habilidades al equipo y seguir desarrollándome profesionalmente.',
                    'matching_score' => $index === 0 ? 91.25 : 52.5,
                ],
            );
        }

        // Segunda estudiante: postula al Analista de Datos (su práctica ya terminó, ver PracticeSeeder).
        $maria = Student::where('id_student', 20261002)->first();
        $dataOffer = Offer::where('titulo', 'Analista de Datos Jr')->first();
        if ($maria && $dataOffer) {
            Application::updateOrCreate(
                ['student_id' => $maria->id, 'offer_id' => $dataOffer->id],
                [
                    'status' => 'Postulado',
                    'cv_path' => 'cvs/demo/cv-maria-gonzalez.pdf',
                    'motivation' => 'Quiero aplicar mis conocimientos de análisis de datos en el sector financiero.',
                    'matching_score' => 89.0,
                ],
            );
        }
    }
}
