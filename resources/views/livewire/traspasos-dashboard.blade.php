<div class="space-y-6" @if($polling) wire:poll.3s="checkSyncStatus" @endif>

    @if($view === 'sync')
        <!-- ========================================== -->
        <!-- VIEW: TELEMETRY & SYNC ENGINE              -->
        <!-- ========================================== -->
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md bg-cyan-500/10 text-cyan-600 text-[10px] font-mono font-bold tracking-wider uppercase">NEXUS TELEMETRY ENGINE</span>
                    <div class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            @if($firebaseStatus === 'online')
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            @else
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500 animate-pulse"></span>
                            @endif
                        </span>
                        <span class="text-[10px] font-mono text-slate-500 font-bold uppercase">FIREBASE {{ $firebaseStatus === 'online' ? 'CONECTADO' : 'DESCONECTADO' }}</span>
                    </div>
                </div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight mt-2">Sincronización de Traspasos Firebase</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Control de triggers, auditoría de logs y descarga incremental desde la colección remota <code class="bg-slate-100 px-1.5 py-0.5 rounded font-mono text-cyan-700">traspasos</code>.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                <button wire:click="triggerSync(false)" @if($polling) disabled @endif class="px-4 py-2.5 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 rounded-xl text-xs font-semibold tracking-wide transition-all shadow-sm flex items-center gap-2">
                    <i class="ph-bold ph-arrows-clockwise text-sm {{ $polling ? 'animate-spin' : '' }}"></i>
                    <span>SINCRONIZAR AHORA</span>
                </button>
                
                <button wire:click="triggerSync(true)" @if($polling) disabled @endif class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 disabled:opacity-50 rounded-xl text-xs font-semibold tracking-wide transition-all flex items-center gap-2">
                    <i class="ph-bold ph-arrow-counter-clockwise text-sm"></i>
                    <span>RECONSTRUIR TODO</span>
                </button>
            </div>
        </div>

        <!-- Active Sync Progress -->
        @if($polling && $recentLog)
            <div class="p-6 rounded-3xl border border-cyan-500/20 bg-cyan-500/[0.02] shadow-xs relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 bg-cyan-500/10 rounded-2xl flex items-center justify-center text-cyan-600 border border-cyan-500/20">
                            <i class="ph-bold ph-arrows-clockwise text-xl animate-spin"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-sans font-bold text-slate-800 uppercase tracking-wider">Procesador Activo</h4>
                            <p class="text-[10px] text-slate-500 mt-0.5">Ingestando lote de traspasos. Por favor, mantén esta pestaña abierta o monitorea en segundo plano.</p>
                        </div>
                    </div>
                    <button wire:click="stopSync" class="px-3.5 py-1.5 bg-rose-500/10 border border-rose-500/20 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[10px] font-bold tracking-wider transition-all flex items-center gap-1.5">
                        <i class="ph-bold ph-stop-circle text-xs"></i>
                        <span>DETENER</span>
                    </button>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-[10px] font-mono font-bold text-slate-500">
                        <span>PROGRESO DE SINCRONIZACIÓN</span>
                        <span class="text-cyan-600">{{ $progressPercentage }}% ({{ number_format($recordsSynced) }} / {{ number_format($recentLog->total_records) }} docs)</span>
                    </div>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden border border-slate-300">
                        <div class="h-full bg-gradient-to-r from-cyan-400 to-cyan-500 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%;"></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Diagnostic / Checkpoint Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Nexus Checkpoint Details -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="ph-bold ph-shield text-base text-blue-500"></i>
                    Filtro de Ingesta & Checkpoint
                </h3>
                <div class="space-y-2.5 font-mono text-[10px] text-slate-600">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Proceso Asignado:</span>
                        <strong class="text-slate-800 font-bold uppercase">traspasos</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Último Timestamp Remoto:</span>
                        <strong class="text-slate-800">{{ $checkpoint && $checkpoint->last_firebase_updated_at ? $checkpoint->last_firebase_updated_at->format('d/m/Y H:i:s') : 'Ninguno' }}</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Última Ejecución Exitosa:</span>
                        <strong class="text-slate-800">{{ $checkpoint && $checkpoint->last_successful_sync_at ? $checkpoint->last_successful_sync_at->format('d/m/Y H:i:s') : 'Nunca' }}</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Estado Checkpoint:</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase {{ $checkpoint && $checkpoint->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $checkpoint ? $checkpoint->status : 'idle' }}</span>
                    </div>
                </div>
            </div>

            <!-- Costs & Savings Simulations -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="ph-bold ph-coins text-base text-amber-500"></i>
                    Simulador de Consumo Cloud
                </h3>
                <div class="space-y-2.5 font-mono text-[10px] text-slate-600">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Estimación Lecturas:</span>
                        <strong class="text-slate-800">{{ number_format($kpiTotal) }} reads</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Consumo Spark (Free):</span>
                        <strong class="text-emerald-600 font-bold">$0.00 USD (Límite 50k/día)</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Costo Blaze (Pay-as-go):</span>
                        <strong class="text-slate-700 font-bold">~ ${{ number_format(($kpiTotal / 100000) * 0.06, 4) }} USD</strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span>Optimización Hash SafeSync:</span>
                        <strong class="text-cyan-600">100% activa (Gating Duplicados)</strong>
                    </div>
                </div>
            </div>

            <!-- Summary KPI metrics -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="ph-bold ph-database text-base text-purple-500"></i>
                    Registros Ingestados Locales
                </h3>
                <div class="grid grid-cols-2 gap-2 text-center pt-1">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[8px] font-mono text-slate-400 block uppercase">Total</span>
                        <strong class="text-lg font-mono text-slate-800 font-black block mt-0.5">{{ number_format($kpiTotal) }}</strong>
                        <span class="text-[8px] font-mono text-indigo-500 font-bold block mt-0.5">{{ number_format($kpiTotalDeps) }} Deps</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[8px] font-mono text-slate-400 block uppercase">Efectivos</span>
                        <strong class="text-lg font-mono text-emerald-600 font-black block mt-0.5">{{ number_format($kpiEfectivos) }}</strong>
                        <span class="text-[8px] font-mono text-emerald-500 font-bold block mt-0.5">{{ number_format($kpiEfectivosDeps) }} Deps</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[8px] font-mono text-slate-400 block uppercase">Rechazados</span>
                        <strong class="text-lg font-mono text-rose-600 font-black block mt-0.5">{{ number_format($kpiRechazados) }}</strong>
                        <span class="text-[8px] font-mono text-rose-500 font-bold block mt-0.5">{{ number_format($kpiRechazadosDeps) }} Deps</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[8px] font-mono text-slate-400 block uppercase">Pendientes</span>
                        <strong class="text-lg font-mono text-amber-600 font-black block mt-0.5">{{ number_format($kpiPendientes) }}</strong>
                        <span class="text-[8px] font-mono text-amber-500 font-bold block mt-0.5">{{ number_format($kpiPendientesDeps) }} Deps</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sync Logs Audits -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="ph-bold ph-activity text-base text-slate-500"></i>
                    Historial Reciente de Sincronización (Traspasos)
                </h3>
                <span class="px-2 py-0.5 rounded bg-slate-200 text-[9px] font-mono font-bold text-slate-600">AUDITORÍA</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto text-left border-collapse font-sans text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-3">ID Log</th>
                            <th class="px-6 py-3">Tipo Sync</th>
                            <th class="px-6 py-3">Estatus</th>
                            <th class="px-6 py-3 text-center">Agregados</th>
                            <th class="px-6 py-3 text-center">Modificados</th>
                            <th class="px-6 py-3 text-center">Omitidos</th>
                            <th class="px-6 py-3">Fecha y Hora</th>
                            <th class="px-6 py-3">Detalle / Mensaje</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px] text-slate-700">
                        @forelse($syncLogs as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 font-bold">#{{ $log->id }}</td>
                                <td class="px-6 py-3.5 capitalize font-medium text-slate-800">{{ $log->type }}</td>
                                <td class="px-6 py-3.5">
                                    @if($log->status === 'completed')
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-emerald-100 text-emerald-700 uppercase">EXITOSO</span>
                                    @elseif($log->status === 'failed')
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-rose-100 text-rose-700 uppercase">FALLIDO</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-blue-100 text-blue-700 uppercase animate-pulse">PROCESANDO</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-center font-bold text-emerald-600">{{ number_format($log->records_added) }}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-blue-600">{{ number_format($log->records_updated) }}</td>
                                <td class="px-6 py-3.5 text-center font-bold text-amber-600">{{ number_format($log->records_skipped) }}</td>
                                <td class="px-6 py-3.5 text-slate-500 font-medium">{{ $log->created_at->format('d/m/Y h:i:s A') }}</td>
                                <td class="px-6 py-3.5 font-sans text-xs text-slate-600 max-w-xs truncate" title="{{ $log->message }}">{{ $log->message }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                    No hay registros de auditoría de sincronización disponibles.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- VIEW: DEDICATED BUSINESS LIST VIEW         -->
        <!-- ========================================== -->
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-md bg-indigo-500/10 text-indigo-600 text-[10px] font-mono font-bold tracking-wider uppercase">WORKSPACE DE TRASPASOS</span>
                    <span class="px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-600 text-[10px] font-mono font-bold tracking-wider uppercase flex items-center gap-1">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        ACTIVO
                    </span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Expedientes de Traspasos Recibidos</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Búsqueda rápida, filtros avanzados y detalle operativo de expedientes transferidos remotamente desde el sistema maestro CMD.
                </p>
            </div>

            @can('access_admin_panel')
            <a href="{{ route('admin.sync.index', ['activeTab' => 'traspasos']) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold tracking-wide transition-all flex items-center gap-2">
                <i class="ph-bold ph-arrows-clockwise text-sm"></i>
                <span>PANEL DE SINCRONIZACIÓN</span>
            </a>
            @endcan
        </div>

        <!-- Success Toast Info inside View -->
        @if(session()->has('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 rounded-2xl text-xs font-mono font-bold flex items-center gap-3">
                <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Executive KPI Metric Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Card: Total -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-black text-slate-400 uppercase tracking-widest">Total Traspasos</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                        <i class="ph-bold ph-swap text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-slate-800 font-mono tracking-tight">{{ number_format($kpiTotal) }}</h3>
                    <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-500 font-semibold font-mono">
                        <i class="ph-bold ph-users text-[10px] text-indigo-500"></i>
                        <span>{{ number_format($kpiTotalDeps) }} Deps Totales</span>
                    </div>
                </div>
            </div>

            <!-- Card: Efectivos -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-black text-slate-400 uppercase tracking-widest">Efectivos</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <i class="ph-bold ph-shield-check text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-emerald-600 font-mono tracking-tight">{{ number_format($kpiEfectivos) }}</h3>
                    <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-500 font-semibold font-mono">
                        <i class="ph-bold ph-users text-[10px] text-emerald-500"></i>
                        <span>{{ number_format($kpiEfectivosDeps) }} Deps Efectivos</span>
                    </div>
                </div>
            </div>

            <!-- Card: Rechazados -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-black text-slate-400 uppercase tracking-widest">Rechazados</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center">
                        <i class="ph-bold ph-x-circle text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-rose-600 font-mono tracking-tight">{{ number_format($kpiRechazados) }}</h3>
                    <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-500 font-semibold font-mono">
                        <i class="ph-bold ph-users text-[10px] text-rose-500"></i>
                        <span>{{ number_format($kpiRechazadosDeps) }} Deps Rechazados</span>
                    </div>
                </div>
            </div>

            <!-- Card: Pendientes -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-black text-slate-400 uppercase tracking-widest">Pendientes</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <i class="ph-bold ph-clock text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-2xl font-black text-amber-600 font-mono tracking-tight">{{ number_format($kpiPendientes) }}</h3>
                    <div class="flex items-center gap-1 mt-1 text-[9px] text-slate-500 font-semibold font-mono">
                        <i class="ph-bold ph-users text-[10px] text-amber-500"></i>
                        <span>{{ number_format($kpiPendientesDeps) }} Deps en Proceso</span>
                    </div>
                </div>
            </div>

            <!-- Card: Telemetría / Última Sync -->
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between col-span-2 md:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="text-[9px] font-mono font-black text-slate-400 uppercase tracking-widest">Última Sync</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-500/10 text-slate-600 flex items-center justify-center">
                        <i class="ph-bold ph-calendar text-lg"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-bold text-slate-700 tracking-tight leading-snug">{{ $lastSyncDate }}</h3>
                    <p class="text-[9px] text-slate-500 mt-1 font-medium">Actualización automática activa</p>
                </div>
            </div>
        </div>

        <!-- Analytics & Performance Insights Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Trend Chart: Traspasos vs Dependientes (col-span-2) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="ph-bold ph-chart-line-up text-base text-indigo-500"></i>
                            Tendencias de Traspasos y Dependientes
                        </h3>
                        <p class="text-[10px] text-slate-500 mt-0.5">Volumen mensual registrado en los últimos 6 meses</p>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-mono font-bold">
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Traspasos</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Efectivos</span>
                        <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span> Dependientes</span>
                    </div>
                </div>
                <div class="relative h-64 w-full">
                    <div id="trendChart" class="w-full h-full"></div>
                </div>
            </div>

            <!-- Leaderboard: Top Agentes Promotores (col-span-1) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col lg:col-span-1">
                <div class="mb-4">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="ph-bold ph-trophy text-base text-amber-500"></i>
                        Top Promotores del Mes
                    </h3>
                    <p class="text-[10px] text-slate-500 mt-0.5">Líderes en captación de traspasos y dependientes</p>
                </div>
                <div class="flex-1 divide-y divide-slate-100">
                    @foreach($topAgentes as $index => $agente)
                        <div class="py-3 flex items-center justify-between first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <!-- Rank Medal/Pill -->
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-mono font-black text-xs 
                                    @if($index === 0) bg-amber-500/15 text-amber-600 @elseif($index === 1) bg-slate-400/15 text-slate-600 @elseif($index === 2) bg-amber-700/15 text-amber-700 @else bg-slate-100 text-slate-500 @endif">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-1 uppercase tracking-tight max-w-[160px]">{{ $agente['agente'] ?: 'SIN NOMBRE' }}</h4>
                                    <span class="text-[8px] font-mono text-slate-400 block mt-0.5">PROMOTOR DE CAMPAÑA</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-slate-800 font-mono block">{{ number_format($agente['count']) }} <span class="text-[8px] font-normal text-slate-400">Traspasos</span></span>
                                <span class="text-[9px] font-mono text-slate-500 font-bold block">{{ number_format($agente['sum_deps']) }} <span class="text-[8px] font-normal text-slate-400">Deps</span></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Data Filters Bar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="w-full md:w-1/3 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ph ph-magnifying-glass"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por afiliado, cédula, agente o ID CMD..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl text-xs font-mono transition-all">
                </div>
                
                <div class="flex flex-wrap gap-2 w-full md:w-auto justify-end">
                    <!-- Solicitud Date -->
                    <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        <span class="text-[9px] font-mono text-slate-500 font-bold uppercase">Solicitud</span>
                        <input type="date" wire:model.live="fechaSolicitud" class="bg-transparent border-0 p-0 text-xs font-mono focus:ring-0 text-slate-700">
                    </div>
                    
                    <!-- Efectivo Date -->
                    <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        <span class="text-[9px] font-mono text-slate-500 font-bold uppercase">Efectivo</span>
                        <input type="date" wire:model.live="fechaEfectivo" class="bg-transparent border-0 p-0 text-xs font-mono focus:ring-0 text-slate-700">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2 border-t border-slate-100">
                <!-- Filter Estado -->
                <div class="space-y-1">
                    <label class="text-[9px] font-mono font-bold text-slate-400 uppercase">Estado Remoto</label>
                    <select wire:model.live="filterEstado" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-xs text-slate-700">
                        <option value="all">Todos los Estados</option>
                        @foreach($estados as $est)
                            <option value="{{ $est }}">{{ $est }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Agente -->
                <div class="space-y-1">
                    <label class="text-[9px] font-mono font-bold text-slate-400 uppercase">Agente Master</label>
                    <select wire:model.live="filterAgente" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-xs text-slate-700">
                        <option value="all">Todos los Agentes</option>
                        @foreach($agentes as $ag)
                            <option value="{{ $ag }}">{{ $ag }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Periodo -->
                <div class="space-y-1">
                    <label class="text-[9px] font-mono font-bold text-slate-400 uppercase">Periodo Solicitud</label>
                    <select wire:model.live="filterPeriodo" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-xs text-slate-700 select-mono">
                        <option value="all">Todos los Periodos</option>
                        @foreach($periodos as $per)
                            <option value="{{ $per }}">{{ $per }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Unipago -->
                <div class="space-y-1">
                    <label class="text-[9px] font-mono font-bold text-slate-400 uppercase">Estatus Unipago</label>
                    <select wire:model.live="filterStatusUnipago" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-xs text-slate-700">
                        <option value="all">Todos los Estatus</option>
                        @foreach($statusUnipagos as $uni)
                            <option value="{{ $uni }}">{{ $uni }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-mono font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4 cursor-pointer select-none" wire:click="setOrder('firebase_document_id')">
                                <span class="flex items-center gap-1.5">
                                    ID CMD
                                    @if($orderBy === 'firebase_document_id')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 cursor-pointer select-none" wire:click="setOrder('nombre_afiliado')">
                                <span class="flex items-center gap-1.5">
                                    Afiliado
                                    @if($orderBy === 'nombre_afiliado')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 cursor-pointer select-none" wire:click="setOrder('cedula_afiliado')">
                                <span class="flex items-center gap-1.5">
                                    Cédula
                                    @if($orderBy === 'cedula_afiliado')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 cursor-pointer select-none" wire:click="setOrder('agente')">
                                <span class="flex items-center gap-1.5">
                                    Agente
                                    @if($orderBy === 'agente')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center">Deps</th>
                            <th class="px-6 py-4 cursor-pointer select-none text-center" wire:click="setOrder('fecha_solicitud')">
                                <span class="flex items-center gap-1.5 justify-center">
                                    Solicitud
                                    @if($orderBy === 'fecha_solicitud')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center cursor-pointer select-none" wire:click="setOrder('status_unipago')">
                                <span class="flex items-center gap-1.5 justify-center">
                                    Unipago
                                    @if($orderBy === 'status_unipago')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 text-center cursor-pointer select-none" wire:click="setOrder('estado')">
                                <span class="flex items-center gap-1.5 justify-center">
                                    Estado Remoto
                                    @if($orderBy === 'estado')
                                        <i class="ph {{ $orderDir === 'asc' ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                                    @endif
                                </span>
                            </th>
                            <th class="px-6 py-4 text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-sans">
                        @forelse($traspasos as $tr)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4 font-mono font-bold text-slate-800">#{{ $tr->firebase_document_id }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900 capitalize">{{ strtolower($tr->nombre_afiliado) }}</td>
                                <td class="px-6 py-4 font-mono text-slate-600">{{ preg_replace('/(\d{3})(\d{7})(\d{1})/', '$1-$2-$3', $tr->cedula_afiliado) }}</td>
                                <td class="px-6 py-4 font-medium text-slate-700 capitalize">{{ strtolower($tr->agente) }}</td>
                                <td class="px-6 py-4 font-mono text-center text-slate-800">{{ $tr->cantidad_dependientes }}</td>
                                <td class="px-6 py-4 font-mono text-center text-slate-600">{{ $tr->fecha_solicitud ? $tr->fecha_solicitud->format('d/m/Y') : '─' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if(strtoupper($tr->status_unipago) === 'APROBADO')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-mono font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 uppercase tracking-wider">APROBADO</span>
                                    @elseif(strtoupper($tr->status_unipago) === 'PENDIENTE')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-mono font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20 uppercase tracking-wider">PENDIENTE</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-mono font-bold bg-rose-500/10 text-rose-600 border border-rose-500/20 uppercase tracking-wider">{{ $tr->status_unipago }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(strtoupper($tr->estado) === 'EFECTIVO')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-sans font-black bg-blue-500/10 text-blue-600 border border-blue-500/20 uppercase tracking-wider">EFECTIVO</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-sans font-black bg-slate-100 text-slate-600 border border-slate-200 uppercase tracking-wider">{{ $tr->estado }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="showDetail({{ $tr->id }})" class="p-2 text-slate-400 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 rounded-xl transition-all shadow-xs">
                                        <i class="ph-bold ph-eye text-md"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400">
                                            <i class="ph-bold ph-database text-2xl"></i>
                                        </div>
                                        <span class="text-xs font-semibold">No se encontraron registros de traspasos.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($traspasos->hasPages())
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $traspasos->links() }}
                </div>
            @endif
        </div>

        <!-- Detail Modal -->
        @if($showDetailModal && $selectedTraspaso)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 transition-all duration-300">
                <div class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    
                    <!-- Modal Header -->
                    <div class="bg-slate-50 px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-mono font-bold bg-blue-500/10 text-blue-600 px-2 py-0.5 rounded uppercase">MAESTRO CMD</span>
                                <span class="text-xs font-mono font-bold text-slate-500">#{{ $selectedTraspaso->firebase_document_id }}</span>
                            </div>
                            <h3 class="text-md font-bold text-slate-950 mt-1 uppercase tracking-tight">Detalles de Traspaso Remoto</h3>
                        </div>
                        <button wire:click="closeDetailModal" class="w-8 h-8 rounded-xl bg-slate-200/50 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all">
                            <i class="ph-bold ph-x text-sm"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-lg font-black font-mono">
                                {{ substr($selectedTraspaso->nombre_afiliado, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $selectedTraspaso->nombre_afiliado }}</h4>
                                <p class="text-xs text-slate-500 font-mono mt-0.5">Cédula: {{ preg_replace('/(\d{3})(\d{7})(\d{1})/', '$1-$2-$3', $selectedTraspaso->cedula_afiliado) }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Agente Promotor</span>
                                <span class="text-xs font-bold text-slate-800 capitalize block mt-1">{{ strtolower($selectedTraspaso->agente ?: 'SISTEMA') }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Dependientes Declarados</span>
                                <span class="text-xs font-bold text-slate-800 font-mono block mt-1">{{ $selectedTraspaso->cantidad_dependientes }} dependientes</span>
                            </div>
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Fecha Solicitud</span>
                                <span class="text-xs font-bold text-slate-800 font-mono block mt-1">{{ $selectedTraspaso->fecha_solicitud ? $selectedTraspaso->fecha_solicitud->format('d/m/Y') : '─' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Fecha Efectivo</span>
                                <span class="text-xs font-bold text-slate-800 font-mono block mt-1">{{ $selectedTraspaso->fecha_efectivo ? $selectedTraspaso->fecha_efectivo->format('d/m/Y') : '─' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Periodo Facturación</span>
                                <span class="text-xs font-bold text-slate-800 font-mono block mt-1">{{ $selectedTraspaso->periodo ?: '─' }}</span>
                            </div>
                            <div class="bg-slate-50/50 p-3.5 rounded-xl border border-slate-100">
                                <span class="text-[9px] font-mono font-bold text-slate-400 uppercase block">Status Unipago</span>
                                <span class="text-xs font-bold text-slate-800 block mt-1 uppercase">{{ $selectedTraspaso->status_unipago }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h4 class="text-[10px] font-mono font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                <i class="ph ph-database text-xs text-indigo-500"></i>
                                Trazabilidad y Sincronización Local
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-[10px] font-mono text-slate-600">
                                <div class="flex justify-between py-1.5 border-b border-slate-50">
                                    <span>Origen de Datos:</span>
                                    <strong class="text-slate-800 font-bold uppercase">{{ $selectedTraspaso->source_system }}</strong>
                                </div>
                                <div class="flex justify-between py-1.5 border-b border-slate-50">
                                    <span>Estado de Sincronización:</span>
                                    <strong class="text-emerald-600 font-bold uppercase">{{ $selectedTraspaso->sync_status }}</strong>
                                </div>
                                <div class="flex justify-between py-1.5 border-b border-slate-50 col-span-2">
                                    <span>Última Actualización Firebase (Remoto):</span>
                                    <strong class="text-slate-800">{{ $selectedTraspaso->firebase_updated_at ? $selectedTraspaso->firebase_updated_at->format('d/m/Y h:i:s A') : '─' }}</strong>
                                </div>
                                <div class="flex justify-between py-1.5 border-b border-slate-50 col-span-2">
                                    <span>Ingestado en Base de Datos Local:</span>
                                    <strong class="text-slate-800">{{ $selectedTraspaso->synced_at ? $selectedTraspaso->synced_at->format('d/m/Y h:i:s A') : '─' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex justify-end">
                        <button wire:click="closeDetailModal" class="px-4 py-2 bg-slate-200/80 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold tracking-wide transition-all">
                            CERRAR DETALLES
                        </button>
                    </div>

                </div>
            </div>
        @endif
    @endif
</div>

<!-- Scripts for Analytics ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        initTrendChart();
    });

    // Support Livewire navigation and component lifecycle updates
    document.addEventListener("livewire:init", () => {
        Livewire.hook('element.init', ({ el, component }) => {
            // Auto reinitialize chart if trendChart element exists
            if (document.querySelector("#trendChart")) {
                setTimeout(initTrendChart, 50);
            }
        });
    });

    // Also watch for Livewire updates/triggers
    if (typeof window.Livewire !== 'undefined') {
        window.Livewire.on('chartUpdated', () => {
            initTrendChart();
        });
    }

    function initTrendChart() {
        const chartEl = document.querySelector("#trendChart");
        if (!chartEl) return;
        
        // Clean existing chart instance to avoid duplicates
        chartEl.innerHTML = '';

        const options = {
            series: [{
                name: 'Total Traspasos',
                type: 'area',
                data: @json($chartTotalTransfers ?? [])
            }, {
                name: 'Efectivos',
                type: 'area',
                data: @json($chartEffectiveTransfers ?? [])
            }, {
                name: 'Dependientes',
                type: 'column',
                data: @json($chartTotalDependents ?? [])
            }],
            chart: {
                height: 250,
                type: 'line',
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif'
            },
            stroke: {
                width: [2.5, 2.5, 0],
                curve: 'smooth'
            },
            fill: {
                type: ['gradient', 'gradient', 'solid'],
                gradient: {
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    opacityFrom: [0.25, 0.18, 0],
                    opacityTo: [0.03, 0.02, 0],
                    stops: [0, 90, 100]
                }
            },
            colors: ['#6366f1', '#10b981', '#a78bfa'],
            labels: @json($chartLabels ?? []),
            xaxis: {
                type: 'category',
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '9px',
                        fontWeight: 600,
                        fontFamily: 'JetBrains Mono, monospace'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '9px',
                        fontFamily: 'JetBrains Mono, monospace'
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: {
                    left: 10,
                    right: 10,
                    bottom: 0,
                    top: 10
                }
            },
            legend: {
                show: false
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light',
                style: {
                    fontSize: '10px'
                }
            }
        };

        const chart = new ApexCharts(chartEl, options);
        chart.render();
    }
</script>
