<?php

namespace App\Models;

use App\Modules\State\Model\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends BaseModel
{
    public const DEFAULT_RELATIONS = ['documentType', 'user', 'state'];

    protected $fillable = [
        'user_id',
        'document_type_id',
        'number',
        'issuer',
        'state_id',
        'issued_at',
        'is_default'
    ];

    protected $casts = [
        'issued_at' => 'date',
        'is_default' => 'boolean',
    ];

    /** Relationships */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /** Scopes */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

}
