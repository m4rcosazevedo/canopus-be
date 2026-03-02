<?php

namespace App\Modules\Subject\Repositories;

use App\Builders\QueryFilter;
use App\Modules\Subject\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectRepository
{
    public function __construct(
        protected Subject $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(QueryFilter $filters): LengthAwarePaginator
    {
        return $this->model
            ->with(['area'])
            ->filter($filters)
            ->paginate();
    }

    public function findById(int $id): ?Subject
    {
        return $this->model
            ->with(['area'])
            ->find($id);
    }

    public function create(array $data): Subject
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Subject
    {
        $subject = $this->findById($id);

        if (!$subject) {
            return null;
        }

        $subject->update($data);

        return $subject->refresh();
    }

    public function delete(int $id): bool
    {
        $subject = $this->findById($id);

        return (bool)$subject?->delete();
    }
}
