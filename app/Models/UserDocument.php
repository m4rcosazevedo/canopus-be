<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends BaseModel
{
    public const DEFAULT_RELATIONS = ['user', 'documentType'];

    protected $fillable = [
        'user_id',
        'document_type_id',
        'number',
        'issuer',
        'state',
        'issued_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
