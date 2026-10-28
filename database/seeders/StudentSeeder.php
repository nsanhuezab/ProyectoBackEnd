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
        $user = User::where('email', 'juan@ucsc.cl')->firstOrFail();
        $career = Career::where('codigo', 'ICI')->firstOrFail();

        Student::updateOrCreate(
            ['id_student' => 20261001],
            [
                'user_id' => $user->id,
                'career_id' => $career->id,
                'carrera' => $career->nombre,
                'nivel_avance' => 'Avanzado',
                'habilidades' => ['PHP', 'Laravel', 'SQL', 'Git'],
            ],
        );
    }
}
