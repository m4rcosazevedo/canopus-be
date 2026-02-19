<?php

namespace App\Repositories;

use App\Filters\ReportFilter;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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
