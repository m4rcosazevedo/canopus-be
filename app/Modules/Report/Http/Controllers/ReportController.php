<?php

declare(strict_types=1);

namespace App\Modules\Report\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Report\Http\Requests\StoreReportRequest;
use App\Modules\Report\Http\Resources\ReportResource;
use App\Modules\Report\Models\Report;
use App\Modules\Report\Repositories\ReportRepository;
use App\Modules\Report\Services\ReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ReportService $reportService,
        protected ReportRepository $repository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Report::class);

        return ReportResource::collection(
            $this->repository->paginate($request)
        );
    }

    public function store(StoreReportRequest $request): ReportResource
    {
        $this->authorize('create', Report::class);

        $report = $this->reportService->createReport(
            $request->user(),
            $request->validated()
        );

        return new ReportResource($report->load('user'));
    }

    public function show(Report $report): ReportResource
    {
        $this->authorize('view', $report);

        return new ReportResource($this->repository->find($report));
    }

    public function download(Report $report): JsonResponse|StreamedResponse
    {
        $this->authorize('download', $report);

        if ($report->status !== 'completed') {
            return response()->json(['message' => 'Relatório ainda não está pronto'], 400);
        }

        if (!Storage::disk('local')->exists($report->path)) {
            return response()->json(['message' => 'Arquivo não encontrado'], 404);
        }

        return Storage::disk('local')->download($report->path);
    }
}
