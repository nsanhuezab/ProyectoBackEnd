<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $company = $this->route('company');

        return [
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'razon_social' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:15', Rule::unique('companies', 'rut')->ignore($company)],
            'rubro' => ['required', 'string', 'max:120'],
            'direccion' => ['required', 'string', 'max:255'],
            'sitio_web' => ['nullable', 'url', 'max:255'],
            'contacto_nombre' => ['required', 'string', 'max:255'],
            'contacto_email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
        ];
    }
}
