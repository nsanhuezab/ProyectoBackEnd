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

        $response = $this->actingAs($applicant)->post('/student/applications', [
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

        $response = $this->actingAs($coordinator)->post('/applications/'.$application->id.'/assign');

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

        $response = $this->actingAs($coordinator)->post('/applications/'.$application->id.'/assign');

        $response->assertRedirect()->assertSessionHas('success');
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
        $this->assertDatabaseHas('practices', ['estado' => 'En Curso']);
        $this->assertDatabaseHas('practices', ['estado' => 'Finalizada', 'hired' => true]);
        $this->assertDatabaseCount('practice_evaluations', 2);
        $this->assertDatabaseHas('company_reviews', ['rating' => 5]);
        $this->assertDatabaseHas('application_messages', ['application_status' => 'Postulado']);
        $this->assertGreaterThan(0, \App\Models\PracticeLog::count());
    }

    public function test_admin_can_open_every_management_page(): void
    {
        Storage::fake('local');
        $this->withoutVite();
        $this->seed();
        $admin = User::where('email', 'admin@ucsc.cl')->firstOrFail();

        foreach (['/careers', '/coordinators', '/students', '/companies', '/offers', '/applications', '/practices', '/reports', '/activity', '/company/applicants', '/company/practices'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_coordinator_can_open_their_pages_but_not_admin_ones(): void
    {
        Storage::fake('local');
        $this->withoutVite();
        $this->seed();
        $coordinator = User::where('email', 'coordinador@ucsc.cl')->firstOrFail();

        $this->actingAs($coordinator)->get('/applications')->assertOk();
        $this->actingAs($coordinator)->get('/practices')->assertOk();
        $this->actingAs($coordinator)->get('/offers')->assertOk();
        $this->actingAs($coordinator)->get('/companies')->assertForbidden();
    }

    public function test_evaluado_requires_both_rubrics_and_final_grade_uses_career_weights(): void
    {
        Storage::fake('local');
        $this->seed();
        $admin = User::where('email', 'admin@ucsc.cl')->firstOrFail();
        $practice = $this->practiceOf(20261001); // Juan, ICI: supervisor 60 % / coordinador 40 %
        $practice->update(['estado' => 'Informe Entregado']);

        $this->actingAs($admin)->post("/practices/{$practice->id}/transition", ['estado' => 'Evaluado'])
            ->assertSessionHas('error');
        $this->assertSame('Informe Entregado', $practice->fresh()->estado);

        $this->actingAs($admin)->post("/practices/{$practice->id}/evaluations", [
            'evaluator_role' => 'supervisor',
            'scores' => array_fill_keys(array_keys(config('practices.competencies')), 7),
        ])->assertSessionHas('success');
        $this->actingAs($admin)->post("/practices/{$practice->id}/evaluations", [
            'evaluator_role' => 'coordinador',
            'scores' => array_fill_keys(array_keys(config('practices.competencies')), 5),
        ])->assertSessionHas('success');

        $practice->refresh();
        $this->assertSame('Evaluado', $practice->estado);
        $this->assertEquals(6.2, (float) $practice->final_grade); // 7 × 0,6 + 5 × 0,4
        $this->assertSame('Evaluado', $practice->application->status);

        $this->actingAs($admin)->get("/practices/{$practice->id}/acta")->assertOk();
    }

    public function test_student_registers_biweekly_log_and_cannot_touch_other_practices(): void
    {
        Storage::fake('local');
        $this->seed();
        $juan = User::where('email', 'juan@ucsc.cl')->firstOrFail();
        $ownPractice = $this->practiceOf(20261001);
        $otherPractice = $this->practiceOf(20261002);
        $payload = [
            'period_start' => now()->subDays(13)->toDateString(),
            'period_end' => now()->toDateString(),
            'hours' => 40,
            'activities' => 'Desarrollé el módulo de reportes y sus pruebas.',
        ];

        $this->actingAs($juan)->post("/student/practices/{$ownPractice->id}/logs", $payload)->assertSessionHas('success');
        $this->assertDatabaseHas('practice_logs', ['practice_id' => $ownPractice->id, 'hours' => 40]);

        $this->actingAs($juan)->post("/student/practices/{$otherPractice->id}/logs", $payload)->assertForbidden();

        $this->actingAs($juan)->post("/student/practices/{$ownPractice->id}/logs", [
            ...$payload,
            'period_start' => now()->subDays(40)->toDateString(),
        ])->assertSessionHasErrors('period_end');
    }

    public function test_reports_export_and_company_sees_simplified_profiles(): void
    {
        Storage::fake('local');
        $this->withoutVite();
        $this->seed();
        $admin = User::where('email', 'admin@ucsc.cl')->firstOrFail();
        $company = User::where('email', 'empresa@gmail.com')->firstOrFail();

        $this->actingAs($admin)->get('/reports')->assertOk()->assertSee('Tasa de aprobación');
        $this->actingAs($admin)->get('/reports/excel')->assertOk();
        $this->actingAs($admin)->get('/reports/pdf')->assertOk();

        $this->actingAs($company)->get('/company/applicants')
            ->assertOk()
            ->assertSee('Juan P.')
            ->assertDontSee('juan@ucsc.cl');
    }

    public function test_application_messages_are_limited_to_participants(): void
    {
        Storage::fake('local');
        $this->withoutVite();
        $this->seed();
        $application = Application::with('student')->orderBy('id')->firstOrFail();
        $juan = User::where('email', 'juan@ucsc.cl')->firstOrFail();
        $maria = User::where('email', 'maria@ucsc.cl')->firstOrFail();

        $this->actingAs($juan)->get("/applications/{$application->id}")->assertOk();
        $this->actingAs($juan)->post("/applications/{$application->id}/messages", ['body' => '¿Hay cupos disponibles?'])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('application_messages', ['body' => '¿Hay cupos disponibles?', 'user_id' => $juan->id]);

        $this->actingAs($maria)->get("/applications/{$application->id}")->assertForbidden();
    }

    private function practiceOf(int $studentId): \App\Models\Practice
    {
        return \App\Models\Practice::whereHas('application.student', fn ($student) => $student->where('id_student', $studentId))->firstOrFail();
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
