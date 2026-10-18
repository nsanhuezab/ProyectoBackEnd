<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'vacantes' => ['required', 'integer', 'min:1', 'max:1000'],
            'empresa_id' => ['required', 'integer', 'exists:companies,id'],
            'career_ids' => ['required', 'array', 'min:1'],
            'career_ids.*' => ['integer', 'distinct', 'exists:careers,id'],
            'area' => ['required', 'string', 'max:120'],
            'perfil' => ['required', 'string', 'max:5000'],
            'nivel_requerido' => ['required', 'string', 'max:120'],
            'modalidad' => ['required', Rule::in(['Presencial', 'Remota', 'Híbrida'])],
            'duracion_semanas' => ['required', 'integer', 'between:1,104'],
            'horario' => ['required', 'string', 'max:255'],
            'remuneracion' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'habilidades' => ['required', 'array', 'min:1', 'max:30'],
            'habilidades.*' => ['string', 'max:100'],
            'ubicacion' => ['required', 'string', 'max:255'],
        ];
    }
}
