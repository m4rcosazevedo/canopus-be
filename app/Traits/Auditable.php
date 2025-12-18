<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(fn ($model) => $model->audit('created'));
        static::updated(fn ($model) => $model->audit('updated'));
        static::deleted(fn ($model) => $model->audit('deleted'));
    }

    protected function audit(string $event)
    {
        $user = Auth::user();

        $attributes = $this->getAttributes();
        $original = $this->getRawOriginal();

        if (isset($this->auditExclude)) {
            $attributes = array_diff_key($attributes, array_flip($this->auditExclude));
            $original = array_diff_key($original, array_flip($this->auditExclude));
        }

        AuditLog::create([
            'user_id'       => $user?->id,
            'user_email'    => $user?->email,
            'event'         => $event,
            'transaction_id' => Context::get('audit_transaction_id'),
            'auditable_id'  => $this->id,
            'auditable_type' => get_class($this),
            'old_values'    => $event === 'created' ? null : $original,
            'new_values'    => $event === 'deleted' ? null : $attributes,
            'url'           => request()->fullUrl(),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}
