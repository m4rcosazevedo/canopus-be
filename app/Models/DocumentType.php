<?php

namespace App\Models;

class DocumentType extends BaseModel
{
    public const CPF = 1;
    public const CNPJ = 3;


    protected $fillable = [
        'name',
        'description'
    ];
}
