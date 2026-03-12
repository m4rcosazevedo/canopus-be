<?php

namespace App\Modules\Report\Contracts;

interface FormatterStrategy
{
    /**
     * @return string
     */
    public function key(): string;

    /**
     * @return int
     */
    public function priority(): int;

    /**
     * Apply the formatting to the value.
     *
     * @param mixed $value The value to format.
     * @param string $param The configuration parameter (e.g., 'BRL', 'dd/mm/yyyy').
     * @return string
     */
    public function format(mixed $value, string $param): string;
}
