<?php

namespace App\Modules\Report\Exports;

use Iterator;
use Maatwebsite\Excel\Concerns\FromIterator;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromIterator, WithHeadings
{
    protected $iterator;
    protected $fields;

    public function __construct(Iterator $iterator, $fields)
    {
        $this->iterator = $iterator;
        $this->fields = $fields;
    }

    public function iterator(): Iterator
    {
        return $this->iterator;
    }

    public function headings(): array
    {
        return array_map(function($field) {
            return $field['title'];
        }, $this->fields);
    }
}
