<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'id_student' => ['required', 'integer', 'min:1', Rule::unique('students', 'id_student')->ignore($student)],
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('students', 'user_id')->ignore($student)],
            'career_id' => ['required', 'integer', 'exists:careers,id'],
            'nivel_avance' => ['required', 'string', 'max:120'],
            'year_level' => ['nullable', 'integer', 'between:1,7'],
            'gpa' => ['nullable', 'numeric', 'between:1,7'],
            'habilidades' => ['nullable', 'array', 'max:30'],
            'habilidades.*' => ['string', 'max:100'],
        ];
    }

    /**
     * Convierte las habilidades (separadas por coma) en lista.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('habilidades'))) {
            $this->merge([
                'habilidades' => collect(explode(',', $this->input('habilidades')))
                    ->map(fn (string $skill) => trim($skill))
                    ->filter()
                    ->values()
                    ->all(),
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $user = User::find($this->input('user_id'));
            if ($user && ! $user->hasRole('Estudiante')) {
                $validator->errors()->add('user_id', 'El perfil debe vincularse a una cuenta con rol Estudiante.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'id_student' => 'ID de estudiante',
            'user_id' => 'cuenta de estudiante',
            'career_id' => 'carrera',
            'nivel_avance' => 'nivel de avance',
            'year_level' => 'año que cursa',
            'gpa' => 'promedio de notas',
        ];
    }
}
