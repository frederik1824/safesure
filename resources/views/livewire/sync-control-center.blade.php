<div class="min-h-screen font-sans text-slate-700 p-4 md:p-8 pb-24 -mx-4 md:-mx-8 -mt-8" style="background-color: #f8fafc !important;" wire:poll.visible.{{ $polling ? '2s' : '30s' }}="updateStatus">
    <!-- Premium Vercel/Apple Light & Elegant Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');
        
        .font-sans { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* General Ambient Background Override */
        body, html, main, .bg-slate-900, .bg-slate-950, #app {
            background-color: #f8fafc !important;
            background-image: 
                radial-gradient(circle at 50% -10%, rgba(99, 102, 241, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.02) 0%, transparent 40%) !important;
        }

        /* Glassmorphic Surfaces - Elegant Light Dashboard Cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04), 0 1px 3px 0 rgba(0, 0, 0, 0.01), inset 0 1px 0 0 rgba(255, 255, 255, 0.8);
            transition: all 300ms cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-card:hover {
            border-color: rgba(6, 182, 212, 0.3);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 20px 40px -10px rgba(6, 182, 212, 0.08), 0 1px 5px 0 rgba(0, 0, 0, 0.02), inset 0 1px 0 0 rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        /* Modern Apple-like tabs in Light Mode */
        .apple-tab {
            color: #64748b;
            transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .apple-tab:hover {
            color: #0f172a;
            background: rgba(0, 0, 0, 0.02);
        }

        .apple-tab.active {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: rgba(0, 0, 0, 0.06) !important;
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.06), 0 1px 3px 0 rgba(0, 0, 0, 0.02);
        }

        /* Modern interactive actions button */
        .btn-minimal {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            color: #334155;
            font-weight: 600;
            transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }

        .btn-minimal:hover:not(:disabled) {
            background: #f8fafc;
            border-color: rgba(0, 0, 0, 0.15);
            color: #0f172a;
            transform: translateY(-1px);
        }

        .btn-premium {
            background: linear-gradient(180deg, #06b6d4 0%, #0891b2 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
            color: #ffffff;
            font-weight: 600;
            transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 15px -2px rgba(6, 182, 212, 0.15);
        }

        .btn-premium:hover:not(:disabled) {
            background: linear-gradient(180deg, #0891b2 0%, #0e7490 100%);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 6px 20px -2px rgba(6, 182, 212, 0.25);
            transform: translateY(-1px);
        }

        /* Breathing Dot Pulses (Light) */
        .pulse-dot {
            position: relative;
            display: inline-flex;
            border-radius: 50%;
            height: 8px;
            width: 8px;
        }

        .pulse-dot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.35;
            animation: pulse-ring-ambient 3s cubic-bezier(0.16, 1, 0.3, 1) infinite;
        }

        @keyframes pulse-ring-ambient {
            0% { transform: scale(0.7); opacity: 0.8; }
            100% { transform: scale(2.5); opacity: 0; }
        }

        /* Soft scrollbar aesthetics */
        .custom-scroller::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroller::-webkit-scrollbar-track { background: transparent; }
        .custom-scroller::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 10px; }
        .custom-scroller::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.2); }

        /* Premium Skeleton shimmer */
        .shimmer-active {
            background: linear-gradient(90deg, rgba(0,0,0,0.01) 25%, rgba(0,0,0,0.04) 50%, rgba(0,0,0,0.01) 75%);
            background-size: 200% 100%;
            animation: shimmer-pulse 1.8s infinite;
        }

        @keyframes shimmer-pulse {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>

    <!-- 1. HEADER HUD STATUS STRIP -->
    <div class="sticky top-0 z-[100] border-b border-slate-200 backdrop-blur-md bg-white/80 py-4 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 md:px-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 bg-slate-100 rounded-2xl flex items-center justify-center border border-slate-200 shadow-sm group hover:border-[#0891b2]/40 transition-all duration-300">
                    <i class="ph-bold ph-cloud-arrow-down text-[#0891b2] text-xl group-hover:scale-105 transition-transform"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-sans font-extrabold text-slate-900 tracking-tight">SafeSync Nexus</h2>
                        <span class="bg-slate-100 text-slate-600 text-[9px] font-mono font-bold px-2 py-0.5 rounded-full border border-slate-200 uppercase tracking-wider">v3.0 Blaze</span>
                    </div>
                    <div class="flex items-center gap-3.5 mt-1.5">
                        <div class="flex items-center gap-2 group cursor-pointer" title="Servicio conectado y listo">
                            <span class="pulse-dot text-emerald-500"></span>
                            <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-500 group-hover:text-slate-700 transition-colors">CLOUD ENGINE: <span class="text-emerald-600 font-bold">ONLINE</span></span>
                        </div>
                        <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                        <div class="flex items-center gap-2 group cursor-pointer" title="Estado de tareas de fondo">
                            <span class="pulse-dot {{ $pendingJobs > 10 ? 'text-amber-500' : 'text-emerald-500' }}"></span>
                            <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-slate-500 group-hover:text-slate-700 transition-colors">DAEMON STATUS: <span class="font-bold {{ $pendingJobs > 10 ? 'text-amber-600 animate-pulse' : 'text-emerald-600' }}">{{ $pendingJobs > 10 ? 'BUSY' : 'IDLE' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4 bg-slate-100 p-2 pr-5 rounded-2xl border border-slate-200 shadow-sm" title="Métricas globales e integridad del sistema">
                <div class="flex -space-x-1">
                    <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex flex-col items-center justify-center text-slate-700 shadow-xs">
                        <span class="text-[5px] font-mono font-bold leading-none mb-0.5">LOCAL</span>
                        <span class="text-[8px] font-mono font-bold leading-none">{{ number_format(($totalAfiliados + $totalEmpresas) / 1000, 1) }}K</span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-[#0891b2]/10 border border-[#0891b2]/20 flex flex-col items-center justify-center text-[#0891b2] shadow-xs">
                        <span class="text-[5px] font-mono font-bold leading-none mb-0.5">CLOUD</span>
                        <span class="text-[8px] font-mono font-bold leading-none">{{ number_format($estimatedCloudCount / 1000, 1) }}K</span>
                    </div>
                </div>
                <div class="h-6 w-px bg-slate-200 mx-1"></div>
                <div class="text-right">
                    <p class="text-[7px] font-mono font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">INTEGRIDAD</p>
                    @if(($totalAfiliados + $totalEmpresas) == $estimatedCloudCount)
                        <div class="flex items-center justify-end gap-1">
                            <span class="text-[10px] font-mono font-bold text-emerald-600">ALIGNED</span>
                            <i class="ph-fill ph-check-circle text-emerald-500 text-xs"></i>
                        </div>
                    @else
                        <div class="flex items-center justify-end gap-1">
                            <span class="text-[10px] font-mono font-bold text-[#0891b2]">DRIFT</span>
                            <i class="ph-bold ph-warning-circle text-[#0891b2] text-xs"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 2. APPLE DASHBOARD TABS NAVIGATION -->
    <div class="max-w-7xl mx-auto px-4 md:px-8 mt-6">
        <div class="flex flex-wrap md:flex-nowrap items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 gap-1">
            <button wire:click="setTab('dashboard')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'dashboard' ? 'active' : '' }}">
                <i class="ph-bold ph-chart-bar text-sm mr-2 align-middle"></i>
                <span class="align-middle">DASHBOARD</span>
            </button>
            <button wire:click="setTab('costs')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'costs' ? 'active' : '' }}">
                <i class="ph-bold ph-coins text-sm mr-2 align-middle"></i>
                <span class="align-middle">MÉTRICAS Y COSTOS</span>
            </button>
            <button wire:click="setTab('records')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'records' ? 'active' : '' }}">
                <i class="ph-bold ph-database text-sm mr-2 align-middle"></i>
                <span class="align-middle">INVENTARIO</span>
            </button>
            <button wire:click="setTab('timeline')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'timeline' ? 'active' : '' }}">
                <i class="ph-bold ph-activity text-sm mr-2 align-middle"></i>
                <span class="align-middle">TELEMETRÍA</span>
            </button>
            <button wire:click="setTab('conflicts')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'conflicts' ? 'active' : '' }} relative">
                <i class="ph-bold ph-warning-octagon text-sm mr-2 align-middle"></i>
                <span class="align-middle">RESOLUCIÓN</span>
                @if($totalConflicts > 0)
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse border border-white"></span>
                @endif
            </button>
            <button wire:click="setTab('traspasos')" class="flex-1 md:flex-none px-5 py-2.5 rounded-xl text-xs font-mono font-semibold transition-all apple-tab {{ $activeTab === 'traspasos' ? 'active' : '' }}">
                <i class="ph-bold ph-swap text-sm mr-2 align-middle"></i>
                <span class="align-middle">SINCRONIZACIÓN DE TRASPASOS</span>
            </button>
        </div>
    </div>

    <!-- MAIN BODY CONTENT -->
    <div class="max-w-7xl mx-auto px-4 md:px-8 mt-6">
        @if(session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 rounded-2xl text-xs font-mono font-bold flex items-center gap-3">
                <i class="ph-bold ph-check-circle text-lg text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session()->has('warning'))
            <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 text-amber-700 rounded-2xl text-xs font-mono font-bold flex items-center gap-3">
                <i class="ph-bold ph-warning-circle text-lg text-amber-600"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- TAB: DASHBOARD                                 -->
        <!-- ============================================== -->
        @if($activeTab === 'dashboard')
            @if($recentLog && $recentLog->status === 'failed' && (str_contains(strtolower($recentLog->message), 'cuota') || str_contains(strtolower($recentLog->message), 'quota') || str_contains(strtolower($recentLog->message), '429') || str_contains(strtolower($recentLog->message), 'resource_exhausted') || str_contains(strtolower($recentLog->message), 'exceeded') || str_contains(strtolower($recentLog->message), 'excedida')))
                <!-- ALERTA CUOTA EXCEDIDA -->
                <div class="p-6 rounded-3xl bg-amber-500/5 border border-amber-500/20 mb-6 relative overflow-hidden">
                    <div class="flex flex-col md:flex-row items-center gap-5 relative z-10">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 border border-amber-500/20">
                            <i class="ph-bold ph-shield-warning text-2xl animate-pulse"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-sans font-bold text-slate-900 uppercase tracking-wider">Plan Firebase Spark Excedido (Límite 50K diario)</h4>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                El motor Firebase <span class="text-slate-800 font-bold">"{{ config('services.firebase.project_id', 'syscarnet') }}"</span> ha alcanzado su cuota diaria. Recomendamos migrar a un <strong class="text-amber-600">Plan Blaze (Pago por consumo)</strong> para evitar interrupciones de sincronización. Las lecturas en plan Blaze cuestan aproximadamente $0.06 por cada 100k consultas.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($polling && $recentLog)
                <!-- PROCESADOR ACTIVO -->
                <div class="glass-card p-6 md:p-8 mb-6 border-[#06b6d4]/20 bg-[#06b6d4]/[0.02]">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#06b6d4]/10 rounded-2xl flex items-center justify-center text-[#0891b2] border border-[#06b6d4]/20">
                                <i class="ph-bold ph-arrows-clockwise text-2xl animate-spin"></i>
                            </div>
                            <div>
                                <h4 class="text-md font-sans font-bold text-slate-900 uppercase tracking-wider">Procesador Nexus Sincronizando</h4>
                                <p class="text-xs text-slate-600 mt-1">Colección actual: <strong class="text-[#0891b2] capitalize font-mono">{{ $recentLog->process_name ?: 'afiliados' }}</strong> en tiempo real.</p>
                            </div>
                        </div>
                        <button wire:click="stopSync" class="px-4 py-2 bg-rose-500/10 border border-rose-500/20 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-xs font-semibold transition-all flex items-center gap-2">
                            <i class="ph-bold ph-stop-circle text-sm"></i>
                            <span>DETENER PROCESO</span>
                        </button>
                    </div>

                    <!-- Sleek Progress Systems -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-xs font-mono font-bold text-slate-500">
                            <span>PROGRESO DE TRANSFERENCIA</span>
                            <span class="text-[#0891b2]">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden border border-slate-300">
                            <div class="h-full bg-gradient-to-r from-cyan-400 to-[#06b6d4] rounded-full shadow-[0_0_15px_rgba(6,182,212,0.2)] transition-all duration-500" style="width: {{ $progressPercentage }}%;"></div>
                        </div>
                    </div>

                    <!-- Mini status grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 text-center shadow-xs">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Sincronizados</p>
                            <p class="text-lg font-mono font-semibold text-slate-800 mt-1">{{ number_format($recordsSynced) }}</p>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 text-center shadow-xs">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Creados</p>
                            <p class="text-lg font-mono font-semibold text-emerald-600 mt-1">{{ number_format($recentLog->records_added) }}</p>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 text-center shadow-xs">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Actualizados</p>
                            <p class="text-lg font-mono font-semibold text-[#0891b2] mt-1">{{ number_format($recentLog->records_updated) }}</p>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 text-center shadow-xs">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Omitidos</p>
                            <p class="text-lg font-mono font-semibold text-amber-600 mt-1">{{ number_format($recentLog->records_skipped) }}</p>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 text-center shadow-xs col-span-2 sm:col-span-1">
                            <p class="text-[8px] font-mono text-slate-500 uppercase tracking-widest">Errores</p>
                            <p class="text-lg font-mono font-semibold text-rose-600 mt-1">{{ number_format($recentLog->records_failed) }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($lastSyncSummary)
                <!-- STATUS TRAY -->
                <div class="glass-card p-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-600">
                                <i class="ph-bold ph-check text-base"></i>
                            </div>
                            <div>
                                <h6 class="text-xs font-mono font-bold text-slate-800">ÚLTIMO PROCESO: {{ strtoupper($lastSyncSummary['status']) }}</h6>
                                <p class="text-[9px] font-mono text-slate-400 uppercase mt-0.5 font-semibold">FINALIZADO A LAS {{ $lastSyncSummary['time'] }}</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="text-right">
                                <p class="text-[8px] font-mono text-slate-500 uppercase tracking-wider">DESCARGADOS</p>
                                <p class="text-sm font-mono font-bold text-emerald-600">{{ number_format($lastSyncSummary['synced']) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-mono text-slate-500 uppercase tracking-wider">FALLIDOS</p>
                                <p class="text-sm font-mono font-bold text-rose-600">{{ number_format($lastSyncSummary['failed']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($isCircuitOpen)
                <!-- CORTACIRCUITOS ACTIVO -->
                <div class="p-6 rounded-3xl bg-rose-500/5 border border-rose-500/20 mb-6 relative overflow-hidden">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-5 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-600 border border-rose-500/20">
                                <i class="ph-bold ph-lightning text-xl animate-pulse"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-sans font-bold text-slate-900 uppercase tracking-wider">Corta-circuitos de protección activo</h4>
                                <p class="text-xs text-slate-600 mt-1 max-w-2xl leading-relaxed">
                                    SafeSync bloqueó llamadas externas tras detectar un error HTTP 429 de cuota excedida. Si ya migraste al plan Blaze, puedes ignorar el límite restableciendo el circuito.
                                </p>
                            </div>
                        </div>
                        <button wire:click="resetCircuitBreaker" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl transition-all shadow-md">
                            RESTABLECER CORTA-CIRCUITOS
                        </button>
                    </div>
                </div>
            @endif

            <!-- 3. FIVE CORE SEGREGATED METRICS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-6">
                <!-- 1. TOTAL AFILIADOS -->
                <div class="glass-card p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-9 h-9 bg-cyan-100 rounded-xl flex items-center justify-center text-[#0891b2] border border-cyan-200">
                            <i class="ph-bold ph-users text-lg"></i>
                        </div>
                        <span class="text-[8px] font-mono font-bold text-cyan-700 bg-cyan-500/10 px-2 py-0.5 rounded-full uppercase">AFILIADOS</span>
                    </div>
                    <h3 class="text-2xl font-sans font-extrabold text-slate-900 tracking-tight">{{ number_format($totalAfiliados) }}</h3>
                    <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest mt-1">TOTAL AFILIADOS</p>
                    <div class="border-t border-slate-200 pt-2.5 mt-3 text-[9px] font-mono text-slate-500 leading-normal flex items-center gap-1.5">
                        <i class="ph-bold ph-user-check text-emerald-600"></i>
                        <span>En base de datos SafeSure</span>
                    </div>
                </div>

                <!-- 2. TOTAL EMPRESAS -->
                <div class="glass-card p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 border border-purple-200">
                            <i class="ph-bold ph-buildings text-lg"></i>
                        </div>
                        <span class="text-[8px] font-mono font-bold text-purple-700 bg-purple-500/10 px-2 py-0.5 rounded-full uppercase">EMPRESAS</span>
                    </div>
                    <h3 class="text-2xl font-sans font-extrabold text-slate-900 tracking-tight">{{ number_format($totalEmpresas) }}</h3>
                    <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest mt-1">TOTAL EMPRESAS</p>
                    <div class="border-t border-slate-200 pt-2.5 mt-3 text-[9px] font-mono text-slate-500 leading-normal flex items-center gap-1.5">
                        <i class="ph-bold ph-shield text-purple-600"></i>
                        <span>Registros patronales activos</span>
                    </div>
                </div>

                <!-- 3. PENDING PUSH -->
                <div class="glass-card p-5">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200">
                            <i class="ph-bold ph-paper-plane-tilt text-lg"></i>
                        </div>
                        @if($pendingPush > 0)
                            <span class="text-[8px] font-mono font-bold text-amber-700 bg-amber-500/10 px-2 py-0.5 rounded-full animate-pulse">PENDING</span>
                        @else
                            <span class="text-[8px] font-mono font-bold text-emerald-700 bg-emerald-500/10 px-2 py-0.5 rounded-full">ALIGNED</span>
                        @endif
                    </div>
                    <h3 class="text-2xl font-sans font-extrabold text-slate-900 tracking-tight">{{ number_format($pendingPush) }}</h3>
                    <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest mt-1">EN TRÁNSITO (PENDIENTES)</p>
                    <div class="border-t border-slate-200 pt-2.5 mt-3 text-[9px] font-mono text-slate-500 leading-normal flex items-center gap-1.5">
                        <i class="ph-bold ph-info text-slate-400"></i>
                        <span>Cambios listos para subir</span>
                    </div>
                </div>

                <!-- 4. CONFLICTS -->
                <div class="glass-card p-5 {{ $totalConflicts > 0 ? 'bg-rose-500/[0.01] border-rose-500/20 shadow-sm' : '' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200 {{ $totalConflicts > 0 ? 'text-rose-500 bg-rose-500/5' : '' }}">
                            <i class="ph-bold ph-warning-octagon text-lg"></i>
                        </div>
                        @if($totalConflicts > 0)
                            <span wire:click="setTab('conflicts')" class="text-[8px] font-mono font-bold text-white bg-rose-600 px-2.5 py-0.5 rounded-full cursor-pointer hover:bg-rose-500 transition-all uppercase">RESOLVER</span>
                        @else
                            <span class="text-[8px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full uppercase">SECURE</span>
                        @endif
                    </div>
                    <h3 class="text-2xl font-sans font-extrabold {{ $totalConflicts > 0 ? 'text-rose-600' : 'text-slate-900' }} tracking-tight">{{ number_format($totalConflicts) }}</h3>
                    <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest mt-1">COLISIONES DE CHECKSUM</p>
                    <div class="border-t border-slate-200 pt-2.5 mt-3 text-[9px] font-mono text-slate-500 leading-normal flex items-center gap-1.5">
                        <i class="ph-bold ph-git-merge text-slate-400"></i>
                        <span>Divergencia entre local y la nube</span>
                    </div>
                </div>

                <!-- 5. EFFICIENCY SAVINGS -->
                <div class="glass-card p-5">
                    <div class="flex justify-between items-start mb-2">
                        <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 border border-slate-200">
                            <i class="ph-bold ph-shield-check text-lg"></i>
                        </div>
                        <span class="text-[8px] font-mono font-bold text-emerald-700 bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase">HASH AHORRO</span>
                    </div>
                    <h3 class="text-2xl font-sans font-extrabold text-slate-900 tracking-tight">+{{ number_format($savingsCount) }}</h3>
                    <p class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest mt-1">REGISTROS AHORRADOS</p>
                    <div class="border-t border-slate-200 pt-2.5 mt-3 text-[9px] font-mono text-slate-500 leading-normal flex items-center gap-1.5">
                        <i class="ph-bold ph-coins text-emerald-600"></i>
                        <span>Escrituras redundantes evitadas</span>
                    </div>
                </div>
            </div>

            <!-- CORE SYSTEM AND LOGS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- PANEL IZQUIERDO: CONTROLES & CHECKPOINTS -->
                <div class="lg:col-span-2 glass-card p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h4 class="text-md font-sans font-bold text-slate-900 tracking-tight">Motores de Sincronización Nexus</h4>
                                <p class="text-xs text-slate-500 mt-1">Desencadena transferencias incrementales o completas bajo demanda.</p>
                            </div>
                            <button wire:click="updateStatus" class="w-8 h-8 btn-minimal rounded-xl flex items-center justify-center group" title="Refrescar Estado">
                                <i class="ph-bold ph-arrows-counter-clockwise text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                            </button>
                        </div>

                        <!-- Checkpoints grid -->
                        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                            <table class="w-full text-left font-mono">
                                <thead class="bg-slate-50 text-[8px] font-mono font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3">DATABASE SEGMENT</th>
                                        <th class="px-5 py-3">CURSOR</th>
                                        <th class="px-5 py-3">STATUS</th>
                                        <th class="px-5 py-3 text-right">TIMESTAMP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                    @foreach($checkpoints as $cp)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-5 py-3.5">
                                                <div class="flex items-center gap-2">
                                                    <i class="ph-bold ph-folder-open text-xs text-slate-400"></i>
                                                    <span class="font-bold text-slate-800 capitalize font-sans text-xs">{{ $cp->process_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[9px] font-bold bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-slate-600">{{ $cp->last_cursor ?: '0x000 (INIT_SEED)' }}</span>
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[8px] font-bold px-2 py-0.5 rounded-full {{ $cp->status === 'completed' ? 'text-emerald-700 bg-emerald-500/10 border border-emerald-500/20' : 'text-amber-700 bg-amber-500/10 border border-amber-500/20 animate-pulse' }}">
                                                    {{ $cp->status === 'completed' ? 'COMPLETED' : ($cp->status === 'failed' ? 'FAILED' : 'SYNCING') }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right">
                                                <span class="text-[9px] font-bold text-slate-400">{{ $cp->updated_at->diffForHumans() }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Config Tray -->
                        <div class="mb-6 p-4 rounded-2xl border border-slate-200 bg-slate-50">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div>
                                    <h5 class="text-xs font-sans font-bold text-slate-800">Segmentación Activa</h5>
                                    <p class="text-[9px] font-mono text-slate-500 uppercase mt-0.5">Define qué colección se verá afectada por los procesos manuales</p>
                                </div>
                                <div class="flex items-center bg-white p-1 rounded-xl border border-slate-200 gap-1 shadow-2xs">
                                    <button wire:click="$set('syncTarget', 'all')" class="px-3 py-1.5 rounded-lg text-[9px] font-mono font-bold transition-all {{ $syncTarget === 'all' ? 'bg-[#0891b2]/10 text-[#0891b2] font-extrabold border border-[#0891b2]/20' : 'text-slate-500 hover:text-slate-700' }}">
                                        TODOS
                                    </button>
                                    <button wire:click="$set('syncTarget', 'afiliados')" class="px-3 py-1.5 rounded-lg text-[9px] font-mono font-bold transition-all {{ $syncTarget === 'afiliados' ? 'bg-[#0891b2]/10 text-[#0891b2] font-extrabold border border-[#0891b2]/20' : 'text-slate-500 hover:text-slate-700' }}">
                                        AFILIADOS
                                    </button>
                                    <button wire:click="$set('syncTarget', 'empresas')" class="px-3 py-1.5 rounded-lg text-[9px] font-mono font-bold transition-all {{ $syncTarget === 'empresas' ? 'bg-[#0891b2]/10 text-[#0891b2] font-extrabold border border-[#0891b2]/20' : 'text-slate-500 hover:text-slate-700' }}">
                                        EMPRESAS
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Core actions Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <!-- ACCIÓN: INCREMENTAL -->
                            <button wire:click="syncIncremental" @if($isCircuitOpen) disabled @endif class="flex flex-col gap-3 p-4 btn-minimal rounded-xl text-left group disabled:opacity-30 disabled:pointer-events-none transition-all shadow-sm">
                                <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                    <i wire:loading.remove wire:target="syncIncremental" class="ph-bold ph-lightning text-base"></i>
                                    <i wire:loading wire:target="syncIncremental" class="ph-bold ph-arrows-clockwise text-base animate-spin"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-sans font-bold text-slate-800 leading-tight">Sync Incremental</p>
                                    <p class="text-[8px] font-mono text-emerald-600 font-bold uppercase mt-0.5">FAST & ECO</p>
                                </div>
                            </button>

                            <!-- ACCIÓN: FULL -->
                            <button wire:click="syncFull" @if($isCircuitOpen) disabled @endif class="flex flex-col gap-3 p-4 btn-minimal rounded-xl text-left group disabled:opacity-30 disabled:pointer-events-none transition-all shadow-sm">
                                <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                    <i wire:loading.remove wire:target="syncFull" class="ph-bold ph-cloud-arrow-down text-base"></i>
                                    <i wire:loading wire:target="syncFull" class="ph-bold ph-arrows-clockwise text-base animate-spin"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-sans font-bold text-slate-800 leading-tight">Sync Completo</p>
                                    <p class="text-[8px] font-mono text-amber-600 font-bold uppercase mt-0.5">INSPECT ALL</p>
                                </div>
                            </button>

                            <!-- ACCIÓN: MIS CUENTAS -->
                            <button wire:click="syncMyAffiliates" @if($isCircuitOpen) disabled @endif class="flex flex-col gap-3 p-4 bg-[#0891b2]/5 border border-[#0891b2]/15 rounded-xl text-left group disabled:opacity-30 disabled:pointer-events-none hover:border-[#0891b2]/35 transition-all shadow-sm">
                                <div class="w-8 h-8 bg-[#0891b2]/10 rounded-lg flex items-center justify-center text-[#0891b2] group-hover:scale-105 transition-transform">
                                    <i wire:loading.remove wire:target="syncMyAffiliates" class="ph-bold ph-user-focus text-base"></i>
                                    <i wire:loading wire:target="syncMyAffiliates" class="ph-bold ph-arrows-clockwise text-base animate-spin"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-sans font-bold text-slate-800 leading-tight">Mis Afiliados</p>
                                    <p class="text-[8px] font-mono text-[#0891b2] font-bold uppercase mt-0.5">MY SCOPE</p>
                                </div>
                            </button>

                            <!-- ACCIÓN: PUSH -->
                            <button wire:click="syncPush" @if($isCircuitOpen) disabled @endif class="flex flex-col gap-3 p-4 btn-minimal rounded-xl text-left group disabled:opacity-30 disabled:pointer-events-none transition-all shadow-sm">
                                <div class="w-8 h-8 bg-[#0891b2]/10 rounded-lg flex items-center justify-center text-[#0891b2] group-hover:scale-105 transition-transform">
                                    <i wire:loading.remove wire:target="syncPush" class="ph-bold ph-paper-plane-tilt text-base"></i>
                                    <i wire:loading wire:target="syncPush" class="ph-bold ph-arrows-clockwise text-base animate-spin"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-sans font-bold text-slate-800 leading-tight">Subir Cambios</p>
                                    <p class="text-[8px] font-mono text-slate-500 uppercase mt-0.5 font-bold">OUTBOUND PUSH</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Failsafe Lock panel -->
                    <div class="mt-6 p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between shadow-xs">
                        <span class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-wider">ESTADO DE COLA (CACHE LOCK):</span>
                        @if(\Illuminate\Support\Facades\Cache::has('firebase_sync_lock'))
                            <div class="flex items-center gap-3">
                                <span class="pulse-dot text-amber-500"></span>
                                <span class="text-[9px] font-mono font-bold text-amber-600 uppercase">SYS TRABAJANDO</span>
                                <button wire:click="forceReleaseLock" class="text-[8px] font-mono font-bold text-white bg-rose-600 border border-rose-500 px-3 py-1.5 rounded-lg hover:bg-rose-700 transition-all uppercase tracking-wider">LIBERAR PROCESO</button>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-[9px] font-mono font-bold text-emerald-600 uppercase tracking-wider">SISTEMA LIBRE</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- DERECHA: WEBHOOKS TERMINAL & TELEMETRY -->
                <div class="space-y-6">
                    <!-- Webhooks Stream -->
                    <div class="glass-card p-6 flex flex-col min-h-[300px]">
                        <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                            <h4 class="text-[10px] font-mono font-bold uppercase tracking-widest text-[#0891b2] flex items-center gap-2">
                                <i class="ph-bold ph-terminal-window text-base"></i> TERMINAL: SEÑALES
                            </h4>
                            <div class="flex gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0891b2] animate-pulse"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-[#0891b2]/20"></div>
                            </div>
                        </div>
                        
                        <div class="space-y-3.5 flex-1 overflow-y-auto max-h-[320px] pr-2 custom-scroller font-mono text-xs text-slate-700">
                            @forelse($webhooks as $wh)
                                <div class="border-l-2 border-slate-300 pl-3 py-0.5">
                                    <div class="flex justify-between text-[9px] font-bold text-slate-400 mb-0.5">
                                        <span class="text-[#0891b2] font-bold">▶ {{ strtoupper($wh->event_type) }}</span>
                                        <span>{{ $wh->created_at->format('H:i:s') }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-700 font-bold leading-relaxed">{{ $wh->message }}</p>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center h-full opacity-30 py-12 text-center">
                                    <i class="ph-bold ph-ghost text-2xl mb-2 text-slate-400"></i>
                                    <p class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest">SIN ACTIVIDAD EN WEBHOOK</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Telemetry Signals -->
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <h5 class="text-[10px] font-mono font-bold uppercase tracking-widest text-[#0891b2]">TELEMETRÍA EN VIVO</h5>
                                @if($polling)
                                    <span class="w-2 h-2 bg-[#0891b2] rounded-full animate-ping"></span>
                                @endif
                            </div>
                            <span class="text-[9px] font-mono text-slate-400 uppercase">SIGNAL STREAM</span>
                        </div>
                        
                        <div class="space-y-3.5 relative before:absolute before:left-3 before:top-2 before:bottom-2 before:w-px before:bg-slate-200 max-h-[300px] overflow-y-auto pr-1 custom-scroller">
                            @forelse($liveFeed as $item)
                                <div class="relative pl-7 group">
                                    <div class="absolute left-1.5 top-2.5 w-1.5 h-1.5 rounded-full 
                                        {{ $item['type'] === 'success' ? 'bg-[#0891b2]' : 
                                           ($item['type'] === 'primary' ? 'bg-emerald-500' : 
                                           ($item['type'] === 'danger' ? 'bg-rose-500 animate-pulse' : 'bg-slate-400')) }}"></div>
                                    
                                    <div class="space-y-0.5">
                                        <div class="flex justify-between items-baseline">
                                            <p class="text-[10px] font-bold text-slate-800 font-sans tracking-tight leading-normal">{{ $item['msg'] }}</p>
                                            <time class="text-[8px] font-mono text-slate-400 shrink-0 ml-2 font-bold">{{ $item['time'] }}</time>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 opacity-30">
                                    <i class="ph-bold ph-waveform text-2xl mb-2 text-slate-400 animate-pulse"></i>
                                    <p class="text-[9px] font-mono font-bold text-slate-500 uppercase tracking-widest">ESPERANDO FLUJO...</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- System protocol connections -->
                        <div class="mt-5 pt-4 border-t border-slate-200 font-mono text-[9px] text-slate-600">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-slate-400 uppercase">PROTOCOL:</span>
                                <span class="text-slate-800 font-bold">Firestore REST Engine</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 uppercase">RESOLVED ID:</span>
                                <span class="text-[#0891b2] font-bold">RESPONSABLE_{{ auth()->user()->responsable_id ?? 'ALL' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- TAB: COSTS Y EFFICIENCY                        -->
        <!-- ============================================== -->
        @if($activeTab === 'costs')
            <div class="space-y-6">
                <!-- Cost Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Cards: Reads -->
                    <div class="glass-card p-6 border-[#06b6d4]/20 bg-[#06b6d4]/[0.01]">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-9 h-9 bg-[#0891b2]/10 rounded-xl flex items-center justify-center text-[#0891b2] border border-[#0891b2]/20">
                                <i class="ph-bold ph-eye text-lg"></i>
                            </div>
                            <span class="text-[8px] font-mono font-bold text-[#0891b2] bg-[#0891b2]/10 px-2 py-0.5 rounded-full uppercase tracking-wider">DIARIO</span>
                        </div>
                        <h4 class="text-xs font-sans font-bold text-slate-700 uppercase tracking-wider">Consultas a Firestore</h4>
                        <div class="flex items-baseline gap-1 mt-2">
                            <h3 class="text-2xl font-mono font-extrabold text-slate-900 tracking-tight">{{ number_format($dailyReads) }}</h3>
                            <span class="text-xs font-mono text-slate-500">/ 50K</span>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-2 font-mono">
                            Costo estimado: <strong class="text-[#0891b2]">${{ number_format($dailyReads * 0.0000006, 4) }} USD</strong>
                        </p>
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden border border-slate-300 mt-4">
                            <div class="h-full bg-[#0891b2] rounded-full shadow-[0_0_8px_#0891b2]" style="width: {{ min(($dailyReads / 50000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Cards: Monthly Accumulated -->
                    <div class="glass-card p-6 border-emerald-500/20 bg-emerald-500/[0.01]">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-9 h-9 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-500/20">
                                <i class="ph-bold ph-trend-up text-lg"></i>
                            </div>
                            <span class="text-[8px] font-mono font-bold text-emerald-600 bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase tracking-wider">ACUMULADO</span>
                        </div>
                        <h4 class="text-xs font-sans font-bold text-slate-700 uppercase tracking-wider">Llamadas Totales del Mes</h4>
                        <div class="flex items-baseline gap-4 mt-2">
                            <div>
                                <span class="text-[8px] font-mono text-slate-500 block font-bold">LECTURAS</span>
                                <span class="text-lg font-mono font-bold text-slate-800">{{ number_format($accumulatedReads) }}</span>
                            </div>
                            <div>
                                <span class="text-[8px] font-mono text-slate-500 block font-bold">ESCRITURAS</span>
                                <span class="text-lg font-mono font-bold text-[#0891b2]">{{ number_format($accumulatedWrites) }}</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-2 font-mono">
                            Costo total acumulado: <strong class="text-emerald-600">${{ number_format($accumulatedCost, 4) }} USD</strong>
                        </p>
                    </div>

                    <!-- Cards: Efficiency Savings -->
                    <div class="glass-card p-6 border-amber-500/20 bg-amber-500/[0.01]">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-9 h-9 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 border border-amber-500/20">
                                <i class="ph-bold ph-shield-check text-lg"></i>
                            </div>
                            <span class="text-[8px] font-mono font-bold text-amber-600 bg-amber-400/10 px-2 py-0.5 rounded-full uppercase tracking-wider">EFICIENCIA HASH</span>
                        </div>
                        <h4 class="text-xs font-sans font-bold text-slate-700 uppercase tracking-wider">Checksum Omitidos</h4>
                        <div class="flex items-baseline gap-1 mt-2">
                            <h3 class="text-2xl font-mono font-extrabold text-slate-900 tracking-tight">+{{ number_format($savingsCount) }}</h3>
                            <span class="text-xs font-mono text-slate-500">evitadas</span>
                        </div>
                        <p class="text-[10px] text-slate-600 mt-2 font-mono">
                            Costo total ahorrado: <strong class="text-amber-600">${{ number_format($savingsCount * 0.0000018, 4) }} USD</strong>
                        </p>
                    </div>
                </div>

                <!-- Detailed Costs Table -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 glass-card p-6 md:p-8">
                        <div class="mb-5 border-b border-slate-100 pb-3">
                            <h4 class="text-md font-sans font-bold text-slate-950 tracking-tight">Registro Atómico de Consumo</h4>
                            <p class="text-xs text-slate-500 mt-1">Comparación real de consultas e imputación monetaria de los últimos procesos.</p>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
                            <table class="w-full text-left font-mono text-xs">
                                <thead class="bg-slate-50 text-[8px] font-mono font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3">TIPO</th>
                                        <th class="px-5 py-3">LECTURAS (READS)</th>
                                        <th class="px-5 py-3">ESCRITURAS (WRITES)</th>
                                        <th class="px-5 py-3">DAEMON STATUS</th>
                                        <th class="px-5 py-3 text-right">COSTO</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($logs as $log)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-5 py-3.5">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-slate-800 font-sans text-xs capitalize">{{ $log->type }}</span>
                                                    <span class="text-[9px] text-[#0891b2] font-bold uppercase tracking-wider font-mono mt-0.5">{{ $log->process_name ?: 'Global' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3.5 font-bold text-slate-700">
                                                {{ number_format($log->firebase_reads) }}
                                            </td>
                                            <td class="px-5 py-3.5 font-bold text-slate-700">
                                                {{ number_format($log->firebase_writes) }}
                                            </td>
                                            <td class="px-5 py-3.5">
                                                <span class="text-[8px] font-bold px-2 py-0.5 rounded-full {{ $log->status === 'completed' ? 'text-emerald-700 bg-emerald-500/10 border border-emerald-500/20' : ($log->status === 'failed' ? 'text-rose-700 bg-rose-500/10 border border-rose-500/20' : 'text-amber-700 bg-amber-500/10 border border-amber-500/20') }}">
                                                    {{ $log->status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3.5 text-right font-bold text-emerald-600">
                                                ${{ number_format($log->estimated_cost, 6) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-10 text-slate-400 font-bold">No hay registros de consumo disponibles.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rules Advice -->
                    <div class="glass-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                                <i class="ph-bold ph-shield-alert text-amber-500 text-lg"></i>
                                <h5 class="text-xs font-mono font-bold uppercase tracking-widest text-slate-900">REGLAS CLAVE DE PROTECCIÓN</h5>
                            </div>
                            
                            <ul class="space-y-4 text-xs text-slate-600 leading-relaxed font-mono">
                                <li class="border-b border-slate-200 pb-3">
                                    <span class="text-amber-600 font-bold block mb-1">▶ CONECTIVIDAD BLAZE</span>
                                    Recomendamos fuertemente migrar a plan de consumo Blaze para evitar cuotas Spark y asegurar syncs fluidos de más de 15,000 registros.
                                </li>
                                <li class="border-b border-slate-200 pb-3">
                                    <span class="text-emerald-600 font-bold block mb-1">▶ HASH CHECKCHECKSUM</span>
                                    SafeSync Nexus previene escrituras duplicadas comparando localmente checksums MD5 antes de disparar la API de la nube.
                                </li>
                                <li>
                                    <span class="text-[#0891b2] font-bold block mb-1">▶ INTELIGENCIA DYNAMIC POLLING</span>
                                    La velocidad de refresco Livewire optimiza su polling dinámicamente de 2s a 30s si el sistema se encuentra inactivo.
                                </li>
                            </ul>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200 font-mono text-[9px] text-slate-400 text-center uppercase tracking-wider">
                            Nexus Auditor v3.0 Certified
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- TAB: INVENTARIO DE REGISTROS                   -->
        <!-- ============================================== -->
        @if($activeTab === 'records')
            <div class="glass-card overflow-hidden">
                <!-- Filter Bar -->
                <div class="p-6 md:p-8 bg-white/50 border-b border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-md font-sans font-bold text-slate-950 tracking-tight">Inventario de Expedientes</h4>
                            <span class="px-2.5 py-0.5 bg-cyan-500/10 text-cyan-700 text-[8px] font-mono font-bold rounded-lg border border-cyan-500/20 uppercase tracking-widest">DATABASE FEED</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Auditoría atómica de versiones locales e integridad en los servidores de la nube.</p>
                    </div>
                    
                    <div class="flex flex-wrap gap-3 items-center w-full md:w-auto">
                        <div class="relative flex-1 md:w-80">
                            <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input wire:model.live="search" type="text" placeholder="Buscar por nombre, cédula o UID..." class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-cyan-500 transition-all placeholder-slate-400 shadow-2xs">
                        </div>
                        <select wire:model.live="filterStatus" class="py-2.5 px-4 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-cyan-500 transition-all shadow-2xs">
                            <option value="all" class="bg-white">TODOS LOS REGISTROS</option>
                            <option value="synced" class="bg-white">SINCRONIZADOS</option>
                            <option value="pending" class="bg-white">PENDIENTES</option>
                            <option value="error" class="bg-white">CON ERRORES</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto custom-scroller">
                    <table class="w-full text-left font-mono">
                        <thead>
                            <tr class="bg-slate-50 text-[8px] font-mono font-bold text-slate-500 uppercase tracking-widest border-b border-slate-200">
                                <th class="px-6 py-3.5">AFILIADO / EXPEDIENTE</th>
                                <th class="px-6 py-3.5">COMPAÑÍA / EMISOR</th>
                                <th class="px-6 py-3.5">LOCAL STATE</th>
                                <th class="px-6 py-3.5">CLOUD STATUS</th>
                                <th class="px-6 py-3.5 text-right">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @foreach($records as $record)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-sans font-bold text-slate-900 group-hover:text-cyan-600 transition-colors text-xs">{{ $record->nombre_completo }}</span>
                                            <span class="text-[9px] text-slate-400 font-mono mt-0.5">CÉDULA: {{ $record->cedula_formatted }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col font-sans">
                                            <span class="text-xs font-semibold text-slate-700">{{ $record->empresaModel?->nombre ?? 'N/D' }}</span>
                                            <span class="text-[8px] text-slate-400 font-mono font-bold uppercase tracking-wider mt-0.5">{{ $record->responsable?->nombre ?? 'SYSTEM' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-sans">
                                        @php
                                            $hasAcuse = $record->evidenciasAfiliado->where('tipo_documento', 'acuse_recibo')->whereIn('status', ['recibido', 'validado'])->isNotEmpty() 
                                                     || ($record->estado && str_contains(strtolower($record->estado->nombre), 'acuse'));
                                            $isCompletedOrAcuse = ($record->estado_id == 9 || $hasAcuse);
                                        @endphp
                                        
                                        @if($isCompletedOrAcuse)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-emerald-700 bg-emerald-500/10 border border-emerald-500/20 shadow-2xs">
                                                <i class="ph-bold ph-check-circle text-emerald-600 text-xs"></i>
                                                {{ $hasAcuse ? 'ACUSE RECIBIDO' : 'COMPLETADO' }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                                                {{ $record->estado?->nombre ?? 'PENDIENTE' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full 
                                                {{ $record->firebase_sync_status === 'synced' ? 'bg-emerald-500 shadow-[0_0_8px_#10b981]' : 
                                                   ($record->firebase_sync_status === 'error' ? 'bg-rose-500 animate-pulse shadow-[0_0_8px_#ef4444]' : 'bg-amber-500 animate-pulse shadow-[0_0_8px_#f59e0b]') }}"></div>
                                            <span class="text-[9px] font-bold uppercase text-slate-500">
                                                {{ $record->firebase_sync_status === 'synced' ? 'IN CLOUD' : ($record->firebase_sync_status === 'error' ? 'WRITE ERROR' : 'OUT OF SYNC') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3 opacity-80 group-hover:opacity-100 transition-opacity">
                                            <div class="text-right mr-1 hidden sm:block">
                                                <p class="text-[9px] font-bold text-slate-800">{{ $record->updated_at->format('d/m H:i') }}</p>
                                                <p class="text-[8px] text-slate-400 mt-0.5">{{ $record->updated_at->diffForHumans() }}</p>
                                            </div>
                                            <button wire:click="openRecordDetail({{ $record->id }})" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-[#0891b2] hover:border-[#0891b2]/30 flex items-center justify-center transition-all shadow-2xs">
                                                <i class="ph-bold ph-magnifying-glass-plus text-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-6 bg-white/50 border-t border-slate-200">
                    {{ $records->links() }}
                </div>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- TAB: TELEMETRÍA (TIMELINE)                     -->
        <!-- ============================================== -->
        @if($activeTab === 'timeline')
            <div class="max-w-4xl mx-auto space-y-8">
                <div class="text-center space-y-1">
                    <h4 class="text-lg font-sans font-bold text-slate-900 tracking-tight">Registro de Telemetría Operativa</h4>
                    <p class="text-xs text-slate-500 font-mono">SECUENCIA DE EVENTOS CAPTURADOS POR EL MOTOR NEXUS</p>
                </div>

                <div class="relative space-y-6 before:absolute before:left-6 before:top-2 before:bottom-2 before:w-px before:bg-slate-200 font-mono">
                    @foreach($auditLogs as $audit)
                        <div class="relative flex items-start gap-6 group">
                            <!-- Timeline node -->
                            <div class="w-11 h-11 rounded-xl border border-slate-200 bg-white shadow-sm flex items-center justify-center shrink-0 z-10 
                                {{ str_contains($audit->event, 'pull') ? 'text-amber-500' : 'text-[#0891b2]' }} group-hover:scale-105 transition-transform duration-300">
                                <i class="ph-bold {{ str_contains($audit->event, 'pull') ? 'ph-download-simple' : 'ph-upload-simple' }} text-base"></i>
                            </div>
                            
                            <!-- Tactical Info -->
                            <div class="flex-1 glass-card p-5">
                                <div class="flex items-center justify-between mb-3.5 border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-500 uppercase font-mono">
                                            {{ substr($audit->user?->name ?? 'SY', 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 font-sans">{{ $audit->user?->name ?? 'Nexus Engine' }}</p>
                                            <p class="text-[8px] text-slate-400 uppercase mt-0.5 font-bold">EVENT: #{{ $audit->id }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[8px] text-[#0891b2] font-bold bg-[#0891b2]/5 px-2.5 py-0.5 rounded-full border border-[#0891b2]/10">{{ $audit->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <div class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 mb-3 text-xs text-slate-600">
                                    <span>Acción ejecutada: <strong class="text-slate-900 uppercase tracking-wide font-sans text-xs">{{ str_replace('_', ' ', $audit->event) }}</strong></span>
                                    <span class="mx-2 text-slate-300">|</span>
                                    <span>ID Local: <strong class="text-[#0891b2]">#{{ $audit->model_id }}</strong></span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach((array)($audit->new_values ?? []) as $key => $val)
                                        @break($loop->iteration > 4)
                                        @if(!is_array($val) && !in_array($key, ['updated_at', 'created_at', 'id']))
                                            <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 overflow-hidden shadow-2xs">
                                                <p class="text-[7px] text-slate-400 uppercase tracking-wider mb-0.5 font-mono">{{ $key }}</p>
                                                <p class="text-[9px] font-bold text-slate-700 truncate font-mono">{{ is_string($val) ? $val : json_encode($val) }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- TAB: CONFLICTOS (MANUAL GATE)                  -->
        <!-- ============================================== -->
        @if($activeTab === 'conflicts')
            <div class="space-y-6 max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-md font-sans font-bold text-rose-500 tracking-tight">Resolución Manual de Conflictos</h4>
                            <span class="px-2 py-0.5 bg-rose-500/10 text-rose-600 text-[8px] font-mono font-bold rounded-lg border border-rose-500/20 uppercase tracking-widest">GATEKEEPER</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Registros bloqueados temporalmente por colisiones de Checksum MD5.</p>
                    </div>
                </div>

                @if($totalConflicts == 0)
                    <div class="glass-card p-12 text-center flex flex-col items-center justify-center bg-emerald-500/[0.01] border-emerald-500/20">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-2xl flex items-center justify-center mb-4 border border-emerald-500/20 shadow-xs">
                            <i class="ph-bold ph-shield-check text-2xl"></i>
                        </div>
                        <h5 class="text-sm font-sans font-bold text-slate-900 tracking-tight">Consistencia Verificada</h5>
                        <p class="text-slate-500 mt-1 max-w-sm text-xs leading-relaxed">No se han detectado inconsistencias de checksum. Todos los datos están al 100% alineados.</p>
                    </div>
                @else
                    <div class="space-y-4 font-mono">
                        @foreach($conflicts as $record)
                            <div class="glass-card border-rose-200 bg-rose-500/[0.01] p-6 relative overflow-hidden group">
                                <div class="flex flex-col lg:flex-row justify-between gap-6">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="w-11 h-11 bg-rose-500/10 text-rose-500 rounded-xl flex items-center justify-center border border-rose-500/20">
                                                <i class="ph-bold ph-warning-diamond text-xl"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-sans font-bold text-slate-900 text-md">{{ $record->nombre_completo }}</h5>
                                                <div class="flex items-center gap-2 mt-1 text-[9px]">
                                                    <span class="font-bold text-rose-700 bg-rose-500/10 px-2.5 py-0.5 border border-rose-500/20 rounded-full">MD5 MISMATCH</span>
                                                    <span class="text-slate-400 font-bold">IDENTIFICADOR: {{ $record->cedula_formatted }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <!-- Local base version -->
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-2xs">
                                                <h6 class="text-[8px] font-bold text-slate-400 uppercase tracking-wider mb-3 font-mono">MASTER LOCAL (SAFESURE)</h6>
                                                <div class="space-y-1.5 text-[10px]">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-slate-400 font-bold">ESTADO REGISTRO:</span>
                                                        <span class="text-slate-800 font-extrabold">{{ $record->estado?->nombre }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-slate-400 font-bold">MUTACIÓN:</span>
                                                        <span class="text-slate-800 font-extrabold">{{ $record->updated_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Actions resolution option -->
                                            <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col justify-center shadow-2xs">
                                                <button wire:click="openRecordDetail({{ $record->id }})" class="w-full py-2.5 btn-premium text-xs rounded-xl flex items-center justify-center gap-2">
                                                    <i class="ph-bold ph-git-merge text-sm"></i>
                                                    <span>RESOLVER CONFLICTO</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Audit conflicts sidebar -->
                                    <div class="w-full lg:w-64 bg-slate-50 rounded-xl p-4 border border-slate-200 flex flex-col justify-between text-[10px] shadow-2xs">
                                        <div>
                                            <h6 class="text-[8px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5 mb-3 font-sans">
                                                <i class="ph-bold ph-shield-check text-xs"></i> AUDITORÍA NEXUS
                                            </h6>
                                            <p class="text-slate-500 leading-normal">Se interceptó mutación externa desde el webhook remoto. Resuelva manualmente arriba.</p>
                                        </div>
                                        <div class="mt-4 p-2 rounded bg-white border border-slate-200 shadow-2xs">
                                            <p class="text-[7px] text-slate-400 uppercase">CHECKSUM HASH MD5</p>
                                            <p class="text-[9px] text-[#0891b2] truncate font-bold mt-0.5">{{ $record->last_sync_hash ?: 'PENDING_HASH' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'traspasos')
            @livewire('traspasos-dashboard', ['view' => 'sync'])
        @endif
    </div>

    <!-- 7. FIXED FEEDBACK TOASTS -->
    <div class="fixed bottom-6 right-6 z-[100] space-y-3 font-mono text-xs">
        @if($polling)
            <div class="bg-white text-slate-800 border border-slate-200 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 animate-in slide-in-from-bottom-5 duration-300 backdrop-blur-md">
                <i class="ph-bold ph-arrows-clockwise text-base text-[#0891b2] animate-spin"></i>
                <div>
                    <p class="font-bold text-slate-900 font-sans text-xs">NEXUS PROCESSOR</p>
                    <p class="text-[8px] font-bold text-[#0891b2] uppercase tracking-wider mt-0.5">Sincronizando con Firebase...</p>
                </div>
            </div>
        @endif
        
        @if($isStalled)
            <div class="bg-rose-50 text-rose-800 border border-rose-200 px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 animate-in slide-in-from-bottom-5 duration-300 backdrop-blur-md">
                <i class="ph-bold ph-warning text-base text-rose-500 animate-bounce"></i>
                <div>
                    <p class="font-bold font-sans text-xs">PROCESO DETENIDO</p>
                    <button wire:click="forceReleaseLock" class="text-[8px] font-bold text-rose-600 underline uppercase tracking-wider mt-0.5">LIBERAR MEMORIA CACHÉ</button>
                </div>
            </div>
        @endif
    </div>

    <!-- 8. RECORD COMPREHENSIVE DETAIL INSPECTOR MODAL -->
    @if($showModal && $selectedRecordDetail)
        <div class="fixed inset-0 z-[200] flex items-center justify-center p-4 md:p-6">
            <div wire:click="closeRecordDetail" class="absolute inset-0 bg-slate-900/60 backdrop-blur-md animate-in fade-in duration-300"></div>
            
            <div class="relative w-full max-w-2xl bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300 flex flex-col max-h-[85vh] backdrop-blur-3xl">
                <!-- Modal Header -->
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#0891b2]/10 border border-[#0891b2]/20 flex items-center justify-center text-[#0891b2]">
                            <i class="ph-bold ph-user-focus text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-sans font-bold text-slate-900 leading-none">{{ $selectedRecordDetail->nombre_completo }}</h3>
                            <p class="text-[8px] font-mono text-slate-400 uppercase tracking-widest mt-1.5 font-bold">ID EXPEDIENTE: #{{ $selectedRecordDetail->id }}</p>
                        </div>
                    </div>
                    <button wire:click="closeRecordDetail" class="w-7 h-7 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 flex items-center justify-center text-slate-500 hover:text-rose-500 transition-colors">
                        <i class="ph-bold ph-x text-xs"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6 overflow-y-auto max-h-[55vh] custom-scroller bg-white text-slate-700">
                    @if($selectedRecordDetail->conflict_status)
                        <div class="mb-5 p-4 rounded-2xl bg-amber-500/5 border border-amber-500/20 text-amber-700 text-xs font-sans flex items-start gap-3">
                            <i class="ph-bold ph-warning text-lg animate-pulse mt-0.5"></i>
                            <div>
                                <h4 class="text-slate-900 font-bold">Colisión de Integridad Detectada</h4>
                                <p class="text-[10px] text-slate-500 mt-1">Elige cuál versión prevalecerá en los registros atómicos. Esta acción es definitiva.</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-mono text-xs">
                        @php $diffs = $this->getDiffFields(); @endphp
                        
                        <!-- LOCAL VERSION -->
                        <div class="border {{ in_array('nombre_completo', $diffs) || in_array('estado_id', $diffs) ? 'border-amber-500/30 bg-amber-500/[0.01]' : 'border-slate-200 bg-slate-50/50' }} rounded-2xl overflow-hidden transition-all shadow-2xs">
                            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200 flex items-center justify-between text-[8px] font-bold text-slate-400 uppercase">
                                <span>BASE LOCAL (SAFESURE)</span>
                                <span>{{ $selectedRecordDetail->updated_at->format('d/m/y H:i') }}</span>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="{{ in_array('cedula', $diffs) ? 'bg-amber-500/5 p-2 rounded' : '' }}">
                                    <p class="text-[7px] text-slate-400 uppercase">Cédula</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $selectedRecordDetail->cedula }}</p>
                                </div>
                                <div class="{{ in_array('nombre_completo', $diffs) ? 'bg-amber-500/5 p-2 rounded' : '' }}">
                                    <p class="text-[7px] text-slate-400 uppercase">Nombre Completo</p>
                                    <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $selectedRecordDetail->nombre_completo }}</p>
                                </div>
                                <div class="{{ in_array('estado_id', $diffs) ? 'bg-amber-500/5 p-2 rounded' : '' }}">
                                    <p class="text-[7px] text-slate-400 uppercase">Estado Operativo</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <p class="text-xs font-bold text-slate-800">{{ optional($selectedRecordDetail->estado)->nombre ?? 'N/A' }}</p>
                                        @php
                                            $modalHasAcuse = $selectedRecordDetail->evidenciasAfiliado->where('tipo_documento', 'acuse_recibo')->whereIn('status', ['recibido', 'validado'])->isNotEmpty();
                                        @endphp
                                        @if($modalHasAcuse)
                                            <span class="text-[8px] font-bold text-emerald-700 bg-emerald-500/10 px-1.5 py-0.2 rounded border border-emerald-500/20">CON ACUSE</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-slate-200">
                                    <button wire:click="forceSyncLocalToCloud" class="px-3 py-2 w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-lg text-[9px] font-bold transition-all uppercase" title="Imponer esta versión a la nube">
                                        Imponer Versión Local
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- CLOUD VERSION -->
                        <div class="border {{ !empty($diffs) ? 'border-[#0891b2]/30 bg-[#0891b2]/[0.01]' : 'border-slate-200 bg-slate-50/50' }} rounded-2xl overflow-hidden transition-all shadow-2xs">
                            <div class="bg-[#0891b2]/5 px-4 py-2 border-b border-[#0891b2]/10 flex items-center justify-between text-[8px] font-bold text-[#0891b2] uppercase">
                                <span>NUBE (FIREBASE)</span>
                                @if($firebaseRecordDetail && isset($firebaseRecordDetail['firebase_updated_at_meta']))
                                    <span>{{ \Carbon\Carbon::parse($firebaseRecordDetail['firebase_updated_at_meta'])->format('d/m/y H:i') }}</span>
                                @else
                                    <span class="text-slate-400 font-bold">NO DATA</span>
                                @endif
                            </div>
                            <div class="p-4 space-y-3">
                                @if($firebaseRecordDetail)
                                    <div class="{{ in_array('cedula', $diffs) ? 'bg-[#0891b2]/5 p-2 rounded' : '' }}">
                                        <p class="text-[7px] text-slate-400 uppercase">Cédula</p>
                                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $firebaseRecordDetail['cedula'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="{{ in_array('nombre_completo', $diffs) ? 'bg-[#0891b2]/5 p-2 rounded' : '' }}">
                                        <p class="text-[7px] text-slate-400 uppercase">Nombre Completo</p>
                                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $firebaseRecordDetail['nombre_completo'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="{{ in_array('estado_id', $diffs) ? 'bg-[#0891b2]/5 p-2 rounded' : '' }}">
                                        <p class="text-[7px] text-slate-400 uppercase">Estado ID (CMD)</p>
                                        <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $firebaseRecordDetail['estado_id'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="pt-3 border-t border-slate-200">
                                        <button wire:click="forceSyncCloudToLocal" class="px-3 py-2 w-full btn-premium text-xs rounded-lg text-[9px] uppercase font-bold transition-all" title="Bajar cambios y sobrescribir la base local">
                                            Restaurar Local con Nube
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-16 text-center opacity-30">
                                        <i class="ph-bold ph-cloud-slash text-3xl mb-2 text-slate-400"></i>
                                        <p class="text-[8px] font-bold uppercase tracking-widest text-slate-500">RECORD NOT IN CLOUD</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button wire:click="closeRecordDetail" class="px-4 py-2 btn-minimal rounded-xl text-[9px] font-mono font-bold uppercase">CERRAR VISUALIZADOR</button>
                </div>
            </div>
        </div>
    @endif
</div>
