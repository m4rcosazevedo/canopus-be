<?php

namespace App\Modules\Report\Repositories;

use App\Modules\Report\Filters\ReportFilter;
use App\Modules\Report\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportRepository
{
    private const RELATIONS = ['user'];

    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->baseQuery($request)->paginate();
    }

    public function find(Report $report): Report
    {
        return $report->load(self::RELATIONS);
    }

    private function baseQuery(Request $request): Builder
    {
        return Report::with(self::RELATIONS)
                ->filter(new ReportFilter($request))
                ->where('user_id', $request->user()->id)
                ->latest();
    }
}
