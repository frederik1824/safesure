<div @if($polling) wire:poll.1s="updateStatus" @endif class="sync-module bg-[#0b1120] font-sans text-slate-300 min-h-screen p-4 lg:p-10 overflow-hidden rounded-[48px] relative selection:bg-blue-500/30">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .font-sans { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bento-card {
            border-radius: 48px;
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.4) 0%, rgba(15, 23, 42, 0.6) 100%);
            border: 1px solid rgba(255, 255, 255, 0.03);
            box-shadow: 0 4px 24px -8px rgba(0, 0, 0, 0.5);
            transition: all 500ms cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .bento-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.7);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .bento-glow {
            position: absolute;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: -100px;
            right: -100px;
            opacity: 0;
            transition: opacity 500ms;
        }
        .bento-card:hover .bento-glow {
            opacity: 1;
        }
        /* Custom scrollbar for log */
        .log-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .log-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .log-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .log-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>

    <!-- Premium Backdrops -->
    <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-blue-600/5 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-600/5 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <!-- Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6 relative z-10 px-4">
        <div class="flex items-center space-x-5">
            <div class="w-14 h-14 bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="ph-bold ph-arrows-clockwise text-blue-400 text-2xl @if($polling) animate-spin @endif"></i>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white leading-none">Centro de Comando</h1>
                <p class="text-sm font-medium text-slate-400 mt-1">Sincronización de Base de Datos Institucional</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="flex items-center bg-slate-800/50 backdrop-blur-md px-5 py-2.5 rounded-2xl border border-slate-700 shadow-sm">
                <div class="relative flex h-2.5 w-2.5 mr-3">
                    @if($polling)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400 shadow-[0_0_10px_#34d399]"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-500"></span>
                    @endif
                </div>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-widest">{{ $polling ? 'En Línea' : 'En Espera' }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative z-10">
        
        <!-- Left Area (KPIs, Logs) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- HUD Operativo (KPIs) -->
            <section class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @php
                    $kpis = [
                        ['label' => 'Total Solicitudes', 'value' => $totalAfiliados, 'color' => 'text-blue-400', 'icon' => 'ph-users'],
                        ['label' => 'Empresas', 'value' => $totalEmpresas, 'color' => 'text-indigo-400', 'icon' => 'ph-buildings'],
                        ['label' => 'Nodos Locales', 'value' => $totalLocales, 'color' => 'text-emerald-400', 'icon' => 'ph-hard-drives'],
                    ];
                @endphp

                @foreach($kpis as $kpi)
                <div class="bento-card p-6 group">
                    <div class="bento-glow"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700 flex items-center justify-center">
                            <i class="ph-bold {{ $kpi['icon'] }} text-xl {{ $kpi['color'] }}"></i>
                        </div>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-extrabold text-white tracking-tight">
                            {{ number_format($kpi['value']) }}
                        </h3>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mt-2">{{ $kpi['label'] }}</p>
                    </div>
                </div>
                @endforeach

                <!-- Eficiencia Global / Progress -->
                <div class="bento-card p-6 group flex flex-col justify-between">
                    <div class="bento-glow"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-1">
                            <i class="ph-bold ph-trend-up text-blue-400 text-xl"></i>
                            <span class="text-2xl font-extrabold text-white">{{ $progressPercentage }}%</span>
                        </div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-4">Progreso Global</p>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden relative z-10">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-400 h-full rounded-full transition-all duration-700 ease-out" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </section>

            <!-- Real-time Log Table -->
            <section class="bento-card flex flex-col min-h-[400px]">
                <div class="px-8 py-6 border-b border-white/5 flex justify-between items-center bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <i class="ph-bold ph-terminal-window text-blue-400 text-lg"></i>
                        <h2 class="font-bold text-[13px] uppercase tracking-widest text-white">Registro de Operaciones</h2>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Bitácora en Vivo</span>
                </div>
                
                <div class="overflow-x-auto log-scroll flex-1 p-2">
                    <table class="w-full text-left">
                        <thead class="text-[10px] font-bold uppercase tracking-widest text-slate-500 border-b border-white/5">
                            <tr>
                                <th class="px-6 py-4">ID de Paquete</th>
                                <th class="px-6 py-4 hidden sm:table-cell">Protocolo</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4 hidden md:table-cell">Volumen</th>
                                <th class="px-6 py-4 text-right">Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            @forelse($logs as $log)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center">
                                            <i class="ph-bold {{ str_contains(strtolower($log->type), 'pull') ? 'ph-download-simple text-amber-400' : 'ph-upload-simple text-blue-400' }} text-[14px]"></i>
                                        </div>
                                        <span class="text-[13px] font-bold text-slate-200">#{{ $log->id }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[12px] font-medium text-slate-400 hidden sm:table-cell">
                                    {{ str_contains(strtolower($log->type), 'incremental') ? 'Sincronización Delta' : 'Carga Completa' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[10px] px-3 py-1.5 rounded-lg font-bold uppercase tracking-wider 
                                        {{ $log->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 
                                           ($log->status === 'failed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20') }}">
                                        {{ $log->status === 'completed' ? 'Exitoso' : ($log->status === 'failed' ? 'Error' : 'En Proceso') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[12px] font-bold text-slate-300 hidden md:table-cell">
                                    {{ number_format($log->records_synced) }} <span class="text-[10px] font-normal text-slate-500">regs</span>
                                </td>
                                <td class="px-6 py-4 text-[12px] font-medium text-slate-500 text-right">
                                    {{ $log->created_at->format('H:i:s') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-500 text-[12px] italic uppercase tracking-widest">
                                    Sin registros de operación recientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Right Sidebar: Terminal Controls ⌨️ -->
        <aside class="lg:col-span-4 space-y-6">
                <!-- Control Panel -->
            <section class="bento-card p-8">
                <div class="bento-glow"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <h3 class="text-[12px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-2">
                        <i class="ph-bold ph-faders"></i> Panel de Control
                    </h3>
                </div>

                <div class="space-y-4 relative z-10">
                    
                    @if($polling)
                    <div class="bg-slate-800/50 border border-slate-700 rounded-[24px] p-5 mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                                <i class="ph-bold ph-spinner-gap text-blue-400 animate-spin"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-white">Sincronizando</p>
                                <p class="text-xs text-slate-400">Proceso en segundo plano activo</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button wire:click="cancelCurrentSync" class="flex-1 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl py-3 text-[11px] font-bold uppercase tracking-wider hover:bg-rose-500/20 transition-all flex items-center justify-center gap-2">
                                <i class="ph-bold ph-stop"></i> Abortar
                            </button>
                        </div>
                        
                        @if($isStalled)
                        <div class="mt-4 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl">
                            <p class="text-xs text-rose-400 font-bold mb-2 flex items-center gap-2"><i class="ph-bold ph-warning"></i> Proceso Estancado</p>
                            <button wire:click="forceReleaseLock" class="w-full bg-rose-600 text-white text-[11px] font-bold py-2 rounded-lg uppercase tracking-wider hover:bg-rose-500 transition-all">
                                Forzar Desbloqueo
                            </button>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if(!$polling && \Illuminate\Support\Facades\Cache::has('firebase_sync_lock'))
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-[24px] p-5 mb-6">
                        <p class="text-xs text-amber-500 font-bold mb-3 flex items-center gap-2"><i class="ph-bold ph-lock-key"></i> Sistema Bloqueado</p>
                        <button wire:click="forceReleaseLock" class="w-full bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[11px] font-bold py-2.5 rounded-xl uppercase tracking-wider hover:bg-amber-500/30 transition-all">
                            Restablecer Seguro
                        </button>
                    </div>
                    @endif

                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="type" value="incremental">
                        <button type="submit" class="w-full p-5 bg-slate-800/40 border border-slate-700/50 rounded-[24px] flex items-center hover:bg-slate-700/60 hover:border-slate-600 transition-all group">
                            <div class="w-12 h-12 rounded-[16px] bg-slate-700/50 flex items-center justify-center text-slate-400 group-hover:text-blue-400 transition-colors mr-4 shrink-0">
                                <i class="ph-bold ph-cloud-arrow-down text-2xl"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">Sincronización Delta</p>
                                <p class="text-xs text-slate-400 mt-0.5">Firebase → Base Local</p>
                            </div>
                        </button>
                    </form>

                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="type" value="push">
                        <button type="submit" class="w-full p-5 bg-slate-800/40 border border-slate-700/50 rounded-[24px] flex items-center hover:bg-slate-700/60 hover:border-slate-600 transition-all group">
                            <div class="w-12 h-12 rounded-[16px] bg-slate-700/50 flex items-center justify-center text-slate-400 group-hover:text-indigo-400 transition-colors mr-4 shrink-0">
                                <i class="ph-bold ph-cloud-arrow-up text-2xl"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">Push Institucional</p>
                                <p class="text-xs text-slate-400 mt-0.5">Base Local → Firebase</p>
                            </div>
                        </button>
                    </form>

                    <div class="pt-6 mt-6 border-t border-slate-800 relative z-10">
                        <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0" onsubmit="return confirm('¿Confirma que desea realizar una sincronización completa? Esto puede demorar.');">
                            @csrf
                            <input type="hidden" name="type" value="full">
                            <button type="submit" class="flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-widest text-slate-500 hover:text-rose-400 transition-colors w-full p-3 rounded-xl hover:bg-rose-500/10">
                                <i class="ph-bold ph-warning"></i> Sincronización Forzada Completa
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Webhooks Feed (CMD Interactions) -->
            <section class="bento-card p-8">
                <div class="bento-glow"></div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <h3 class="text-[12px] font-bold uppercase tracking-widest text-slate-400 flex items-center gap-2">
                        <i class="ph-bold ph-broadcast"></i> Monitoreo Webhooks
                    </h3>
                    <div class="flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                    </div>
                </div>

                <div class="space-y-4 relative z-10">
                    @forelse($webhooks as $wh)
                        <div class="p-4 rounded-2xl bg-slate-800/30 border border-white/5 hover:border-blue-500/30 transition-all group">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black uppercase tracking-widest {{ $wh->event_type === 'afiliado' ? 'text-blue-400' : 'text-indigo-400' }}">
                                    {{ $wh->event_type }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-500">{{ $wh->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[11px] text-slate-300 font-medium line-clamp-1 group-hover:line-clamp-none transition-all">
                                {{ $wh->message }}
                            </p>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $wh->status === 'processed' ? 'bg-emerald-500' : ($wh->status === 'failed' ? 'bg-rose-500' : 'bg-blue-500 animate-pulse') }}"></div>
                                <span class="text-[9px] font-bold uppercase tracking-tighter text-slate-500">{{ $wh->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <i class="ph-bold ph-intersect text-slate-700 text-3xl mb-2"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-600">Sin señales externas</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
