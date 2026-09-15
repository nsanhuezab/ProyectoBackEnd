<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::updateOrCreate(
            [
                'email' => 'admin@ucsc.cl',
            ],
            [
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
            ]
        );
        $user2 = User::updateOrCreate(
            [
                'email' => 'juan@ucsc.cl',
            ],
            [
                'name' => 'Juan',
                'password' => Hash::make('12345678'),
            ]
        );

        $user3 = User::updateOrCreate(
            [
                'email' => 'coordinador@ucsc.cl',
            ],
            [
                'name' => 'Coordinador',
                'password' => Hash::make('12345678'),
            ]
        );
        $user4 = User::updateOrCreate(
            [
                'email' => 'empresa@gmail.com',
            ],
            [
                'name' => 'Empresa',
                'password' => Hash::make('12345678'),
            ]
        );
        $user1->assignRole('Administrador');
        $user2->assignRole('Estudiante');
        $user3->assignRole('Coordinador');
        $user4->assignRole('Empresa');
    }
}
