<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoordinatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'correo' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($this->route('coordinator')),
            ],
            'telefono' => ['nullable', 'string', 'max:20'],
            'career_id' => ['required', 'integer', 'exists:careers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'career_id.required' => 'Debes seleccionar una carrera.',
            'career_id.exists' => 'La carrera seleccionada no existe.',
        ];
    }
}
