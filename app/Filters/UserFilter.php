<?php

namespace App\Filters;

use App\Builders\SortableQueryFilter;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserFilter extends SortableQueryFilter
{
    protected array $sortable = [
        'id'        => 'id',
        'name'      => 'name',
        'email'     => 'email',
        'createdAt' => 'created_at',
    ];

    public function id($id)
    {
        $id = (int) $id;
        return $this->builder->where('id', '=', $id);
    }

    public function name(string $name)
    {
        return $this->builder->where('name', 'LIKE', '%' . $name . '%');
    }

    public function email(string $email): void
    {
        $value = addcslashes(trim($email), '%_');

        $this->builder->where('email', 'like', "%{$value}%");
    }

    public function onlyWithDefaultDocument($value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($value !== true) {
            return;
        }

        $this->builder->with([
            'documents' => function (HasMany $query) {
                $query->where('is_default', true);
            }
        ]);
    }

    public function onlyWithDefaultAddress($value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($value !== true) {
            return;
        }

        $this->builder->with([
            'addresses' => function (HasMany $query) {
                $query->where('is_default', true);
            }
        ]);
    }

    public function type($type)
    {
        $type = (int) $type;
        return $this->builder->where('user_type_id', '=', $type);
    }
}
