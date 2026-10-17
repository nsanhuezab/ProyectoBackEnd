<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            ['codigo' => 'ICI',  'nombre' => 'Ingeniería Civil Informática', 'supervisor' => 60, 'coordinador' => 40],
            ['codigo' => 'ICI2', 'nombre' => 'Ingeniería Civil Industrial',  'supervisor' => 50, 'coordinador' => 50],
            ['codigo' => 'ICOM', 'nombre' => 'Ingeniería Comercial',         'supervisor' => 55, 'coordinador' => 45],
        ];

        foreach ($carreras as $c) {
            Career::updateOrCreate(['codigo' => $c['codigo']], $c + ['activa' => true]);
        }
    }
}
