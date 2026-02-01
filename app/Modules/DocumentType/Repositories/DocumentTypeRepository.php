<?php

namespace App\Modules\DocumentType\Repositories;

use App\Modules\DocumentType\Models\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DocumentTypeRepository
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->baseQuery($request)->paginate();
    }

    public function options(Request $request): Collection
    {
        $options = $this->baseQuery($request)
            ->orderBy('name')
            ->get();

        return $options->map(fn($item) => [
            'value' => $item->id,
            'label' => $item->name,
        ]);
    }

    public function find(DocumentType $documentType): DocumentType
    {
        return $documentType;
    }

    public function create(array $data): DocumentType
    {
        return DocumentType::create($data);
    }

    public function update(DocumentType $documentType, array $data): DocumentType
    {
        $documentType->update($data);

        return $documentType->refresh();
    }

    public function delete(DocumentType $documentType): bool
    {
        return (bool) $documentType->delete();
    }

    private function baseQuery(Request $request): Builder
    {
        return DocumentType::query();
//            ->filter();
    }
}
