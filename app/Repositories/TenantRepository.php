<?php

namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantRepository
{
    public function paginate(): LengthAwarePaginator
    {
        return Tenant::query()
            ->paginate();
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(Tenant $model, array $data): ?Tenant
    {
        $model->update($data);

        return $model->refresh();
    }

    public function destroy(Tenant $model)
    {
        // TODO adicionar um status e mudar para inativo
    }
}
