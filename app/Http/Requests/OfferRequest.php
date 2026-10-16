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
            'empresa' => ['required', 'string', 'max:255'],
            'vacantes' => ['required', 'integer', 'min:1', 'max:1000'],
            'estado' => ['required', Rule::in(['Disponible', 'Cerrada'])],
        ];
    }
}
