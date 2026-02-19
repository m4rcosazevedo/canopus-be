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

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;

    /**
     * Create a new job instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        try {
            $this->report->update(['status' => 'processing']);
            $reportService->generate($this->report);
            $this->report->update(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('Report generation failed: ' . $e->getMessage());
            $this->report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
