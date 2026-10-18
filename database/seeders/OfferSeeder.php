<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Company;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('rut', '76123456-7')->firstOrFail();
        $secondCompany = Company::where('rut', '96987654-3')->firstOrFail();
        $creator = User::where('email', 'empresa@gmail.com')->firstOrFail();
        $careers = Career::whereIn('codigo', ['ICI', 'ICOM'])->get()->keyBy('codigo');

        $offers = [
            [
                'titulo' => 'Desarrollador Backend Laravel', 'company' => $company, 'vacantes' => 3,
                'area' => 'Desarrollo de software', 'perfil' => 'Experiencia inicial con PHP, APIs REST y bases de datos relacionales.',
                'nivel_requerido' => 'Avanzado', 'modalidad' => 'Híbrida', 'duracion_semanas' => 16,
                'horario' => 'Lunes a viernes, 09:00 a 17:00', 'remuneracion' => 180000,
                'habilidades' => ['PHP', 'Laravel', 'SQL'], 'ubicacion' => 'Concepción', 'careers' => ['ICI'],
            ],
            [
                'titulo' => 'Analista de Datos Jr', 'company' => $secondCompany, 'vacantes' => 1,
                'area' => 'Datos y analítica', 'perfil' => 'Apoyo en preparación de datos y reportes de gestión.',
                'nivel_requerido' => 'Intermedio', 'modalidad' => 'Presencial', 'duracion_semanas' => 12,
                'horario' => 'Lunes a viernes, 09:00 a 16:00', 'remuneracion' => null,
                'habilidades' => ['Excel', 'SQL', 'Python'], 'ubicacion' => 'Concepción', 'careers' => ['ICI', 'ICOM'],
            ],
        ];

        foreach ($offers as $data) {
            $careerCodes = $data['careers'];
            unset($data['careers']);
            $offer = Offer::updateOrCreate(
                ['titulo' => $data['titulo']],
                [
                    'company_id' => $data['company']->id,
                    'empresa' => $data['company']->razon_social,
                    'created_by' => $creator->id,
                    'vacantes' => $data['vacantes'],
                    'estado' => 'Aprobada',
                    'area' => $data['area'],
                    'perfil' => $data['perfil'],
                    'nivel_requerido' => $data['nivel_requerido'],
                    'modalidad' => $data['modalidad'],
                    'duracion_semanas' => $data['duracion_semanas'],
                    'horario' => $data['horario'],
                    'remuneracion' => $data['remuneracion'],
                    'habilidades' => $data['habilidades'],
                    'ubicacion' => $data['ubicacion'],
                ],
            );
            $offer->careers()->sync(collect($careerCodes)->map(fn (string $code) => $careers[$code]->id)->all());
        }
    }
}
