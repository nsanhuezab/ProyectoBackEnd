<?php

namespace App\Http\Requests;

use App\Models\Company;
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
            'min_gpa' => ['nullable', 'numeric', 'between:1,7'],
            'modalidad' => ['required', Rule::in(['Presencial', 'Remota', 'Híbrida'])],
            'duracion_semanas' => ['required', 'integer', 'between:1,104'],
            'horario' => ['required', 'string', 'max:255'],
            'remuneracion' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'habilidades' => ['required', 'array', 'min:1', 'max:30'],
            'habilidades.*' => ['string', 'max:100'],
            'ubicacion' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Convierte las habilidades en lista y, si publica una empresa, usa su propia empresa.
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

        if ($this->user()?->hasRole('Empresa')) {
            $this->merge([
                'empresa_id' => Company::where('owner_user_id', $this->user()->id)->value('id'),
            ]);
        }
    }

    public function attributes(): array
    {
        return [
            'empresa_id' => 'empresa',
            'career_ids' => 'carreras',
            'nivel_requerido' => 'nivel requerido',
            'min_gpa' => 'promedio mínimo',
            'duracion_semanas' => 'duración',
            'remuneracion' => 'remuneración',
            'ubicacion' => 'ubicación',
        ];
    }
}
