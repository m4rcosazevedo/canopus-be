<?php

namespace App\Modules\Report\Services\Formatters;

use App\Modules\Report\Contracts\FormatterStrategy;

final class CurrencyFormatter implements FormatterStrategy
{
    public function key(): string
    {
        return 'currency';
    }

    public function priority(): int
    {
        return 10;
    }

    public function format(mixed $value, string $param): string
    {
        $value = (float) $value;

        $config = match ($param) {
            'BRL' => [
                'symbol' => 'R$',
                'decimal' => ',',
                'thousand' => '.',
                'symbol_first' => true
            ],
            'USD' => [
                'symbol' => '$',
                'decimal' => '.',
                'thousand' => ',',
                'symbol_first' => true
            ],
            'EUR' => [
                'symbol' => '€',
                'decimal' => ',',
                'thousand' => '.',
                'symbol_first' => false
            ],
            default => [
                'symbol' => $param,
                'decimal' => ',',
                'thousand' => '.',
                'symbol_first' => true
            ],
        };

        $formatted = number_format($value, 2, $config['decimal'], $config['thousand']);

        if ($config['symbol_first']) {
            return "{$config['symbol']} {$formatted}";
        }

        return "{$formatted} {$config['symbol']}";
    }
}
