<?php

namespace App\Modules\Report\Services;

use App\Modules\Report\Contracts\FormatterStrategy;
use App\Modules\Report\Services\Formatters\CurrencyFormatter;
use App\Modules\Report\Services\Formatters\DateFormatter;
use App\Modules\Report\Services\Formatters\MaskFormatter;
use App\Modules\Report\Services\Formatters\SanitizeFormatter;

class FormatterService
{
    /** @var FormatterStrategy[] */
    protected array $formatters = [];

    public function __construct()
    {
        foreach ($this->registers() as $formatter) {
            $this->formatters[$formatter->key()] = $formatter;
        }
    }

    public function format(mixed $value, ?array $formatConfig): string
    {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return '';
        }

        if (!$formatConfig) {
            return (string) $value;
        }

        $pipeline = [];
        foreach ($formatConfig as $type => $param) {
            if (!isset($this->formatters[$type])) {
                continue;
            }

            $formatter = $this->formatters[$type];

            $pipeline[] = [
                'formatter' => $formatter,
                'param' => $param,
                'priority' => $formatter->priority(),
            ];
        }

        usort($pipeline, fn ($a, $b) => $b['priority'] <=> $a['priority']);

        foreach ($pipeline as $step) {
            $value = $step['formatter']->format($value, $step['param']);
        }

        return (string) $value;
    }

    protected function registers(): array
    {
        return [
            new SanitizeFormatter(),
            new CurrencyFormatter(),
            new DateFormatter(),
            new MaskFormatter(),
        ];
    }
}
