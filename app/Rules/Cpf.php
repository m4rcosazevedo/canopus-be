<?php

namespace App\Rules;

use App\Helpers\DocumentHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!DocumentHelper::isCpfValid((string) $value)) {
            $fail('O CPF informado é inválido.');
        }
    }
}
