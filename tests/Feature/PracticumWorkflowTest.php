<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Career;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PracticumWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_can_apply_while_pending_applications_do_not_consume_offer_capacity(): void
    {
        Storage::fake('local');
        $studentRole = Role::firstOrCreate(['name' => 'Estudiante', 'guard_name' => 'web']);
        $career = $this->createCareer('ICI');
        $company = $this->createCompany();
        $offer = $this->createOffer($company, $career, 1);

        $firstStudentUser = User::factory()->create();
        $firstStudentUser->assignRole($studentRole);
        $firstStudent = $this->createStudent($firstStudentUser, $career, 10001);
        $this->createStudentApplication($firstStudent, $offer);

        $applicant = User::factory()->create();
        $applicant->assignRole($studentRole);
        $this->createStudent($applicant, $career, 10002);

        $response = $this->actingAs($applicant)->post('/applications', [
            'offer_id' => $offer->id,
            'cv' => UploadedFile::fake()->create('curriculum.pdf', 100, 'application/pdf'),
            'carta_motivacion' => 'Quiero aportar al equipo de desarrollo.',
        ]);

        $response->assertCreated()->assertJsonPath('matching_score', 82.5);
        $createdApplication = Application::where('student_id', $applicant->studentProfile->id)->firstOrFail();
        Storage::disk('local')->assertExists($createdApplication->cv_path);
        $this->assertSame('Postulado', $createdApplication->status);
    }

    public function test_coordinator_cannot_assign_a_student_from_another_career(): void
    {
        $coordinatorRole = Role::firstOrCreate(['name' => 'Coordinador', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'Estudiante', 'guard_name' => 'web']);
        $coordinatorCareer = $this->createCareer('ICI');
        $studentCareer = $this->createCareer('ICOM');
        $company = $this->createCompany();
        $offer = $this->createOffer($company, $studentCareer, 2);

        $coordinator = User::factory()->create(['career_id' => $coordinatorCareer->id]);
        $coordinator->assignRole($coordinatorRole);
        $studentUser = User::factory()->create();
        $studentUser->assignRole($studentRole);
        $student = $this->createStudent($studentUser, $studentCareer, 10003);
        $application = $this->createStudentApplication($student, $offer);

        $response = $this->actingAs($coordinator)->post('/coordinator/applications/'.$application->id.'/assign');

        $response->assertForbidden();
        $this->assertDatabaseMissing('practices', ['application_id' => $application->id]);
    }

    public function test_coordinator_can_assign_a_postulation_in_their_career(): void
    {
        $coordinatorRole = Role::firstOrCreate(['name' => 'Coordinador', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'Estudiante', 'guard_name' => 'web']);
        $career = $this->createCareer('ICI');
        $coordinator = User::factory()->create(['career_id' => $career->id]);
        $coordinator->assignRole($coordinatorRole);
        $studentUser = User::factory()->create();
        $studentUser->assignRole($studentRole);
        $student = $this->createStudent($studentUser, $career, 10004);
        $company = $this->createCompany();
        $offer = $this->createOffer($company, $career, 1);
        $application = $this->createStudentApplication($student, $offer);

        $response = $this->actingAs($coordinator)->post('/coordinator/applications/'.$application->id.'/assign');

        $response->assertCreated();
        $this->assertDatabaseHas('practices', ['application_id' => $application->id, 'estado' => 'Asignado']);
        $this->assertSame('Asignado', $application->refresh()->status);
    }

    public function test_sprint_two_seeders_populate_all_crud_resources(): void
    {
        Storage::fake('local');
        $this->seed();

        $this->assertDatabaseHas('roles', ['name' => 'Estudiante']);
        $this->assertDatabaseHas('careers', ['codigo' => 'ICI']);
        $this->assertDatabaseHas('users', ['email' => 'rsoto@ucsc.cl']);
        $this->assertDatabaseHas('students', ['id_student' => 20261001]);
        $this->assertDatabaseHas('companies', ['rut' => '76123456-7']);
        $this->assertDatabaseHas('offers', ['titulo' => 'Desarrollador Backend Laravel']);
        $this->assertDatabaseHas('applications', ['status' => 'Postulado']);
        $this->assertDatabaseHas('practices', ['estado' => 'Asignado']);
    }

    private function createCareer(string $code, float $supervisorWeight = 60, float $coordinatorWeight = 40): Career
    {
        return Career::create([
            'codigo' => $code,
            'nombre' => 'Ingeniería '.$code,
            'supervisor' => $supervisorWeight,
            'coordinador' => $coordinatorWeight,
            'activa' => true,
        ]);
    }

    private function createCompany(): Company
    {
        return Company::create([
            'razon_social' => 'Empresa de prueba',
            'rut' => '76123456-7',
            'rubro' => 'Tecnología',
            'direccion' => 'Concepción',
            'contacto_nombre' => 'Supervisora',
            'contacto_email' => 'supervisora@example.com',
            'telefono' => '+56912345678',
        ]);
    }

    private function createOffer(Company $company, Career $career, int $vacancies): Offer
    {
        $offer = Offer::create([
            'titulo' => 'Desarrollo de software',
            'empresa' => $company->razon_social,
            'empresa_id' => $company->id,
            'vacantes' => $vacancies,
            'estado' => 'Aprobada',
            'nivel_requerido' => 'Avanzado',
            'habilidades' => ['PHP', 'SQL'],
        ]);
        $offer->careers()->attach($career);

        return $offer;
    }

    private function createStudent(User $user, Career $career, int $studentId): Student
    {
        return Student::create([
            'id_student' => $studentId,
            'user_id' => $user->id,
            'career_id' => $career->id,
            'carrera' => $career->nombre,
            'nivel_avance' => 'Avanzado',
            'habilidades' => ['PHP', 'Laravel'],
        ]);
    }

    private function createStudentApplication(Student $student, Offer $offer): Application
    {
        return Application::create([
            'student_id' => $student->id,
            'offer_id' => $offer->id,
            'status' => 'Postulado',
            'cv_path' => 'cvs/test.pdf',
            'motivation' => 'Carta de prueba.',
            'matching_score' => 82.5,
        ]);
    }
}
