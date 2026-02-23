<?php

namespace App\Modules\DocumentType\Models;

use App\Models\BaseModel;
use App\Models\UserDocument;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends BaseModel
{
    public const CPF = 1;
    public const CNPJ = 3;


    protected $fillable = [
        'name',
        'description'
    ];

    /** Relationships */
    public function userDocuments(): HasMany
    {
        return $this->hasMany(UserDocument::class, 'document_type_id');
    }
}
