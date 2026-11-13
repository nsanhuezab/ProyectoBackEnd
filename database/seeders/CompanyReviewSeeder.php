<?php

namespace Database\Seeders;

use App\Models\CompanyReview;
use App\Models\Practice;
use Illuminate\Database\Seeder;

class CompanyReviewSeeder extends Seeder
{
    /**
     * Evaluación de la empresa hecha por el estudiante al terminar su práctica.
     */
    public function run(): void
    {
        foreach (Practice::with('application.offer')->where('estado', 'Finalizada')->get() as $practice) {
            CompanyReview::updateOrCreate(
                ['practice_id' => $practice->id],
                [
                    'company_id' => $practice->application->offer->empresa_id,
                    'rating' => 5,
                    'comment' => 'Muy buen ambiente de trabajo y acompañamiento del supervisor.',
                ],
            );
        }
    }
}
