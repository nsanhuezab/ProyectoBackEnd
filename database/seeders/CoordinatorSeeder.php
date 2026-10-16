<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoordinatorSeeder extends Seeder
{
    public function run(): void
    {
        $coordinadores = [
            ['name' => 'Roberto Soto', 'email' => 'rsoto@ucsc.cl', 'telefono' => '+56912345678', 'carrera' => 'ICI'],
            ['name' => 'Claudia Valenzuela', 'email' => 'cvalenzuela@ucsc.cl', 'telefono' => '+56987654321', 'carrera' => 'ICOM'],
        ];

        foreach ($coordinadores as $c) {
            $user = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'telefono' => $c['telefono'],
                    'career_id' => Career::where('codigo', $c['carrera'])->value('id'),
                    'password' => Hash::make('12345678'),
                ]
            );
            $user->syncRoles('Coordinador');
        }
    }
}
