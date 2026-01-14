<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->resource->id,
            "user"          => new UserResource($this->whenLoaded('user')),
            "documentType"  => new DocumentTypeResource($this->whenLoaded('documentType')),
            "number"        => $this->resource->number,
            "issuer"        => $this->resource->issuer,
            "state"         => new StateResource($this->whenLoaded('state')),
            "issuedAt"      => $this->resource->issued_at ? Carbon::parse($this->resource->issued_at)->format('Y-m-d') : null,
            "createdAt"     => $this->resource->created_at,
            "updatedAt"     => $this->resource->updated_at,
        ];
    }

}
