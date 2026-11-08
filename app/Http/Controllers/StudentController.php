<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Career;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StudentController extends Controller
{
    /**
     * Mantenedor de perfiles de estudiante (cuenta + carrera + nivel + habilidades).
     */
    public function index(): View
    {
        $students = Student::query()
            ->with(['user:id,name,last_name,email', 'career:id,codigo,nombre'])
            ->withCount('applications')
            ->orderBy('id_student')
            ->get();

        $counts = [
            'total' => $students->count(),
            'con_postulaciones' => $students->where('applications_count', '>', 0)->count(),
            'sin_postulaciones' => $students->where('applications_count', 0)->count(),
        ];

        $careers = Career::query()->orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        // Cuentas con rol Estudiante; las que ya tienen perfil se marcan en la vista.
        $studentUsers = User::role('Estudiante')->orderBy('name')->get(['id', 'name', 'last_name', 'email']);
        $usedUserIds = $students->pluck('user_id')->filter()->all();

        return view('students.index', compact('students', 'counts', 'careers', 'studentUsers', 'usedUserIds'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['carrera'] = Career::findOrFail($data['career_id'])->nombre;
        $student = Student::create($data);

        return back()->with('success', "Perfil de estudiante #{$student->id_student} creado correctamente.");
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();
        $data['carrera'] = Career::findOrFail($data['career_id'])->nombre;
        $student->update($data);

        return back()->with('success', "Perfil de estudiante #{$student->id_student} actualizado correctamente.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $applications = $student->applications()->count();
        if ($applications > 0) {
            return back()->with('error', "No se puede eliminar el perfil #{$student->id_student} porque tiene {$applications} postulación(es).");
        }

        $student->delete();

        return back()->with('success', "Perfil de estudiante #{$student->id_student} eliminado correctamente.");
    }
}
