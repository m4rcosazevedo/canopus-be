<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'template' => 'nullable|string',
            'endpoint' => 'required|url',
            'authenticated' => 'boolean',
//            'token' => 'nullable|string|required_if:authenticated,true', // Token is required if authenticated is true, unless it's internal (handled below)
            'token' => 'nullable|string', // Token is required if authenticated is true, unless it's internal (handled below)
            'options' => 'required|array',
            'options.type' => 'required|in:pdf,csv,xlsx',
            'options.queryParams' => 'nullable|array',
            'options.fields' => 'required|array',
            'options.title' => 'nullable|string',
            'options.queryDisplay' => 'nullable|array',
        ]);

        $token = $request->input('token');
        $authenticated = $request->boolean('authenticated');

        $report = Report::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'] ?? 'report_' . now()->timestamp,
            'format' => $validated['options']['type'],
            'template' => $validated['template'],
            'endpoint' => $validated['endpoint'],
            'authenticated' => $authenticated,
            'token' => $token, // Can be null if internal auth is needed
            'parameters' => $validated['options'],
            'status' => 'pending',
        ]);

        GenerateReportJob::dispatch($report);

        return response()->json([
            'message' => 'Report generation started',
            'report' => $report
        ], 202);
    }

    public function download(Report $report)
    {
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        if ($report->status !== 'completed') {
            return response()->json(['message' => 'Report is not ready yet'], 400);
        }

        if (!Storage::disk('local')->exists($report->path)) {
             return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('local')->download($report->path);
    }

    public function index()
    {
        return Report::where('user_id', auth()->id())->latest()->get();
    }

    public function show(Report $report)
    {
         if ($report->user_id !== auth()->id()) {
            abort(403);
        }
        return $report;
    }
}
