<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::updateOrCreate(
            ['titulo' => 'Desarrollador Backend Laravel', 'empresa' => 'Tech Solutions SpA'],
            ['vacantes' => 3, 'estado' => 'Disponible']
        );
        Offer::updateOrCreate(
            ['titulo' => 'Analista de Datos Jr', 'empresa' => 'Banco de Concepción'],
            ['vacantes' => 1, 'estado' => 'Disponible']
        );
    }
}
