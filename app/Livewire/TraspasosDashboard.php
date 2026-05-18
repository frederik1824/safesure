<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Traspaso;
use App\Models\CloudSyncCheckpoint;
use App\Models\FirebaseSyncLog;
use App\Services\FirebaseSyncService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class TraspasosDashboard extends Component
{
    use WithPagination;

    // Tipo de Vista: 'list' (Administración de Datos) o 'sync' (Panel de Telemetría/Triggers)
    public $view = 'list';

    // Filtros y Búsqueda
    public $search = '';
    public $filterEstado = 'all';
    public $filterAgente = 'all';
    public $filterPeriodo = 'all';
    public $filterStatusUnipago = 'all';
    public $fechaSolicitud = '';
    public $fechaEfectivo = '';
    
    // Ordenamiento
    public $orderBy = 'firebase_updated_at';
    public $orderDir = 'desc';

    // Detalle
    public $selectedTraspasoId = null;
    public $showDetailModal = false;

    // Telemetría & Sync
    public $polling = false;
    public $recentLog = null;
    public $progressPercentage = 0;
    public $recordsSynced = 0;
    public $isStalled = false;
    public $lastSyncDate = null;
    public $firebaseStatus = 'online';

    // Paginación personalizada
    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => 'all'],
        'filterAgente' => ['except' => 'all'],
        'filterPeriodo' => ['except' => 'all'],
        'filterStatusUnipago' => ['except' => 'all'],
    ];

    public function mount($view = 'list')
    {
        $this->view = $view;
        $this->checkSyncStatus();
    }

    public function checkSyncStatus()
    {
        // Obtener último log de traspasos
        $this->recentLog = FirebaseSyncLog::where(function($query) {
            $query->where('message', 'like', '%traspasos%')
                  ->orWhere('type', 'traspasos');
        })->latest()->first();

        if ($this->recentLog) {
            if (in_array($this->recentLog->status, ['started', 'in_progress'])) {
                $this->polling = true;
                
                // Calcular progreso
                $total = $this->recentLog->total_records ?: 100;
                $synced = $this->recentLog->records_synced ?: 0;
                $this->progressPercentage = min(100, round(($synced / $total) * 100));
                $this->recordsSynced = $synced;

                // Verificar si se ha estancado
                $lastActivity = $this->recentLog->last_heartbeat_at ?: $this->recentLog->started_at;
                if ($lastActivity && Carbon::parse($lastActivity)->diffInMinutes(now()) >= 5) {
                    $this->isStalled = true;
                } else {
                    $this->isStalled = false;
                }
            } else {
                $this->polling = false;
                $this->progressPercentage = 0;
            }
        }

        // Obtener última fecha de sincronización del checkpoint
        $checkpoint = CloudSyncCheckpoint::where('process_name', 'traspasos')->first();
        $this->lastSyncDate = $checkpoint ? ($checkpoint->last_successful_sync_at ? Carbon::parse($checkpoint->last_successful_sync_at)->format('d/m/Y h:i A') : 'Nunca') : 'Nunca';

        // Ping rápido de Firebase en caché por 30 segundos
        $this->firebaseStatus = Cache::remember('firebase_ping_status', 30, function() {
            try {
                $srv = app(FirebaseSyncService::class);
                return $srv->ping() ? 'online' : 'offline';
            } catch (\Throwable $e) {
                return 'offline';
            }
        });
    }

    public function triggerSync(bool $full = false)
    {
        if ($this->polling) return;

        Cache::forget('firebase_sync_stop');

        // Determinar total de registros remotos estimados
        $totalRemote = 0;
        try {
            $srv = app(FirebaseSyncService::class);
            $totalRemote = $srv->getCollectionCount('traspasos') ?: 500;
        } catch (\Throwable $e) {
            $totalRemote = 500;
        }

        // Crear Log
        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => 'traspasos',
            'status' => 'started',
            'started_at' => now(),
            'total_records' => $totalRemote,
            'message' => 'Sincronización manual de traspasos iniciada desde el Panel.'
        ]);

        $params = [
            '--log-id' => $log->id
        ];

        if ($full) {
            $params['--full'] = true;
        }

        Artisan::queue('firebase:sync-traspasos', $params);

        $this->polling = true;
        $this->recentLog = $log;
        $this->progressPercentage = 0;
        $this->recordsSynced = 0;

        session()->flash('success', 'La sincronización de traspasos ha sido iniciada en segundo plano.');
        $this->checkSyncStatus();
    }

    public function stopSync()
    {
        Cache::put('firebase_sync_stop', true, 60);
        Cache::lock('firebase_sync_traspasos_lock')->forceRelease();
        
        if ($this->recentLog && in_array($this->recentLog->status, ['started', 'in_progress'])) {
            $this->recentLog->update([
                'status' => 'failed',
                'message' => 'Sincronización detenida por el usuario.',
                'completed_at' => now()
            ]);
        }
        
        session()->flash('warning', 'Se ha enviado la señal de parada al motor de traspasos.');
        $this->checkSyncStatus();
    }

    public function showDetail($id)
    {
        $this->selectedTraspasoId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedTraspasoId = null;
    }

    public function setOrder($field)
    {
        if ($this->orderBy === $field) {
            $this->orderDir = $this->orderDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderBy = $field;
            $this->orderDir = 'desc';
        }
    }

    public function render()
    {
        $this->checkSyncStatus();

        // Contadores KPI
        $kpiTotal = Traspaso::count();
        $kpiEfectivos = Traspaso::where('estado', 'EFECTIVO')->count();
        $kpiRechazados = Traspaso::where('status_unipago', 'RECHAZADO')->count();
        $kpiPendientes = Traspaso::where('status_unipago', 'PENDIENTE')->count();

        // Totales de dependientes segregados
        $kpiTotalDeps = Traspaso::sum('cantidad_dependientes');
        $kpiEfectivosDeps = Traspaso::where('estado', 'EFECTIVO')->sum('cantidad_dependientes');
        $kpiRechazadosDeps = Traspaso::where('status_unipago', 'RECHAZADO')->sum('cantidad_dependientes');
        $kpiPendientesDeps = Traspaso::where('status_unipago', 'PENDIENTE')->sum('cantidad_dependientes');

        // Analítica: Top 5 Agentes Promotores
        $topAgentes = Cache::remember('traspasos_top_agentes', 60, function() {
            return Traspaso::selectRaw('agente, count(*) as count, sum(cantidad_dependientes) as sum_deps')
                ->groupBy('agente')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();
        });

        // Analítica: Tendencia mensual (Últimos 6 meses)
        $monthsTrend = Cache::remember('traspasos_monthly_trend', 60, function() {
            $isPgsql = config('database.default') === 'pgsql';
            $periodSelect = $isPgsql 
                ? "to_char(fecha_solicitud, 'YYYY-MM') as period"
                : "DATE_FORMAT(fecha_solicitud, '%Y-%m') as period";

            return Traspaso::selectRaw("
                {$periodSelect},
                COUNT(*) as total_transfers,
                SUM(CASE WHEN estado = 'EFECTIVO' THEN 1 ELSE 0 END) as effective_transfers,
                SUM(cantidad_dependientes) as total_dependents
            ")
            ->whereNotNull('fecha_solicitud')
            ->where('fecha_solicitud', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        });

        $chartLabels = [];
        $chartTotalTransfers = [];
        $chartEffectiveTransfers = [];
        $chartTotalDependents = [];

        $spanishMonths = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
        ];

        foreach ($monthsTrend as $trend) {
            if (!$trend->period) continue;
            list($year, $month) = explode('-', $trend->period);
            $monthName = $spanishMonths[$month] ?? $month;
            $chartLabels[] = "$monthName " . substr($year, 2);
            $chartTotalTransfers[] = (int)$trend->total_transfers;
            $chartEffectiveTransfers[] = (int)$trend->effective_transfers;
            $chartTotalDependents[] = (int)$trend->total_dependents;
        }

        // 1. Lógica para la Vista de Sincronización
        if ($this->view === 'sync') {
            $syncLogs = FirebaseSyncLog::where(function($query) {
                $query->where('message', 'like', '%traspasos%')
                      ->orWhere('type', 'traspasos');
            })->latest()->limit(8)->get();

            $checkpoint = CloudSyncCheckpoint::where('process_name', 'traspasos')->first();

            return view('livewire.traspasos-dashboard', [
                'syncLogs' => $syncLogs,
                'checkpoint' => $checkpoint,
                'kpiTotal' => $kpiTotal,
                'kpiEfectivos' => $kpiEfectivos,
                'kpiRechazados' => $kpiRechazados,
                'kpiPendientes' => $kpiPendientes,
                'kpiTotalDeps' => $kpiTotalDeps,
                'kpiEfectivosDeps' => $kpiEfectivosDeps,
                'kpiRechazadosDeps' => $kpiRechazadosDeps,
                'kpiPendientesDeps' => $kpiPendientesDeps,
                'topAgentes' => $topAgentes,
                'chartLabels' => $chartLabels,
                'chartTotalTransfers' => $chartTotalTransfers,
                'chartEffectiveTransfers' => $chartEffectiveTransfers,
                'chartTotalDependents' => $chartTotalDependents,
            ]);
        }

        // 2. Lógica para la Vista del Listado Administrativo
        // Cargar listas únicas para filtros
        $agentes = Cache::remember('traspasos_unique_agentes', 60, function() {
            return Traspaso::distinct()->pluck('agente')->filter()->toArray();
        });

        $periodos = Cache::remember('traspasos_unique_periodos', 60, function() {
            return Traspaso::distinct()->pluck('periodo')->filter()->toArray();
        });

        $estados = Cache::remember('traspasos_unique_estados', 60, function() {
            return Traspaso::distinct()->pluck('estado')->filter()->toArray();
        });

        $statusUnipagos = Cache::remember('traspasos_unique_unipagos', 60, function() {
            return Traspaso::distinct()->pluck('status_unipago')->filter()->toArray();
        });

        // Consulta filtrada
        $query = Traspaso::query();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('nombre_afiliado', 'like', '%' . $this->search . '%')
                  ->orWhere('cedula_afiliado', 'like', '%' . $this->search . '%')
                  ->orWhere('agente', 'like', '%' . $this->search . '%')
                  ->orWhere('firebase_document_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterEstado !== 'all') {
            $query->where('estado', $this->filterEstado);
        }

        if ($this->filterAgente !== 'all') {
            $query->where('agente', $this->filterAgente);
        }

        if ($this->filterPeriodo !== 'all') {
            $query->where('periodo', $this->filterPeriodo);
        }

        if ($this->filterStatusUnipago !== 'all') {
            $query->where('status_unipago', $this->filterStatusUnipago);
        }

        if (!empty($this->fechaSolicitud)) {
            $query->whereDate('fecha_solicitud', $this->fechaSolicitud);
        }

        if (!empty($this->fechaEfectivo)) {
            $query->whereDate('fecha_efectivo', $this->fechaEfectivo);
        }

        $traspasos = $query->orderBy($this->orderBy, $this->orderDir)->paginate(15);

        // Detalle seleccionado
        $selectedTraspaso = $this->selectedTraspasoId ? Traspaso::find($this->selectedTraspasoId) : null;

        return view('livewire.traspasos-dashboard', [
            'traspasos' => $traspasos,
            'agentes' => $agentes,
            'periodos' => $periodos,
            'estados' => $estados,
            'statusUnipagos' => $statusUnipagos,
            'kpiTotal' => $kpiTotal,
            'kpiEfectivos' => $kpiEfectivos,
            'kpiRechazados' => $kpiRechazados,
            'kpiPendientes' => $kpiPendientes,
            'kpiTotalDeps' => $kpiTotalDeps,
            'kpiEfectivosDeps' => $kpiEfectivosDeps,
            'kpiRechazadosDeps' => $kpiRechazadosDeps,
            'kpiPendientesDeps' => $kpiPendientesDeps,
            'topAgentes' => $topAgentes,
            'chartLabels' => $chartLabels,
            'chartTotalTransfers' => $chartTotalTransfers,
            'chartEffectiveTransfers' => $chartEffectiveTransfers,
            'chartTotalDependents' => $chartTotalDependents,
            'selectedTraspaso' => $selectedTraspaso,
        ]);
    }
}
