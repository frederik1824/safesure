<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FirebaseSyncLog;
use Livewire\WithPagination;

class SyncControlCenter extends Component
{
    use WithPagination;
    public $logs = [];
    public $recentLog = null;
    public $totalRecords = 0;
    public $recordsSynced = 0;
    public $progressPercentage = 0;
    public $polling = false;
    
    public $totalEmpresas = 0;
    public $totalAfiliados = 0;
    // $totalLocales removed — computed from $totalAfiliados + $totalEmpresas in the view

    public $isStalled = false;
    public $webhooks = [];
    public $pendingJobs = 0;
    
    // Navegación de Vistas
    public $activeTab = 'dashboard'; // dashboard, records, timeline, conflicts
    
    // Métricas y Datos
    public $totalConflicts = 0;
    public $totalPendingSync = 0;
    public $totalErrors = 0;
    public $auditLogs = [];
    public $pendingPush = 0;
    
    // Filtros para registros
    public $search = '';
    public $filterStatus = 'all';
    public $filterOrigin = 'all';
    
    // Estado de Conexión (Simulado/Real)
    public $systemStatus = [
        'firebase' => 'online',
        'workers' => 'active',
        'safe_node' => 'online',
        'cmd_node' => 'online'
    ];

    // Métodos de Control de Cuota (SafeSync)
    public $dailyReads = 0;
    public $dailyWrites = 0;
    public $accumulatedReads = 0;
    public $accumulatedWrites = 0;
    public $accumulatedCost = 0.0;
    public $estimatedCost = 0.0;
    public $isCircuitOpen = false;
    public $checkpoints = [];

    // Detalle de Registro
    public $selectedRecordId = null;
    public $selectedRecordDetail = null;
    public $showModal = false;
    public $liveFeed = [];
    public $lastSyncSummary = null;
    public $isLockActive = false;
    public $syncTarget = 'all'; // all, afiliados, empresas
    public $estimatedCloudCount = 0;
    public $lastSyncDate = null;
    public $savingsCount = 0; // Registros omitidos por hash

    public function mount()
    {
        $this->updateStatus();
    }

    public function stopSync()
    {
        \Illuminate\Support\Facades\Cache::put('firebase_sync_stop', true, 60);
        \Illuminate\Support\Facades\Cache::lock('firebase_sync_lock')->forceRelease();
        
        if ($this->recentLog && ($this->recentLog->status === 'started' || $this->recentLog->status === 'in_progress')) {
            $this->recentLog->update([
                'status' => 'failed',
                'message' => 'Sincronización detenida por el usuario.',
                'completed_at' => now()
            ]);
        }
        
        session()->flash('warning', 'Se ha enviado la señal de parada al motor y se ha liberado el subproceso.');
        $this->updateStatus();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        
        // Solo actualizamos contadores pesados si volvemos al dashboard
        if ($tab === 'dashboard') {
            try {
                $this->updateStatus();
            } catch (\Throwable $e) {
                \Log::error("SyncControlCenter setTab Error: " . $e->getMessage());
            }
        }
    }

    // #[\Livewire\Attributes\On('echo:sync-channel,SyncProgressEvent')]
    public function updateStatus()
    {
        try {
            $this->totalEmpresas = \App\Models\Empresa::count();
            $this->totalAfiliados = \App\Models\Afiliado::withoutGlobalScopes()->count();
            $this->pendingPush = \App\Models\Afiliado::withoutGlobalScopes()->whereIn('firebase_sync_status', ['pending', 'modified'])->count();
            $this->totalConflicts = \App\Models\Afiliado::withoutGlobalScopes()->where('conflict_status', true)->count() + 
                                     \App\Models\Empresa::withoutGlobalScopes()->where('conflict_status', true)->count();
            
            $this->pendingJobs = \Illuminate\Support\Facades\DB::table('jobs')->count();
            $this->savingsCount = \Illuminate\Support\Facades\Cache::get('firebase_sync_savings', 0);
            
            $this->recentLog = FirebaseSyncLog::where('type', '!=', 'traspasos')
                ->where('message', 'not like', '%traspasos%')
                ->latest()
                ->first();
            
            if ($this->recentLog) {
                $this->totalRecords = $this->recentLog->total_records ?: 1;
                $this->recordsSynced = $this->recentLog->records_synced;
                $this->progressPercentage = min(round(($this->recordsSynced / $this->totalRecords) * 100), 100);
                

                // Estimar el conteo en la nube basado en el último éxito total
                $lastSuccess = FirebaseSyncLog::where('status', 'completed')
                    ->where('type', 'full')
                    ->where('message', 'not like', '%traspasos%')
                    ->latest()
                    ->first();
                
                $this->estimatedCloudCount = $lastSuccess ? $lastSuccess->total_records : ($this->totalAfiliados + $this->totalEmpresas);
                $this->lastSyncDate = $lastSuccess ? $lastSuccess->completed_at : null;
                
                if ($this->recentLog->status === 'started' || $this->recentLog->status === 'in_progress') {
                    $this->polling = true;
                } else {
                    $this->polling = false;
                    $this->lastSyncSummary = [
                        'type' => $this->recentLog->type,
                        'status' => $this->recentLog->status,
                        'synced' => $this->recentLog->records_synced,
                        'failed' => $this->recentLog->records_failed,
                        'message' => $this->recentLog->message,
                        'time' => $this->recentLog->completed_at ? $this->recentLog->completed_at->format('H:i:s') : 'N/A'
                    ];
                }
            }

            // Check cloud status
            $this->isCircuitOpen = \Illuminate\Support\Facades\Cache::get('firebase_circuit_open', false);
            $this->isLockActive = \Illuminate\Support\Facades\DB::table('cache_locks')->where('key', 'firebase_sync_lock')->exists();

            // SafeSync Metrics
            $today = now()->format('Y-m-d');
            $this->dailyReads = \Illuminate\Support\Facades\Cache::get('firebase_daily_reads_count_' . $today, 0);
            
            // Computar métricas acumuladas y del log reciente
            $this->accumulatedReads = (int)\App\Models\FirebaseSyncLog::sum('firebase_reads');
            $this->accumulatedWrites = (int)\App\Models\FirebaseSyncLog::sum('firebase_writes');
            $this->accumulatedCost = (float)\App\Models\FirebaseSyncLog::sum('estimated_cost');
            
            if ($this->recentLog) {
                $this->estimatedCost = (float)$this->recentLog->estimated_cost;
            }

            $this->checkpoints = \App\Models\CloudSyncCheckpoint::where('process_name', '!=', 'traspasos')->get();
            $this->liveFeed = \Illuminate\Support\Facades\Cache::get('firebase_live_feed', []);
            
            $this->logs = FirebaseSyncLog::where('type', '!=', 'traspasos')
                ->where('message', 'not like', '%traspasos%')
                ->with('user')
                ->orderBy('id', 'desc')
                ->limit(6)
                ->get();
            
            $this->auditLogs = \App\Models\AuditLog::with('user')
                ->orderBy('id', 'desc')
                ->limit(15)
                ->get();
            
            // pendingJobs already calculated above — no duplicate needed
            
            // Cálculo de progreso para la barra visual
            if ($this->recentLog) {
                $this->totalRecords = $this->recentLog->total_records ?: 0;
                $this->recordsSynced = $this->recentLog->records_synced ?: 0;
                $this->progressPercentage = $this->totalRecords > 0 
                    ? min(100, round(($this->recordsSynced / $this->totalRecords) * 100)) 
                    : 0;
            }
            
            $this->systemStatus['firebase'] = $this->isCircuitOpen ? 'limited' : 'online';
            $this->systemStatus['workers'] = $this->pendingJobs > 50 ? 'busy' : 'active';

            if ($this->recentLog && ($this->recentLog->status === 'started' || $this->recentLog->status === 'in_progress')) {
                $this->polling = true;
                $lastActivity = $this->recentLog->last_heartbeat_at ?: $this->recentLog->started_at;
                if ($lastActivity && $lastActivity->diffInMinutes(now()) >= 5) {
                    $this->isStalled = true;
                }
            } else {
                $this->polling = false;
            }
        } catch (\Throwable $e) {
            \Log::error("SyncControlCenter UpdateStatus Failure: " . $e->getMessage());
        }
    }

    public function forceReleaseLock()
    {
        // Forzar la liberación del bloqueo en el driver de caché (incluyendo base de datos)
        \Illuminate\Support\Facades\Cache::lock('firebase_sync_lock')->forceRelease();
        \Illuminate\Support\Facades\Cache::forget('firebase_sync_stop');
        
        if ($this->recentLog) {
            $this->recentLog->update([
                'status' => 'failed',
                'message' => 'Sistema liberado manualmente por el administrador (Lock Reset).',
                'completed_at' => now()
            ]);
        }
        
        $this->updateStatus();
        session()->flash('info', 'Bloqueo del sistema liberado manualmente.');
    }

    public function syncIncremental()
    {
        if ($this->isCircuitOpen) return;

        $total = 0;
        if ($this->syncTarget === 'all' || $this->syncTarget === 'afiliados') $total += \App\Models\Afiliado::count();
        if ($this->syncTarget === 'all' || $this->syncTarget === 'empresas') $total += \App\Models\Empresa::count();

        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => 'incremental',
            'status' => 'started',
            'started_at' => now(),
            'total_records' => $total, 
            'message' => 'Iniciando sincronización incremental vía Nexus Dashboard.'
        ]);

        $params = [
            '--hours' => 24, 
            '--log-id' => $log->id
        ];

        if ($this->syncTarget !== 'all') {
            $params['--collection'] = $this->syncTarget;
        }

        \Illuminate\Support\Facades\Artisan::queue('firebase:pull-all', $params);

        $this->updateStatus();
        session()->flash('success', 'Sincronización incremental iniciada en segundo plano.');
    }

    public function syncFull()
    {
        if ($this->isCircuitOpen) return;

        $total = \App\Models\Afiliado::withoutGlobalScopes()->count() + \App\Models\Empresa::withoutGlobalScopes()->count();
        if ($total === 0) $total = 15000;

        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => 'full',
            'status' => 'started',
            'started_at' => now(),
            'total_records' => $total, 
            'message' => 'Reconstrucción total iniciada desde consola Nexus.'
        ]);

        $params = [
            '--full' => true, 
            '--log-id' => $log->id
        ];

        if ($this->syncTarget !== 'all') {
            $params['--collection'] = $this->syncTarget;
        }

        \Illuminate\Support\Facades\Artisan::queue('firebase:pull-all', $params);

        $this->updateStatus();
        session()->flash('success', 'Sincronización TOTAL iniciada.');
    }

    public function syncMyAffiliates()
    {
        if ($this->isCircuitOpen) return;

        $total = \App\Models\Afiliado::count(); // Conteo con scope (solo los míos)
        if ($total === 0) $total = 500; // Estimado inicial pequeño

        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => 'full',
            'status' => 'started',
            'started_at' => now(),
            'total_records' => $total, 
            'message' => 'Sincronización de MIS afiliados (Filtro Responsable) iniciada.'
        ]);

        $params = [
            '--full' => true, 
            '--log-id' => $log->id,
            '--collection' => 'afiliados',
            '--responsable-id' => auth()->user()->responsable_id ?? auth()->id()
        ];

        \Illuminate\Support\Facades\Artisan::queue('firebase:pull-all', $params);

        $this->updateStatus();
        session()->flash('success', 'Descarga de TUS afiliados iniciada en segundo plano.');
    }

    public function syncPush()
    {
        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => 'push',
            'status' => 'started',
            'started_at' => now(),
            'message' => 'Push de cambios locales iniciado vía Nexus.'
        ]);

        \Illuminate\Support\Facades\Artisan::queue('firebase:push', [
            '--all' => true, 
            '--log-id' => $log->id
        ]);

        $this->updateStatus();
        session()->flash('success', 'Sincronización de salida (Push) iniciada.');
    }

    public $firebaseRecordDetail = null;

    public function getDiffFields()
    {
        if (!$this->selectedRecordDetail || !$this->firebaseRecordDetail) {
            return [];
        }

        $diffs = [];
        $local = $this->selectedRecordDetail->toArray();
        $remote = $this->firebaseRecordDetail;

        // Campos clave a comparar
        $fieldsToCompare = [
            'nombre_completo', 'cedula', 'estado_id', 'empresa_id', 
            'puesto', 'telefono', 'email'
        ];

        foreach ($fieldsToCompare as $field) {
            $localVal = (string)($local[$field] ?? '');
            $remoteVal = (string)($remote[$field] ?? '');

            if ($localVal !== $remoteVal) {
                $diffs[] = $field;
            }
        }

        return $diffs;
    }

    public function openRecordDetail($id)
    {
        $this->selectedRecordId = $id;
        $this->selectedRecordDetail = \App\Models\Afiliado::with(['estado', 'empresaModel', 'responsable', 'lastUpdatedBy', 'evidenciasAfiliado'])->find($id);
        
        // Cargar versión en la nube para el Diff Viewer
        if ($this->selectedRecordDetail && $this->selectedRecordDetail->cedula) {
            $firebase = app(\App\Services\FirebaseSyncService::class);
            $this->firebaseRecordDetail = $firebase->getDocument('afiliados', $this->selectedRecordDetail->cedula);
        } else {
            $this->firebaseRecordDetail = null;
        }

        $this->showModal = true;
    }

    public function closeRecordDetail()
    {
        $this->showModal = false;
        $this->selectedRecordId = null;
        $this->selectedRecordDetail = null;
        $this->firebaseRecordDetail = null;
    }

    public function forceSyncLocalToCloud()
    {
        // Fuerza push de ESTE registro a Firebase
        if ($this->selectedRecordDetail) {
            $firebase = app(\App\Services\FirebaseSyncService::class);
            
            // Usamos cedula como ID de documento para coincidir con el Pull engine
            $success = $firebase->push('afiliados', $this->selectedRecordDetail->cedula, $this->selectedRecordDetail->toArray(), $this->selectedRecordDetail);
            
            if ($success) {
                $this->selectedRecordDetail->update(['conflict_status' => false]);
                session()->flash('success', 'Registro local forzado hacia la nube exitosamente.');
            } else {
                session()->flash('error', 'Error al sincronizar con la nube. Revise los logs.');
            }
            
            $this->closeRecordDetail();
        }
    }

    public function forceSyncCloudToLocal()
    {
        if ($this->selectedRecordDetail && $this->firebaseRecordDetail) {
            $fillable = $this->selectedRecordDetail->getFillable();
            $filtered = array_intersect_key($this->firebaseRecordDetail, array_flip($fillable));
            
            // Merge cloud data with sync metadata in a SINGLE quiet update
            // to avoid triggering observer twice (which would re-mark as 'modified')
            $this->selectedRecordDetail->updateQuietly(array_merge($filtered, [
                'updated_from'         => 'firebase',
                'conflict_status'      => false,
                'firebase_sync_status' => 'synced',
                'firebase_synced_at'   => now()
            ]));
            
            session()->flash('success', 'Registro de la nube sobrescribió el local exitosamente.');
            $this->closeRecordDetail();
        }
    }

    public function resetCircuitBreaker()
    {
        \Illuminate\Support\Facades\Cache::forget('firebase_circuit_open');
        session()->flash('success', 'El corta-circuitos se ha restablecido. Conectividad reanudada con Firebase.');
        $this->updateStatus();
    }

    public function render()
    {
        // NOTE: Log::info removed — with 2s polling active this generated 30 log entries/min
        $records = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        $webhooks = collect();
        $conflicts = collect();

        try {
            if ($this->activeTab === 'records') {
                $records = \App\Models\Afiliado::query()
                    ->with(['empresaModel', 'estado', 'responsable', 'evidenciasAfiliado'])
                    ->when($this->search, function($q) {
                        $q->where('nombre_completo', 'like', '%'.$this->search.'%')
                          ->orWhere('cedula', 'like', '%'.$this->search.'%');
                    })
                    ->latest()
                    ->paginate(15);
            }

            if ($this->activeTab === 'dashboard') {
                if (\Illuminate\Support\Facades\Schema::hasTable('webhook_logs')) {
                    $webhooks = \App\Models\WebhookLog::latest()->limit(10)->get();
                }
            }

            if ($this->activeTab === 'conflicts') {
                $conflicts = \App\Models\Afiliado::where('conflict_status', true)
                    ->with(['empresaModel', 'estado', 'evidenciasAfiliado'])
                    ->latest()
                    ->get();
            }

            if ($this->activeTab === 'timeline') {
                $this->auditLogs = \App\Models\AuditLog::with('user')
                    ->orderBy('id', 'desc')
                    ->limit(15)
                    ->get();
            }
        } catch (\Throwable $e) {
            \Log::error("SyncControlCenter Render Failure: " . $e->getMessage());
        }

        return view('livewire.sync-control-center', [
            'records' => $records,
            'webhooks' => $webhooks,
            'conflicts' => $conflicts
        ]);
    }
}
