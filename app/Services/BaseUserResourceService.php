<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseUserResourceService
{
    protected $repository;

    public function index(User $user): LengthAwarePaginator
    {
        return $this->repository->paginate($user);
    }

    public function show(User $user, Model $model): Model
    {
        $this->abortIfUserIsDifferent($user, $model);
        return $this->repository->find($model);
    }

    public function update(array $data, User $user, Model $model): Model
    {
        $this->abortIfUserIsDifferent($user, $model);

        return DB::transaction(function () use ($data, $user, $model) {
            $isChangingFromDefaultToFalse = ($model->is_default && isset($data['is_default']) && $data['is_default'] == false);

            $this->clearDefaultForUser($data, $user->id);

            $updatedModel = $this->repository->update($data, $model);

            if ($isChangingFromDefaultToFalse) {
                $anotherModel = $this->repository->getAnotherForUser($user->id, $updatedModel->id);
                if ($anotherModel) {
                    $this->repository->setAsDefault($anotherModel);
                } else {
                    $this->repository->setAsDefault($updatedModel);
                }
            }
            return $updatedModel;
        });
    }

    public function delete(User $user, Model $model): void
    {
        $this->abortIfUserIsDifferent($user, $model);

        DB::transaction(function () use ($user, $model) {
            $wasDefault = $model->is_default;

            $this->repository->delete($model);

            if ($wasDefault) {
                $anotherModel = $this->repository->getAnotherForUser($user->id, $model->id);

                if ($anotherModel) {
                    $this->repository->setAsDefault($anotherModel);
                }
            }
        });
    }

    protected function abortIfUserIsDifferent(User $user, Model $model): void
    {
        abort_if($model->user_id !== $user->id, 404, $this->getNotFoundMessage());
    }

    protected function clearDefaultForUser(array $data, int $userId): void
    {
        if (!empty($data['is_default'])) {
            $this->repository->clearDefaultForUser($userId);
        }
    }

    abstract protected function getNotFoundMessage(): string;
}
