<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EvaluationRequest extends FormRequest
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
            'tipo' => ['required', Rule::in(['supervisor', 'coordinador', 'estudiante'])],
            'calificacion' => ['required', 'numeric', 'between:1,7'],
            'competencias' => ['nullable', 'array', 'max:30'],
            'competencias.*' => ['string', 'max:120'],
            'comentarios' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
