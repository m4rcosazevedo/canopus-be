<?php

namespace App\Modules\Report\Services\Formatters;

use App\Modules\Report\Contracts\FormatterStrategy;

final class SanitizeFormatter implements FormatterStrategy
{
    public function key(): string
    {
        return 'sanitize';
    }

    public function priority(): int
    {
        return 100; // sempre primeiro
    }

    public function format(mixed $value, string $param): string
    {
        $value = (string) $value;

        return match ($param) {
            'onlyNumbers' => preg_replace('/\D+/', '', $value),

            'onlyLetters' => preg_replace('/[^a-zA-Z]+/', '', $value),

            'onlyNumbersAndLetters' => preg_replace('/[^a-zA-Z0-9]+/', '', $value),

            default => $value,
        };
    }
}
