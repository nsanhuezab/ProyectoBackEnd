<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('email', 'empresa@gmail.com')->firstOrFail();

        foreach ([
            [
                'rut' => '76123456-7', 'razon_social' => 'Tech Solutions SpA', 'rubro' => 'Tecnología',
                'direccion' => 'Avenida Paicaví 1234, Concepción', 'sitio_web' => 'https://techsolutions.example',
                'contacto_nombre' => 'Camila Torres', 'contacto_email' => 'camila@techsolutions.example',
                'telefono' => '+56912345678',
            ],
            [
                'rut' => '96987654-3', 'razon_social' => 'Banco de Concepción', 'rubro' => 'Servicios financieros',
                'direccion' => 'Barros Arana 456, Concepción', 'sitio_web' => 'https://bancoconcepcion.example',
                'contacto_nombre' => 'Matías Rojas', 'contacto_email' => 'matias@bancoconcepcion.example',
                'telefono' => '+56987654321',
            ],
        ] as $data) {
            Company::updateOrCreate(
                ['rut' => $data['rut']],
                [...$data, 'owner_user_id' => $owner->id],
            );
        }
    }
}
