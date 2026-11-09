<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Genera un CV ficticio en PDF para cada estudiante de ejemplo y lo asocia a sus postulaciones.
 * Se puede ejecutar solo: php artisan db:seed --class=DemoCvSeeder
 */
class DemoCvSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->profiles() as $idStudent => $profile) {
            $student = Student::with(['user', 'career'])->where('id_student', $idStudent)->first();
            if (! $student) {
                continue;
            }

            $cv = [
                ...$profile,
                'nombre' => trim($student->user?->name.' '.$student->user?->last_name),
                'email' => $student->user?->email,
                'carrera' => $student->career?->nombre ?? $student->carrera,
                'anio' => $student->year_level,
                'promedio' => (float) $student->gpa,
                'habilidades' => $student->habilidades ?: [],
            ];

            $path = 'cvs/demo/'.$profile['archivo'];
            Storage::disk('local')->put($path, $this->render($cv));

            // Solo reemplaza los CV de ejemplo; los que suba el estudiante no se tocan.
            Application::where('student_id', $student->id)
                ->where(fn ($query) => $query->whereNull('cv_path')->orWhere('cv_path', 'like', 'cvs/demo/%'))
                ->update(['cv_path' => $path]);
        }

        Storage::disk('local')->delete('cvs/demo/demo-resume.pdf');
    }

    private function render(array $cv): string
    {
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('applications.demo-cv', ['cv' => $cv])->output();
        }

        return view('applications.demo-cv', ['cv' => $cv])->render();
    }

    private function profiles(): array
    {
        return [
            20261001 => [
                'archivo' => 'cv-juan-perez.pdf',
                'titulo' => 'Estudiante de Ingeniería Civil Informática · Desarrollo backend',
                'telefono' => '+56 9 1111 2222',
                'perfil' => 'Estudiante de cuarto año con interés en el desarrollo de aplicaciones web y APIs. '
                    .'Me gusta trabajar en equipo, escribir código ordenado y aprender tecnologías nuevas.',
                'proyectos' => [
                    [
                        'nombre' => 'Plataforma de gestión de prácticas',
                        'periodo' => '2026',
                        'detalle' => [
                            'Desarrollo con Laravel, MySQL y Tailwind en un equipo de cinco personas.',
                            'Módulos de postulaciones, roles y permisos y generación de reportes en PDF.',
                        ],
                    ],
                    [
                        'nombre' => 'API de inventario para una droguería',
                        'periodo' => '2025',
                        'detalle' => [
                            'API REST con autenticación, validaciones y pruebas automatizadas.',
                            'Control de versiones con Git y GitHub.',
                        ],
                    ],
                ],
                'idiomas' => 'Español nativo · Inglés intermedio (lectura técnica)',
            ],
            20261002 => [
                'archivo' => 'cv-maria-gonzalez.pdf',
                'titulo' => 'Estudiante de Ingeniería Comercial · Análisis de datos',
                'telefono' => '+56 9 3333 4444',
                'perfil' => 'Estudiante de cuarto año orientada al análisis de datos y la toma de decisiones. '
                    .'Busco aplicar herramientas cuantitativas a problemas reales del negocio.',
                'proyectos' => [
                    [
                        'nombre' => 'Dashboard de ventas para una pyme',
                        'periodo' => '2026',
                        'detalle' => [
                            'Limpieza de datos con Python y visualización en Power BI.',
                            'Indicadores de ventas mensuales y segmentación de clientes.',
                        ],
                    ],
                    [
                        'nombre' => 'Estudio de mercado del sector financiero',
                        'periodo' => '2025',
                        'detalle' => [
                            'Encuesta a 200 personas y análisis en Excel y SQL.',
                            'Presentación de resultados a docentes y empresa colaboradora.',
                        ],
                    ],
                ],
                'idiomas' => 'Español nativo · Inglés avanzado',
            ],
        ];
    }
}
