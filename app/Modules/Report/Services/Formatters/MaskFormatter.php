<?php

namespace App\Modules\Report\Services\Formatters;

use App\Modules\Report\Contracts\FormatterStrategy;

final class MaskFormatter implements FormatterStrategy
{
    public function key(): string
    {
        return 'mask';
    }

    public function priority(): int
    {
        return 10;
    }

    public function format(mixed $value, string $param): string
    {
        $value = (string) $value;
        $mask = $param;

        $masked = '';
        $k = 0;
        for ($i = 0; $i < strlen($mask); $i++) {
            if ($mask[$i] === '#') {
                if (isset($value[$k])) {
                    $masked .= $value[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $masked .= $mask[$i];
                }
            }
        }

        return $masked;
    }
}
