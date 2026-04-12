<div @if($polling) wire:poll.1s="updateStatus" @endif class="dark sync-module bg-surface font-body text-on-surface selection:bg-primary/30 min-h-screen p-4 lg:p-8 overflow-hidden rounded-xl">
    
    <!-- Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <span class="text-lg sm:text-xl font-bold tracking-tighter text-cyan-400 font-headline uppercase">Sincronización</span>
            <div class="hidden sm:block h-4 w-[1px] bg-outline-variant/30"></div>
            <span class="font-headline tracking-tight text-xs sm:text-sm uppercase text-slate-400">Control Center</span>
        </div>
        <div class="flex items-center">
            <div id="connection-status" class="flex items-center bg-surface-container-lowest px-3 py-1.5 rounded-lg border border-outline-variant/10">
                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full mr-2 @if($polling) bg-tertiary-container shadow-[0_0_8px_rgba(73,237,114,0.5)] animate-pulse @else bg-tertiary-container @endif"></div>
                <span class="text-[9px] sm:text-[10px] font-mono text-tertiary-container uppercase tracking-tighter">System Online</span>
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Left Area (KPIs, Flow, Logs) -->
        <div class="lg:col-span-3 space-y-8">
            
            <!-- KPI Panel -->
            <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                <div class="bg-surface-container p-4 rounded-xl border border-outline-variant/5 hover:bg-surface-container-high transition-all border-l-2 border-l-primary-fixed-dim">
                    <p class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-1">Total Registros (Local)</p>
                    <div class="flex items-baseline justify-between mb-2">
                        <h3 class="text-2xl font-headline font-bold text-primary tracking-tight">
                            {{ number_format($totalLocales) }}
                        </h3>
                    </div>
                </div>

                <div class="bg-surface-container p-4 rounded-xl border border-outline-variant/5 hover:bg-surface-container-high transition-all">
                    <p class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-1">Empresas</p>
                    <h3 class="text-2xl font-headline font-bold text-on-surface-variant tracking-tight">
                        {{ number_format($totalEmpresas) }}
                    </h3>
                </div>

                <div class="bg-surface-container p-4 rounded-xl border border-outline-variant/5 hover:bg-surface-container-high transition-all">
                    <p class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-1">Afiliados</p>
                    <h3 class="text-2xl font-headline font-bold text-on-surface-variant tracking-tight">
                        {{ number_format($totalAfiliados) }}
                    </h3>
                </div>

                <div class="bg-surface-container p-4 rounded-xl border border-outline-variant/5">
                    <p class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-1">Estado de Sync</p>
                    @if($polling)
                        <h3 class="text-lg font-headline font-bold tracking-tight text-white mt-1 animate-pulse">Sincronizando</h3>
                        <p class="text-[9px] font-mono text-tertiary mt-1 uppercase">SINCRONIZACION DETECTADA (PULL)...</p>
                    @else
                        <h3 class="text-lg font-headline font-bold tracking-tight text-white mt-1">Esperando...</h3>
                        <p class="text-[9px] font-mono text-tertiary mt-1 uppercase">LISTO</p>
                    @endif
                </div>
                
                <div class="bg-surface-container-high p-4 rounded-xl border border-primary/10 shadow-[0_0_20px_rgba(0,218,243,0.05)]">
                    <p class="text-[10px] font-label uppercase tracking-widest text-primary-fixed-dim mb-1">Completado</p>
                    <h3 class="text-2xl font-mono font-medium text-on-surface tracking-tighter">{{ $progressPercentage }}%</h3>
                    <div class="w-full bg-surface-container-lowest h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-primary h-full rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </section>

            <!-- Centerpiece: Visual Data Flow -->
            <section class="glass-panel p-6 sm:p-8 rounded-2xl relative overflow-hidden min-h-[350px] lg:min-h-[300px] flex flex-col lg:flex-row items-center justify-between border border-outline-variant/10 gap-8 lg:gap-0">
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#00e5ff 0.5px, transparent 0.5px); background-size: 20px 20px;"></div>
                
                <!-- System Local -->
                <div class="relative z-10 flex flex-col items-center group order-3 lg:order-1">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-surface-container-lowest border border-outline-variant/20 flex items-center justify-center group-hover:border-primary/50 transition-colors shadow-2xl relative">
                        <span class="material-symbols-outlined text-3xl sm:text-4xl text-on-surface-variant group-hover:text-primary">database</span>
                    </div>
                    <div class="mt-3 sm:mt-4 text-center">
                        <p class="font-headline font-bold text-xs sm:text-sm uppercase tracking-widest">Local DB</p>
                        <p class="font-mono text-[9px] sm:text-[10px] mt-1 text-on-surface-variant">MySQL</p>
                    </div>
                </div>

                <!-- Flow Lines (Visible on Desktop) -->
                <div class="hidden lg:flex flex-1 flex flex-col items-center justify-center px-4 relative h-32 order-2">
                    <div class="absolute top-1/2 left-0 right-0 h-[2px] bg-surface-container-highest"></div>
                    <div class="absolute top-1/2 left-0 w-full h-[2px] data-flow-line transition-opacity @if($polling) opacity-80 animate-[slide_2s_linear_infinite] @else opacity-0 @endif"></div>
                    
                    @if($polling)
                    <div class="bg-surface-container-lowest/80 backdrop-blur border border-outline-variant/30 px-3 py-1 rounded-full relative z-20">
                        <span class="text-[9px] font-mono text-primary font-bold uppercase tracking-widest">Syncing...</span>
                    </div>
                    @endif
                </div>

                <!-- Flow Lines (Visible on Mobile) -->
                <div class="lg:hidden flex flex-col items-center justify-center relative h-16 w-1 order-2">
                    <div class="absolute top-0 bottom-0 left-1/2 w-[2px] bg-surface-container-highest -translate-x-1/2"></div>
                    @if($polling)
                    <div class="absolute top-0 w-[2px] bg-primary h-full animate-[slideDown_1.5s_linear_infinite] shadow-[0_0_8px_#00e5ff]"></div>
                    @endif
                </div>

                <style>
                    @keyframes slide { from { background-position-x: -200%; } to { background-position-x: 0%; } }
                    @keyframes slideDown { 0% { top: -100%; } 100% { top: 100%; } }
                </style>

                <!-- Firebase Hub -->
                <div class="relative z-10 flex flex-col items-center order-1 lg:order-3">
                    <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-surface-container-highest border-2 border-primary/20 flex items-center justify-center shadow-[0_0_50px_rgba(0,229,255,0.15)] relative">
                        @if($polling) <div class="absolute inset-0 rounded-full animate-ping border border-primary/30"></div> @endif
                        <span class="material-symbols-outlined text-4xl sm:text-5xl text-primary" style="font-variation-settings: 'FILL' 1;">hub</span>
                    </div>
                    <div class="mt-3 sm:mt-4 text-center">
                        <p class="font-headline font-bold text-sm sm:text-base uppercase tracking-tighter text-primary">Firebase</p>
                        <p class="font-mono text-[10px] sm:text-[11px] text-on-surface-variant">Cloud Firestore</p>
                    </div>
                </div>

            </section>

            <!-- Real-time Log Table -->
            <section class="bg-surface-container rounded-2xl overflow-hidden border border-outline-variant/10">
                <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low/50">
                    <h2 class="font-headline font-bold text-base uppercase tracking-tight">Bitácora de Sincronización</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-surface-container-low text-[10px] font-label uppercase tracking-widest text-on-surface-variant">
                            <tr>
                                <th class="px-6 py-4">Operación</th>
                                <th class="px-6 py-4 hidden sm:table-cell">Modo</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 hidden md:table-cell">Resumen</th>
                                <th class="px-6 py-4 text-right">Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/5">
                            @forelse($logs as $log)
                            <tr class="hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="material-symbols-outlined text-xs {{ str_contains(strtolower($log->type), 'pull') ? 'text-tertiary-fixed' : 'text-primary' }}">
                                            {{ str_contains(strtolower($log->type), 'pull') ? 'cloud_download' : 'cloud_upload' }}
                                        </span>
                                        <span class="text-[11px] font-bold">{{ ucfirst(explode(' ', $log->type)[0] ?? 'Pull') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-[10px] text-on-surface-variant hidden sm:table-cell">
                                    {{ str_contains(strtolower($log->type), 'incremental') ? 'Incremental' : 'Full' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase 
                                        {{ $log->status === 'completed' ? 'bg-tertiary/10 text-tertiary' : 
                                           ($log->status === 'failed' ? 'bg-error-container/20 text-error' : 'bg-primary/10 text-primary') }}">
                                        {{ $log->status === 'completed' ? 'OK' : ($log->status === 'failed' ? 'ERR' : 'SYNC') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[10px] text-on-surface-variant hidden md:table-cell">
                                    <span class="mr-2">Regs: <span class="text-white">{{ number_format($log->records_synced) }}</span></span>
                                </td>
                                <td class="px-6 py-4 font-mono text-[10px] text-on-surface-variant text-right">
                                    {{ $log->created_at->format('H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant text-[11px]">
                                    Sin registros recientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Right Sidebar: Activity & Controls -->
        <aside class="space-y-6">
            
            <!-- Action Controls -->
            <section class="bg-surface-container p-6 rounded-2xl border border-outline-variant/10 space-y-4">
                <h3 class="text-[10px] font-label uppercase tracking-widest text-on-surface-variant mb-4">Comandos</h3>

                <!-- Active Control Panel (Hidden unless running) -->
                @if($polling)
                <div class="space-x-2 w-full p-4 bg-surface-container-highest rounded-xl mb-4 border border-primary/20 flex flex-col gap-3">
                    <div class="flex gap-2 w-full">
                        <button class="flex-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-lg py-2 text-[10px] font-bold uppercase hover:bg-amber-500/20 transition-all flex items-center justify-center cursor-not-allowed opacity-50">
                            <span class="material-symbols-outlined text-[10px] mr-1">pause</span> Pausar
                        </button>
                        <button class="flex-1 bg-error/10 text-error border border-error/20 rounded-lg py-2 text-[10px] font-bold uppercase hover:bg-error/20 transition-all flex items-center justify-center" wire:click="cancelCurrentSync">
                            <span class="material-symbols-outlined text-[10px] mr-1">stop</span> Parar
                        </button>
                    </div>

                    @if($isStalled)
                    <div class="p-3 bg-error/20 border border-error/40 rounded-lg">
                        <p class="text-[9px] text-error-fixed font-bold leading-tight">⚠️ PROCESO ESTANCADO</p>
                        <p class="text-[8px] text-on-error-container mt-1 mb-2">No se detecta actividad hace >2m. El proceso pudo morir en el VPS.</p>
                        <button wire:click="forceReleaseLock" class="w-full bg-error text-white text-[8px] font-bold py-1.5 rounded uppercase hover:bg-red-600 transition-all">
                            Liberar Bloqueo Forzado
                        </button>
                    </div>
                    @endif
                </div>
                @endif
                
                @if(!$polling && \Illuminate\Support\Facades\Cache::has('firebase_sync_lock'))
                <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl mb-4">
                    <p class="text-[9px] text-amber-500 font-bold mb-2">🔒 SISTEMA BLOQUEADO (Lock Activo)</p>
                    <button wire:click="forceReleaseLock" class="w-full bg-amber-600 text-white text-[8px] font-bold py-1.5 rounded uppercase transition-all">
                        Limpiar Lock Manual
                    </button>
                </div>
                @endif

                <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="type" value="incremental">
                    <button type="submit" class="w-full flex items-center justify-between p-4 bg-surface-container-highest border border-outline-variant/20 rounded-xl hover:bg-primary/10 hover:border-primary/50 transition-all group">
                        <div class="flex items-center gap-3 text-left">
                            <span class="material-symbols-outlined text-tertiary-fixed group-hover:text-primary">cloud_download</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface">Sync Inteligente</p>
                                <p class="text-[9px] text-on-surface-variant mt-0.5">Firestore -> Local DB</p>
                            </div>
                        </div>
                    </button>
                </form>

                <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="type" value="push">
                    <button type="submit" class="w-full flex items-center justify-between p-4 bg-surface-container-highest border border-outline-variant/20 rounded-xl hover:bg-secondary/10 hover:border-secondary/50 transition-all group">
                        <div class="flex items-center gap-3 text-left">
                            <span class="material-symbols-outlined text-secondary group-hover:text-primary">cloud_upload</span>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface">Subir Local</p>
                                <p class="text-[9px] text-on-surface-variant mt-0.5">Local DB -> Firestore</p>
                            </div>
                        </div>
                    </button>
                </form>
                
                <div class="pt-2">
                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0" onsubmit="return confirm('¿Estás seguro? Consumirá mucha cuota de lectura en Firebase.');">
                        @csrf
                        <input type="hidden" name="type" value="full">
                        <button type="submit" class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-widest text-on-surface-variant hover:text-error transition-colors w-full justify-center">
                            <span class="material-symbols-outlined text-sm">warning</span> Forzar Full Sync
                        </button>
                    </form>
                </div>
            </section>

            <!-- Alerts Section -->
            <section class="bg-surface-container-high p-6 rounded-2xl border border-outline-variant/10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-label uppercase tracking-widest text-primary-fixed-dim">Status del Sistema</h3>
                    <span class="w-2 h-2 rounded-full bg-tertiary-fixed"></span>
                </div>
                <div class="space-y-3">
                    <div class="bg-surface-container-lowest p-3 rounded-xl border-l-2 border-tertiary-fixed">
                        <p class="text-[10px] font-bold text-on-surface">Queue Worker Operativo</p>
                        <p class="text-[9px] text-on-surface-variant mt-1">Los trabajos en segundo plano se están procesando correctamente.</p>
                    </div>
                </div>
            </section>

        </aside>
    </div>
</div>
