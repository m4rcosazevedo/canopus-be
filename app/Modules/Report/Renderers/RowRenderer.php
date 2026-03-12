<?php

namespace App\Modules\Report\Renderers;

use App\Modules\Report\Services\FormatterService;

class RowRenderer
{
    public function __construct(
        protected FormatterService $formatter
    ) {}

    public function render(array $fields, array $row): string
    {
        return $this->renderRow($fields, fn ($field) => data_get($row, $field['title']));
    }

    public function renderAggregation(array $fields, array $aggregations): string
    {
        return $this->renderRow(
            $fields,
            fn ($field) => $aggregations[$field['title']] ?? '',
            'font-weight:bold;background:#f5f5f5;'
        );
    }

    public function renderFooter(int $colspan, int $count): string
    {
        return sprintf(
            "<tr><td colspan='%d' style='text-align:right;font-weight:bold;background-color:#f2f2f2;'>Total de Registros: %d</td></tr>",
            $colspan,
            $count
        );
    }

    protected function renderRow(array $fields, callable $valueResolver, string $style = ''): string
    {
        $styleAttr = $style ? " style=\"{$style}\"" : '';
        $cells = '';

        foreach ($fields as $field) {
            $value = $valueResolver($field);
            $value = $this->formatIfNeeded($field, $value);

            $cells .= $this->renderCell($value);
        }

        return "<tr{$styleAttr}>{$cells}</tr>";
    }

    protected function renderCell(mixed $value): string
    {
        return '<td>' . $this->escape($value) . '</td>';
    }

    protected function formatIfNeeded(array $field, mixed $value): mixed
    {
        if (!empty($field['format'])) {
            return $this->formatter->format($value, $field['format']);
        }

        return $value;
    }

    protected function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
