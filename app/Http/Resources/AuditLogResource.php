<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->resource->id,
            'transactionId' => $this->resource->transaction_id,
            'user' => [
                'id'    => $this->resource->user_id,
                'email' => $this->resource->user_email,
            ],
            'action' => [
                'event'     => $this->resource->event,
                'model'     => $this->getModel($this->resource->auditable_type),
                'modelId'   => $this->resource->auditable_id,
            ],
            'changes' => $this->resource->when($this->resource->event === 'updated', function () {
                return $this->getDiff();
            }),
            'fullData' => [
                'before' => $this->resource->old_values,
                'after'  => $this->resource->new_values,
            ],
            'metadata' => [
                'ip'        => $this->resource->ip_address,
                'userAgent' => $this->resource->user_agent,
                'url'       => $this->resource->url,
                'createdAt' => $this->resource->created_at,
            ],
        ];
    }

    private function getModel(string $str): string
    {
        if ($str === '') {
            return '';
        }

        $parts = explode('\\', $str);
        $last = end($parts);

        return $last !== false ? $last : '';
    }
}
