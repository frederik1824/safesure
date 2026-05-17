<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FirebaseSyncLog;

echo "--- RECENT 5 SYNC LOGS DETAILS ---\n";
$logs = FirebaseSyncLog::latest()->limit(5)->get();
foreach ($logs as $l) {
    echo "Log ID: {$l->id}\n";
    echo "  Type: {$l->type}\n";
    echo "  Status: {$l->status}\n";
    echo "  Message: " . var_export($l->message, true) . "\n";
    echo "  Records Total: {$l->total_records} | Synced: {$l->records_synced} | Added: {$l->records_added} | Updated: {$l->records_updated} | Failed: {$l->records_failed} | Skipped: {$l->records_skipped}\n";
    echo "  Started: {$l->started_at} | Completed: {$l->completed_at}\n\n";
}
