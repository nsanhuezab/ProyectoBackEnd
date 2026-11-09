<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador',
                'last_name' => 'UCSC',
                'rut' => '11111111-1',
                'gender' => 'Prefiero no decir',
                'age' => 40,
                'email' => 'admin@ucsc.cl',
                'role' => 'Administrador',
            ],
            [
                'name' => 'Juan',
                'last_name' => 'Pérez',
                'rut' => '22222222-2',
                'gender' => 'Masculino',
                'age' => 22,
                'email' => 'juan@ucsc.cl',
                'role' => 'Estudiante',
            ],
            [
                'name' => 'María',
                'last_name' => 'González',
                'rut' => '77777777-7',
                'gender' => 'Femenino',
                'age' => 23,
                'email' => 'maria@ucsc.cl',
                'role' => 'Estudiante',
            ],
            [
                'name' => 'Coordinador',
                'last_name' => 'UCSC',
                'rut' => '33333333-3',
                'gender' => 'Prefiero no decir',
                'age' => 35,
                'email' => 'coordinador@ucsc.cl',
                'role' => 'Coordinador',
            ],
            [
                'name' => 'Empresa',
                'last_name' => 'UCSC',
                'rut' => '44444444-4',
                'gender' => 'Prefiero no decir',
                'age' => 30,
                'email' => 'empresa@gmail.com',
                'role' => 'Empresa',
            ],
            [
                'name' => 'Roberto',
                'last_name' => 'Soto',
                'rut' => '55555555-5',
                'gender' => 'Masculino',
                'age' => 38,
                'email' => 'rsoto@ucsc.cl',
                'role' => 'Coordinador',
            ],
            [
                'name' => 'Claudia',
                'last_name' => 'Valenzuela',
                'rut' => '66666666-6',
                'gender' => 'Femenino',
                'age' => 36,
                'email' => 'cvalenzuela@ucsc.cl',
                'role' => 'Coordinador',
            ],
        ];

        foreach ($users as $data) {
            $role = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'password' => Hash::make('12345678'),
                ]
            );

            $user->syncRoles($role);
        }
    }
}
