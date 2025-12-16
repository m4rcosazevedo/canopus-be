<?php

namespace App\Filters;

use App\Builders\QueryFilter;

class UserFilter extends QueryFilter
{
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

    public function type($type)
    {
        $type = (int) $type;
        return $this->builder->where('user_type_id', '=', $type);
    }
}
