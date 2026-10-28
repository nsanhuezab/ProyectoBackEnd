<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasDataTable;
use App\Http\Requests\StudentRequest;
use App\Models\Career;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    use HasDataTable;

    public function index(Request $request): JsonResponse
    {
        return $this->dataTableResponse(
            $request,
            Student::query()->with(['user:id,name,last_name,email', 'career:id,nombre']),
            searchable: ['id_student', 'nivel_avance', 'carrera'],
            sortable: [
                'id' => 'id',
                'id_student' => 'id_student',
                'nivel_avance' => 'nivel_avance',
            ],
            transform: fn (Student $student) => $this->format($student),
            defaultSort: 'id_student',
            extraSearch: fn ($query, $like) => $query
                ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $like)->orWhere('email', 'like', $like))
                ->orWhereHas('career', fn ($career) => $career->where('nombre', 'like', $like)),
        );
    }

    public function store(StudentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);
        abort_unless($user->hasRole('Estudiante'), 422, 'El perfil debe vincularse a una cuenta con rol Estudiante.');
        $career = Career::findOrFail($data['career_id']);
        $data['carrera'] = $career->nombre;
        $student = Student::create($data)->load(['user:id,name,last_name,email', 'career:id,nombre']);

        return response()->json($this->format($student), 201);
    }

    public function update(StudentRequest $request, Student $student): JsonResponse
    {
        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);
        abort_unless($user->hasRole('Estudiante'), 422, 'El perfil debe vincularse a una cuenta con rol Estudiante.');
        $data['carrera'] = Career::findOrFail($data['career_id'])->nombre;
        $student->update($data);

        return response()->json($this->format($student->load(['user:id,name,last_name,email', 'career:id,nombre'])));
    }

    public function destroy(Student $student): Response
    {
        abort_if($student->applications()->exists(), 409, 'No puedes eliminar un perfil con postulaciones.');
        $student->delete();

        return response()->noContent();
    }

    private function format(Student $student): array
    {
        return [
            'id' => $student->id,
            'id_student' => $student->id_student,
            'user_id' => $student->user_id,
            'estudiante' => $student->user?->name.' '.$student->user?->last_name,
            'correo' => $student->user?->email,
            'career_id' => $student->career_id,
            'carrera' => $student->career?->nombre ?? $student->carrera,
            'nivel_avance' => $student->nivel_avance,
            'habilidades' => $student->habilidades ?? [],
        ];
    }
}
