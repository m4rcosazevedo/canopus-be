<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupAndPruneAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:backup-clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera CSV dos logs antigos e remove do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateLimit = now()->subMonth();
        $logsToExport = AuditLog::where('created_at', '<=', $dateLimit);

        if ($logsToExport->count() === 0) {
            $this->info('Nenhum log para limpar.');
            Log::info('BackupAndPruneAuditLogs: Nenhum log para limpar.');
            return;
        }

        $fileName = 'audit_backup_' . Carbon::now()->format('Y_m_d_His') . '.csv';
        $filePath = 'logs/' . $fileName;

        $handle = fopen(storage_path($filePath), 'w');

        fputcsv($handle, ['id', 'user_id', 'user_email', 'event', 'transaction_id', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'url', 'ip_address', 'created_at']);

        $logsToExport->chunk(1000, function ($logs) use ($handle) {
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id, $log->user_id, $log->user_email, $log->event,
                    $log->transaction_id, $log->auditable_type, $log->auditable_id,
                    json_encode($log->old_values), json_encode($log->new_values),
                    $log->url, $log->ip_address, $log->created_at
                ]);
            }
        });

        fclose($handle);
        $this->info("Backup criado: storage/logs/{$fileName}");
        Log::info("BackupAndPruneAuditLogs: Backup criado: storage/logs/{$fileName}");

        $this->call('model:prune', [
            '--model' => [AuditLog::class],
        ]);

        $this->info('Limpeza concluída com sucesso.');
        Log::info("BackupAndPruneAuditLogs: Limpeza concluída com sucesso.");
    }
}
