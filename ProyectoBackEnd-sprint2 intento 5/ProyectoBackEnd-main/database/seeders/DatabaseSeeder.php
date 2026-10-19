<?php

namespace Database\Seeders;

use App\Models\Carrera;
use App\Models\Empresa;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Oferta;
use App\Models\Postulacion;
use App\Models\Practica;
use App\Models\User;
use App\Support\Rut;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Datos de demostración. Todos los usuarios tienen la contraseña "password".
     */
    public function run(): void
    {
        $crear = fn (string $name, string $email, string $role, int $rut) => User::create([
            'name' => $name,
            'email' => $email,
            'rut' => Rut::make($rut),
            'role' => $role,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $crear('Administrador', 'admin@ucsc.cl', 'administrador', 11111111);
        $crear('Coordinador', 'coordinador@ucsc.cl', 'coordinador', 12222222);
        $userEmpresa = $crear('Empresa', 'empresa@ucsc.cl', 'empresa', 13333333);
        $claudia = $crear('Claudia Valenzuela', 'claudia@ucsc.cl', 'estudiante', 20111222);
        $roberto = $crear('Roberto Soto', 'roberto@ucsc.cl', 'estudiante', 20333444);
        $juan = $crear('Juan', 'juan@ucsc.cl', 'estudiante', 20555666);

        $informatica = Carrera::create(['nombre' => 'Ingeniería Civil Informática', 'codigo' => 'ICINF', 'facultad' => 'Ingeniería']);
        Carrera::create(['nombre' => 'Ingeniería Comercial', 'codigo' => 'ICOM', 'facultad' => 'Ciencias Económicas']);
        Carrera::create(['nombre' => 'Trabajo Social', 'codigo' => 'TSOC', 'facultad' => 'Ciencias Sociales']);

        $empresa = Empresa::create([
            'user_id' => $userEmpresa->id,
            'razon_social' => 'Tecnologías del Sur SpA',
            'rut' => Rut::make(76123456),
            'giro' => 'Desarrollo de software',
            'contacto_nombre' => 'Empresa',
            'email' => 'contacto@tecsur.cl',
            'telefono' => '+56 41 2345678',
            'direccion' => 'Av. O\'Higgins 1234, Concepción',
            'estado' => 'activa',
        ]);

        $estudiantes = collect([[$claudia, '2020100001', 9], [$roberto, '2020100002', 8], [$juan, '2021100003', 7]])
            ->map(fn ($d) => Estudiante::create([
                'user_id' => $d[0]->id,
                'carrera_id' => $informatica->id,
                'matricula' => $d[1],
                'semestre' => $d[2],
            ]));

        $ofertaDev = Oferta::create([
            'empresa_id' => $empresa->id,
            'titulo' => 'Práctica de desarrollo web',
            'area' => 'Desarrollo',
            'descripcion' => 'Apoyo en el desarrollo de aplicaciones web con Laravel y bases de datos relacionales.',
            'requisitos' => 'Conocimientos de PHP, SQL y Git.',
            'modalidad' => 'hibrida',
            'cupos' => 2,
            'horas' => 320,
            'fecha_limite' => now()->addMonths(2),
            'estado' => 'abierta',
        ]);

        Oferta::create([
            'empresa_id' => $empresa->id,
            'titulo' => 'Práctica de soporte TI',
            'area' => 'Infraestructura',
            'descripcion' => 'Soporte a usuarios, mantención de equipos y documentación de procesos.',
            'modalidad' => 'presencial',
            'cupos' => 1,
            'horas' => 240,
            'fecha_limite' => now()->addMonth(),
            'estado' => 'abierta',
        ]);

        Postulacion::create(['oferta_id' => $ofertaDev->id, 'estudiante_id' => $estudiantes[0]->id, 'mensaje' => 'Me interesa aprender desarrollo web.', 'estado' => 'aceptada']);
        Postulacion::create(['oferta_id' => $ofertaDev->id, 'estudiante_id' => $estudiantes[1]->id, 'estado' => 'pendiente']);

        $practica = Practica::create([
            'estudiante_id' => $estudiantes[0]->id,
            'empresa_id' => $empresa->id,
            'oferta_id' => $ofertaDev->id,
            'supervisor' => 'Marcela Rojas',
            'fecha_inicio' => now()->subMonth()->toDateString(),
            'fecha_termino' => now()->addMonths(2)->toDateString(),
            'horas_totales' => 320,
            'estado' => 'en_curso',
        ]);

        Evaluacion::create([
            'practica_id' => $practica->id,
            'evaluador_id' => User::where('role', 'coordinador')->value('id'),
            'tipo' => 'coordinador',
            'nota' => 6.5,
            'fecha' => now()->toDateString(),
            'comentario' => 'Buen desempeño en las primeras semanas.',
        ]);
    }
}
