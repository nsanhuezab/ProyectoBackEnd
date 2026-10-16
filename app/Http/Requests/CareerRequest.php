<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => [
                'required', 'string', 'max:20',
                Rule::unique('careers', 'codigo')->ignore($this->route('career')),
            ],
            'supervisor' => ['required', 'numeric', 'between:0,100'],
            'coordinador' => ['required', 'numeric', 'between:0,100'],
            'activa' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if ($v->errors()->hasAny(['supervisor', 'coordinador'])) {
                return;
            }
            $suma = (float) $this->input('supervisor') + (float) $this->input('coordinador');
            if (abs($suma - 100) > 0.001) {
                $v->errors()->add('coordinador', 'Los porcentajes de supervisor y coordinador deben sumar 100.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('codigo')) {
            $this->merge(['codigo' => strtoupper(trim((string) $this->input('codigo')))]);
        }
    }
}
