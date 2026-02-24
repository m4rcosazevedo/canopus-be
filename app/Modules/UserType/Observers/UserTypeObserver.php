<?php

namespace App\Modules\UserType\Observers;

use App\Modules\UserType\Model\UserType;
use Illuminate\Support\Facades\Cache;

class UserTypeObserver
{
    /**
     * Disparado sempre que o UserType for atualizado (ex: mudou o nome).
     */
    public function updated(UserType $userType): void
    {
        $this->clearCache($userType);
    }

    /**
     * Disparado quando as permissões são alteradas (se houver evento).
     * Dica: Se usar o método sync() do Eloquent, você pode disparar isso manualmente.
     */
    public function permissionsChanged(UserType $userType): void
    {
        $this->clearCache($userType);
    }

    private function clearCache(UserType $userType): void
    {
        Cache::forget("permissions_role_{$userType->id}");
    }
}
