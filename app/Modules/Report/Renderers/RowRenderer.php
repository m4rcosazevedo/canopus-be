<?php

namespace App\Modules\Report\Renderers;

class RowRenderer
{
    public function render(array $fields, array $row): string
    {
        $tr = '<tr>';

        foreach ($fields as $field) {
            $value = data_get($row, $field['title']) ?? '';
            $tr .= '<td>' . htmlspecialchars((string) $value) . '</td>';
        }

        $tr .= '</tr>';

        return $tr;
    }

    public function renderAggregation(array $fields, array $aggregations): string
    {
        $tr = '<tr style="font-weight:bold;background:#f5f5f5;">';

        foreach ($fields as $field) {

            $key = $field['title'];

            $value = $aggregations[$key] ?? '';

            $tr .= '<td>' . htmlspecialchars((string) $value) . '</td>';
        }

        $tr .= '</tr>';

        return $tr;
    }

    public function renderFooter(int $colspan, int $count): string
    {
        $str = '<tr>';
        $str .= "<td colspan='{$colspan}' style='text-align: right; font-weight: bold; background-color: #f2f2f2;'>Total de Registros: {$count}</td>";
        $str .= '</tr>';

        return $str;

    }

}
