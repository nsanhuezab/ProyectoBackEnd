<?php

namespace App\Rules;

use App\Support\Rut;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RutChileno implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Rut::isValid(is_string($value) ? $value : null)) {
            $fail('El campo :attribute no tiene un formato de RUT válido (ej: 12.345.678-5).');
        }
    }
}
