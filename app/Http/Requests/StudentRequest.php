<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'habilidades' => ['nullable', 'array', 'max:30'],
            'habilidades.*' => ['string', 'max:100'],
        ];
    }
}
