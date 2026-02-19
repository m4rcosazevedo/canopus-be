<?php

namespace App\Modules\Report\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

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
