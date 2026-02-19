<?php

namespace App\Modules\Report\Policies;

use App\Models\User;
use App\Modules\Report\Models\Report;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function download(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }
}
