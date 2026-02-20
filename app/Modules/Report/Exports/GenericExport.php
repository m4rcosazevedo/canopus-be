<?php

namespace App\Modules\Report\Exports;

use Generator;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GenericExport implements FromGenerator, WithHeadings, WithMapping
{
    /**
     * @param Generator $generator O generator que fará o yield linha a linha do arquivo temporário
     * @param array $fields As configurações dos campos do relatório
     */
    public function __construct(
        protected Generator $generator,
        protected array $fields
    ) {}

    /**
     * Informa ao Maatwebsite como obter os dados.
     * Como usamos Generator, ele puxará sob demanda, mantendo o uso de RAM baixíssimo.
     */
    public function generator(): Generator
    {
        return $this->generator;
    }

    /**
     * Define a primeira linha do Excel (Cabeçalhos).
     */
    public function headings(): array
    {
        return array_map(fn($field) => $field['title'], $this->fields);
    }

    /**
     * Mapeia cada linha ($row) originada do Generator.
     * Isso garante que, mesmo que o array associativo do JSONL venha com as
     * chaves fora de ordem, o Excel sempre colocará o dado na coluna certa.
     */
    public function map($row): array
    {
        $mappedRow = [];

        foreach ($this->fields as $field) {
            // Como no seu DataProcessor você salvou as chaves usando o $field['title'],
            // nós buscamos exatamente por ele aqui. Se não existir, retorna vazio.
            $mappedRow[] = $row[$field['title']] ?? '';
        }

        return $mappedRow;
    }
}
