<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PurgeSyncLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:purge-logs {--days=30 : Días de retención}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purga logs de sincronización y auditoría antiguos para liberar espacio.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $date = now()->subDays($days);

        $this->info("Iniciando purga de logs anteriores a: {$date->format('Y-m-d')}");

        $syncLogsDeleted = \App\Models\FirebaseSyncLog::where('created_at', '<', $date)->delete();
        $auditLogsDeleted = \App\Models\AuditLog::where('created_at', '<', $date)->delete();
        $webhookLogsDeleted = \App\Models\WebhookLog::where('created_at', '<', $date)->delete();

        $this->info("Purga completada.");
        $this->line("- Firebase Sync Logs eliminados: {$syncLogsDeleted}");
        $this->line("- Audit Logs eliminados: {$auditLogsDeleted}");
        $this->line("- Webhook Logs eliminados: {$webhookLogsDeleted}");
        
        \Illuminate\Support\Facades\Log::info("Nexus Purge: Eliminados {$syncLogsDeleted} sync logs, {$auditLogsDeleted} audit logs y {$webhookLogsDeleted} webhook logs.");
    }
}
