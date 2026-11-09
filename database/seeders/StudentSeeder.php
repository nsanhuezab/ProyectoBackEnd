<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $validator = User::where('email', 'admin@ucsc.cl')->first();

        $students = [
            [
                'id_student' => 20261001, 'email' => 'juan@ucsc.cl', 'career' => 'ICI',
                'nivel_avance' => 'Avanzado', 'year_level' => 4, 'gpa' => 5.6,
                'habilidades' => ['PHP', 'Laravel', 'SQL', 'Git'],
            ],
            [
                'id_student' => 20261002, 'email' => 'maria@ucsc.cl', 'career' => 'ICOM',
                'nivel_avance' => 'Intermedio', 'year_level' => 4, 'gpa' => 6.1,
                'habilidades' => ['Excel', 'SQL', 'Python', 'Power BI'],
            ],
        ];

        foreach ($students as $data) {
            $career = Career::where('codigo', $data['career'])->firstOrFail();

            Student::updateOrCreate(
                ['id_student' => $data['id_student']],
                [
                    'user_id' => User::where('email', $data['email'])->firstOrFail()->id,
                    'career_id' => $career->id,
                    'carrera' => $career->nombre,
                    'nivel_avance' => $data['nivel_avance'],
                    'year_level' => $data['year_level'],
                    'gpa' => $data['gpa'],
                    'habilidades' => $data['habilidades'],
                    'validated_at' => now()->subDays(20),
                    'validated_by' => $validator?->id,
                ],
            );
        }
    }
}
