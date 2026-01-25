<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class GenericExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $fields;

    public function __construct($data, $fields)
    {
        $this->data = $data instanceof Collection ? $data : collect($data);
        $this->fields = $fields;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {;
        return array_map(function($field) {
            return $field['title'];
        }, $this->fields);
    }
}
