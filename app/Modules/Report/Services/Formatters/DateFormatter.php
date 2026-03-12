<?php

namespace App\Modules\Report\Services\Formatters;

use App\Modules\Report\Contracts\FormatterStrategy;
use Carbon\Carbon;
use Exception;

final class DateFormatter implements FormatterStrategy
{
    public function key(): string
    {
        return 'date';
    }

    public function priority(): int
    {
        return 10;
    }

    public function format(mixed $value, string $param): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            $date = Carbon::parse($value);

            $phpFormat = str_ireplace(
                ['dd', 'mm', 'yyyy', 'hh', 'ii', 'ss'],
                ['d', 'm', 'Y', 'H', 'i', 's'],
                strtolower($param)
            );

            return $date->format($phpFormat);
        } catch (Exception $e) {
            return (string) $value;
        }
    }
}
