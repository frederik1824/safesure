<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FirebaseSyncLog;

class SyncControlCenter extends Component
{
    public $logs = [];
    public $recentLog = null;
    public $totalRecords = 0;
    public $recordsSynced = 0;
    public $progressPercentage = 0;
    public $polling = false;
    
    public $totalEmpresas = 0;
    public $totalAfiliados = 0;
    public $totalLocales = 0;

    public $isStalled = false;
    public $webhooks = [];

    public function mount()
    {
        $this->updateStatus();
    }

    public function updateStatus()
    {
        $this->totalEmpresas = \App\Models\Empresa::count();
        $this->totalAfiliados = \App\Models\Afiliado::count();
        $this->totalLocales = $this->totalEmpresas + $this->totalAfiliados;
        
        $this->recentLog = FirebaseSyncLog::latest('id')->first();
        
        $this->logs = FirebaseSyncLog::with('user')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
            
        $this->webhooks = \App\Models\WebhookLog::orderBy('id', 'desc')
            ->limit(5)
            ->get();
            
        $this->isStalled = false;

        if ($this->recentLog) {
            $this->totalRecords = $this->recentLog->total_records;
            $this->recordsSynced = $this->recentLog->records_synced;
            
            if ($this->totalRecords > 0) {
                $this->progressPercentage = min(100, round(($this->recordsSynced / $this->totalRecords) * 100, 2));
            } else {
                $this->progressPercentage = $this->recentLog->status === 'completed' ? 100 : 0;
            }
            
            if ($this->recentLog->status === 'started' || $this->recentLog->status === 'in_progress') {
                $this->polling = true;
                
                // Detect stall if no heartbeat for > 2 minutes
                if ($this->recentLog->last_heartbeat_at && $this->recentLog->last_heartbeat_at->diffInMinutes(now()) >= 2) {
                    $this->isStalled = true;
                }
            } else {
                $this->polling = false;
            }
        }
    }

    public function forceReleaseLock()
    {
        \Illuminate\Support\Facades\Cache::forget('firebase_sync_lock');
        if ($this->recentLog && ($this->recentLog->status === 'started' || $this->recentLog->status === 'in_progress')) {
            $this->recentLog->update([
                'status' => 'failed',
                'message' => 'Liberación forzada por el administrador.',
                'completed_at' => now(),
            ]);
        }
        $this->updateStatus();
    }

    public function cancelCurrentSync()
    {
        $log = FirebaseSyncLog::whereIn('status', ['started', 'in_progress'])->latest('id')->first();
        if ($log) {
            $log->update([
                'status' => 'cancelled',
                'message' => 'Sincronización abortada por el usuario.',
                'completed_at' => now(),
            ]);
        }
        $this->polling = false;
        $this->updateStatus();
    }

    public function render()
    {
        return view('livewire.sync-control-center');
    }
}
