<?php

namespace App\Modules\Report\Jobs;

use App\Modules\Report\Models\Report;
use App\Modules\Report\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public int $timeout = 600; // 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Report $report)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        try {
            $this->report->update(['status' => 'processing']);

            $reportService->generate($this->report);

            if ($this->report->status !== 'failed') {
                $this->report->update(['status' => 'completed']);
            }

        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('Report generation failed for report ID ' . $this->report->id . ': ' . $exception->getMessage(), [
            'exception' => $exception
        ]);

        $this->report->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
