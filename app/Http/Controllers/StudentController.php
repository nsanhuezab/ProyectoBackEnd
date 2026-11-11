<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Career;
use App\Models\Student;
use App\Models\User;
use App\Support\Notifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Perfiles de estudiante. El administrador los crea y edita; el coordinador
 * ve los de su carrera y los valida.
 */
class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isCoordinator = (bool) $user?->hasRole('Coordinador');

        $students = Student::query()
            ->with(['user:id,name,last_name,email', 'career:id,codigo,nombre', 'validator:id,name,last_name'])
            ->withCount('applications')
            ->when($isCoordinator, fn ($query) => $query->where('career_id', $user->career_id))
            ->orderBy('id_student')
            ->get();

        $counts = [
            'total' => $students->count(),
            'validados' => $students->whereNotNull('validated_at')->count(),
            'por_validar' => $students->whereNull('validated_at')->count(),
        ];

        $can = [
            'manage' => (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador']),
            'validate' => (bool) $user?->hasAnyRole(['Administrador', 'Super Administrador', 'Coordinador']),
        ];

        $careers = Career::query()->orderBy('nombre')->get(['id', 'codigo', 'nombre']);

        // Cuentas con rol Estudiante para el select del formulario
        $studentUsers = $can['manage'] ? User::role('Estudiante')->orderBy('name')->get(['id', 'name', 'last_name', 'email']) : collect();
        $usedUserIds = $students->pluck('user_id')->filter()->all();

        return view('students.index', compact('students', 'counts', 'careers', 'studentUsers', 'usedUserIds', 'can'));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $this->authorizeManage($request);
        $data = $request->validated();
        $data['carrera'] = Career::findOrFail($data['career_id'])->nombre;
        $student = Student::create($data);

        return back()->with('success', "Perfil de estudiante #{$student->id_student} creado correctamente.");
    }

    /**
     * Actualiza el perfil. La validación vuelve a quedar pendiente.
     */
    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorizeManage($request);
        $data = $request->validated();
        $data['carrera'] = Career::findOrFail($data['career_id'])->nombre;

        $student->fill($data);
        if ($student->isDirty()) {
            $student->validated_at = null;
            $student->validated_by = null;
        }
        $student->save();

        return back()->with('success', "Perfil de estudiante #{$student->id_student} actualizado correctamente.");
    }

    public function destroy(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeManage($request);
        $applications = $student->applications()->count();
        if ($applications > 0) {
            return back()->with('error', "No se puede eliminar el perfil #{$student->id_student} porque tiene {$applications} postulación(es).");
        }

        $student->delete();

        return back()->with('success', "Perfil de estudiante #{$student->id_student} eliminado correctamente.");
    }

    /**
     * El coordinador confirma que los datos del estudiante son correctos.
     */
    public function validateProfile(Request $request, Student $student): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            $user->hasAnyRole(['Administrador', 'Super Administrador'])
                || ($user->hasRole('Coordinador') && $student->career_id === $user->career_id),
            403,
        );

        $student->update([
            'validated_at' => now(),
            'validated_by' => $user->id > 0 ? $user->id : null,
        ]);

        Notifier::send(
            $student->user,
            'Perfil validado',
            'Tu coordinador validó tus datos académicos.',
            route('student.offers.view'),
        );

        return back()->with('success', "Perfil de estudiante #{$student->id_student} validado.");
    }

    private function authorizeManage(Request $request): void
    {
        abort_unless($request->user()->hasAnyRole(['Administrador', 'Super Administrador']), 403);
    }
}
