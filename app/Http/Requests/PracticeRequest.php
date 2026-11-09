<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PracticeRequest extends FormRequest
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
        return [
            'application_id' => [Rule::requiredIf($this->isMethod('POST')), 'nullable', 'integer', 'exists:applications,id'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_termino' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'hired' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'application_id' => 'postulación',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_termino' => 'fecha de término',
            'hired' => 'contratado',
        ];
    }
}
