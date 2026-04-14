<div @if($polling) wire:poll.1s="updateStatus" @endif class="dark sync-module bg-[#05070a] font-body text-slate-200 selection:bg-cyan-500/30 min-h-screen p-4 lg:p-10 overflow-hidden rounded-3xl relative">
    
    <!-- Premium Backdrops -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[120px] -z-10 animate-pulse"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-600/10 rounded-full blur-[100px] -z-10"></div>

    <!-- Header -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-12 gap-6 relative z-10">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(6,182,212,0.3)]">
                <span class="material-symbols-outlined text-white text-2xl animate-spin-slow">sync_alt</span>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white font-headline uppercase leading-none">Sincronización</h1>
                <p class="font-mono tracking-widest text-[10px] uppercase text-cyan-400/60 mt-2">Quantum Data Link v4.0</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div id="connection-status" class="flex items-center bg-white/5 backdrop-blur-md px-5 py-2.5 rounded-2xl border border-white/10 shadow-2xl">
                <div class="relative flex h-2.5 w-2.5 mr-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-cyan-400 shadow-[0_0_15px_#22d3ee]"></span>
                </div>
                <span class="text-[11px] font-mono text-cyan-400 font-bold uppercase tracking-[0.2em]">Signal: Verified</span>
            </div>
        </div>
    </header>

    <!-- Main Content Canvas -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 relative z-10">
        
        <!-- Left Area (KPIs, Flow, Logs) -->
        <div class="lg:col-span-3 space-y-10">
            
            <!-- KPI Panel -->
            <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @php
                    $kpis = [
                        ['label' => 'Total VPS', 'value' => $totalLocales, 'color' => 'text-cyan-400', 'icon' => 'inventory_2'],
                        ['label' => 'Empresas', 'value' => $totalEmpresas, 'color' => 'text-blue-400', 'icon' => 'business'],
                        ['label' => 'Afiliados', 'value' => $totalAfiliados, 'color' => 'text-indigo-400', 'icon' => 'group'],
                    ];
                @endphp

                @foreach($kpis as $kpi)
                <div class="bg-white/[0.03] backdrop-blur-xl p-5 rounded-2xl border border-white/5 hover:bg-white/[0.06] transition-all hover:translate-y-[-2px] group">
                    <div class="flex justify-between items-start mb-3">
                        <span class="material-symbols-outlined text-lg {{ $kpi['color'] }} opacity-50">{{ $kpi['icon'] }}</span>
                        <div class="w-1 h-1 rounded-full bg-white/20"></div>
                    </div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">{{ $kpi['label'] }}</p>
                    <h3 class="text-2xl font-mono font-bold text-white tracking-tighter">
                        {{ number_format($kpi['value']) }}
                    </h3>
                </div>
                @endforeach

                <div class="bg-white/[0.03] backdrop-blur-xl p-5 rounded-2xl border border-white/5">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Status Link</p>
                    @if($polling)
                        <h3 class="text-lg font-bold tracking-tight text-white mt-1 flex items-center gap-2">
                             <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                             ACTIVE
                        </h3>
                        <p class="text-[9px] font-mono text-cyan-400/60 mt-1 uppercase">Sincronización en curso...</p>
                    @else
                        <h3 class="text-lg font-bold tracking-tight text-slate-400 mt-1 uppercase">Standby</h3>
                        <p class="text-[9px] font-mono text-slate-600 mt-1 uppercase">Esperando señal</p>
                    @endif
                </div>
                
                <div class="bg-gradient-to-br from-cyan-500/20 to-blue-600/20 backdrop-blur-2xl p-5 rounded-2xl border border-cyan-500/20 shadow-[0_20px_40px_-15px_rgba(6,182,212,0.2)]">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1">Compleción</p>
                    <h3 class="text-2xl font-mono font-bold text-white tracking-tighter">{{ $progressPercentage }}%</h3>
                    <div class="w-full bg-black/40 h-1.5 rounded-full mt-3 overflow-hidden border border-white/5">
                        <div class="bg-gradient-to-r from-cyan-400 to-blue-500 h-full rounded-full transition-all duration-700 ease-out shadow-[0_0_10px_#22d3ee]" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                </div>
            </section>

            <!-- Centerpiece: The Animated Data Core ⚛️ -->
            <section class="bg-white/[0.02] backdrop-blur-sm p-12 rounded-[2.5rem] relative overflow-hidden flex flex-col lg:flex-row items-center justify-between border border-white/[0.05] min-h-[400px]">
                
                <!-- Circuit Board Pattern Overlay -->
                <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#22d3ee 0.8px, transparent 0.8px); background-size: 32px 32px;"></div>
                
                <!-- Local Core -->
                <div class="relative z-10 flex flex-col items-center group order-3 lg:order-1">
                    <div class="w-24 h-24 rounded-3xl bg-black/40 border-2 border-white/5 flex items-center justify-center group-hover:border-cyan-500/50 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <span class="material-symbols-outlined text-4xl text-slate-500 group-hover:text-cyan-400 transition-colors">database</span>
                    </div>
                    <div class="mt-6 text-center">
                        <p class="font-headline font-black text-[13px] uppercase tracking-[0.3em] text-white">Local Node</p>
                        <p class="font-mono text-[10px] mt-2 text-slate-500 uppercase tracking-widest">Storage Array ALPHA</p>
                    </div>
                </div>

                <!-- Dynamic Data Streams 🌊 -->
                <div class="flex-1 flex flex-col items-center justify-center px-10 relative order-2 min-h-[100px] lg:min-h-0 w-full lg:w-auto">
                    <!-- Base Track -->
                    <div class="hidden lg:block w-3/4 h-[1px] bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
                    
                    @if($polling)
                        <!-- Animated Neon Pulses -->
                        <div class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none">
                            <div class="w-full h-[2px] bg-transparent relative overflow-hidden">
                                <div class="absolute top-0 h-full w-20 bg-gradient-to-r from-transparent via-cyan-400 to-transparent animate-data-stream-right"></div>
                                <div class="absolute top-0 h-full w-32 bg-gradient-to-r from-transparent via-blue-500/50 to-transparent animate-data-stream-right-slow delay-700"></div>
                            </div>
                        </div>
                        
                        <div class="mt-4 bg-cyan-500/10 backdrop-blur-xl border border-cyan-500/20 px-5 py-1.5 rounded-full relative z-20">
                            <span class="text-[10px] font-mono text-cyan-400 font-black uppercase tracking-[0.4em] animate-pulse">Syncing Streams</span>
                        </div>
                    @else
                         <div class="flex flex-col items-center opacity-30">
                            <span class="material-symbols-outlined text-slate-600 text-3xl">cloud_off</span>
                            <span class="text-[9px] font-mono text-slate-500 mt-2 uppercase tracking-widest">No Active Stream</span>
                         </div>
                    @endif
                </div>

                <!-- Central Data Core (Firebase Hub) ⚛️ -->
                <div class="relative z-10 flex flex-col items-center order-1 lg:order-3">
                    <div class="relative w-40 h-40 flex items-center justify-center">
                        <!-- Orbiting Rings -->
                        <div class="absolute inset-0 rounded-full border border-cyan-500/10 animate-spin-slow"></div>
                        <div class="absolute inset-4 rounded-full border border-blue-500/20 animate-spin-reverse-slow"></div>
                        <div class="absolute inset-8 rounded-full border border-white/5"></div>
                        
                        <!-- Core Sphere -->
                        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-black to-slate-900 border-2 border-cyan-500/30 flex items-center justify-center shadow-[0_0_60px_rgba(6,182,212,0.2)] relative overflow-hidden group">
                            @if($polling) 
                                <div class="absolute inset-0 bg-cyan-400/20 animate-pulse-fast"></div>
                                <div class="absolute inset-0 rounded-full animate-ping-slow border border-cyan-400/40"></div>
                            @endif
                            <span class="material-symbols-outlined text-5xl text-cyan-400 relative z-10 group-hover:scale-110 transition-transform duration-500" style="font-variation-settings: 'FILL' 1;">hub</span>
                            
                            <!-- Internal Glow -->
                            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-tr from-cyan-500/10 via-transparent to-transparent"></div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <p class="font-headline font-black text-base uppercase tracking-[0.4em] text-cyan-400 shadow-cyan-400/20 drop-shadow-lg">Firebase</p>
                        <p class="font-mono text-[11px] text-slate-500 mt-2 uppercase tracking-widest">Global Master Cluster</p>
                    </div>
                </div>

            </section>

            <!-- Real-time Log Table (Glass Style) -->
            <section class="bg-white/[0.02] backdrop-blur-xl rounded-3xl overflow-hidden border border-white/5">
                <div class="px-8 py-6 border-b border-white/5 flex justify-between items-center bg-white/[0.01]">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-cyan-500 shadow-[0_0_8px_#22d3ee]"></span>
                        <h2 class="font-headline font-black text-[13px] uppercase tracking-[0.2em] text-white">Quantum Log</h2>
                    </div>
                    <span class="font-mono text-[10px] text-slate-500 uppercase">Live Buffer Stream</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 font-mono">
                            <tr>
                                <th class="px-8 py-5">Packet ID</th>
                                <th class="px-8 py-5 hidden sm:table-cell">Protocol</th>
                                <th class="px-8 py-5">Result</th>
                                <th class="px-8 py-5 hidden md:table-cell">Throughput</th>
                                <th class="px-8 py-5 text-right">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.03]">
                            @forelse($logs as $log)
                            <tr class="hover:bg-cyan-500/[0.02] transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-lg bg-black/40 border border-white/5 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[14px] {{ str_contains(strtolower($log->type), 'pull') ? 'text-amber-400' : 'text-cyan-400' }}">
                                                {{ str_contains(strtolower($log->type), 'pull') ? 'database_download' : 'database_upload' }}
                                            </span>
                                        </div>
                                        <span class="text-[12px] font-bold font-mono tracking-tighter text-slate-200">#{{ $log->id }} {{ ucfirst(explode(' ', $log->type)[0]) }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 font-mono text-[10px] text-slate-500 hidden sm:table-cell uppercase tracking-wider">
                                    {{ str_contains(strtolower($log->type), 'incremental') ? 'Delta Sync' : 'Full Overwrite' }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-[9px] px-3 py-1 rounded-md font-black uppercase tracking-widest 
                                        {{ $log->status === 'completed' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 
                                           ($log->status === 'failed' ? 'bg-red-500/10 text-red-500 border border-red-500/20' : 'bg-blue-500/10 text-blue-400 border border-blue-500/20') }}">
                                        {{ $log->status === 'completed' ? 'Success' : ($log->status === 'failed' ? 'Critical' : 'Syncing') }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-[11px] font-mono text-slate-400 hidden md:table-cell">
                                    <span class="opacity-50">Δ</span> <span class="text-white font-bold">{{ number_format($log->records_synced) }}</span> <span class="text-[9px] opacity-40 lowercase">records</span>
                                </td>
                                <td class="px-8 py-5 font-mono text-[11px] text-slate-500 text-right">
                                    {{ $log->created_at->format('H:i:s') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center text-slate-600 font-mono text-[12px] italic uppercase tracking-widest">
                                    Data Buffer Empty
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Right Sidebar: Terminal Controls ⌨️ -->
        <aside class="space-y-8">
            
            <!-- Matrix Control Panel -->
            <section class="bg-white/[0.03] backdrop-blur-2xl p-8 rounded-[2rem] border border-white/5 space-y-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-500/5 blur-3xl rounded-full"></div>
                
                <h3 class="text-[11px] font-mono font-black uppercase tracking-[0.3em] text-slate-500 flex items-center justify-between">
                    Controls
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>
                </h3>

                <!-- Active Control Panel -->
                @if($polling)
                <div class="space-y-3 w-full p-5 bg-black/40 rounded-3xl border border-cyan-500/20 mb-6 group">
                    <div class="flex gap-3">
                         <button class="flex-1 bg-white/[0.03] text-slate-600 border border-white/5 rounded-2xl py-3 text-[10px] font-black uppercase tracking-widest cursor-not-allowed flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px] mr-2">pause</span> Pause
                        </button>
                        <button class="flex-1 bg-red-500/10 text-red-500 border border-red-500/20 rounded-2xl py-3 text-[10px] font-black uppercase tracking-widest hover:bg-red-500/20 transition-all flex items-center justify-center shadow-[0_0_20px_rgba(239,68,68,0.1)]" wire:click="cancelCurrentSync">
                            <span class="material-symbols-outlined text-[16px] mr-2">stop</span> Abort
                        </button>
                    </div>

                    @if($isStalled)
                    <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-2xl animate-shake">
                        <p class="text-[10px] text-red-400 font-black leading-tight uppercase tracking-widest flex items-center gap-2">
                             <span class="material-symbols-outlined text-sm">warning</span> Core Stalled
                        </p>
                        <p class="text-[9px] text-slate-500 mt-2 mb-4 lowercase italic leading-relaxed">System heartbeat lost. Node unreachable on VPS.</p>
                        <button wire:click="forceReleaseLock" class="w-full bg-red-600 text-white text-[10px] font-black py-2.5 rounded-xl uppercase tracking-widest hover:bg-red-500 transition-all shadow-lg shadow-red-600/20">
                            Emergency Unlock
                        </button>
                    </div>
                    @endif
                </div>
                @endif
                
                @if(!$polling && \Illuminate\Support\Facades\Cache::has('firebase_sync_lock'))
                <div class="p-5 bg-amber-500/5 border border-amber-500/20 rounded-3xl mb-6">
                    <p class="text-[10px] text-amber-500 font-black uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">lock</span> Core Restricted
                    </p>
                    <button wire:click="forceReleaseLock" class="w-full bg-amber-600/20 text-amber-500 border border-amber-500/30 text-[10px] font-black py-3 rounded-2xl uppercase tracking-widest hover:bg-amber-600/30 transition-all">
                        Reset Data Lock
                    </button>
                </div>
                @endif

                <div class="space-y-4">
                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="type" value="incremental">
                        <button type="submit" class="w-full h-20 bg-black/40 border border-white/5 rounded-3xl flex items-center px-6 hover:bg-cyan-500/10 hover:border-cyan-500/30 transition-all group overflow-hidden relative">
                            <div class="absolute inset-y-0 left-0 w-1 bg-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-cyan-400 transition-colors mr-5">
                                <span class="material-symbols-outlined text-xl">rocket_launch</span>
                            </div>
                            <div class="text-left">
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-white">Delta Pull</p>
                                <p class="text-[9px] font-mono text-slate-500 mt-1 uppercase tracking-widest">Firestore → Local</p>
                            </div>
                        </button>
                    </form>

                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="type" value="push">
                        <button type="submit" class="w-full h-20 bg-black/40 border border-white/5 rounded-3xl flex items-center px-6 hover:bg-indigo-500/10 hover:border-indigo-500/30 transition-all group overflow-hidden relative">
                            <div class="absolute inset-y-0 left-0 w-1 bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-indigo-400 transition-colors mr-5">
                                <span class="material-symbols-outlined text-xl">upload</span>
                            </div>
                            <div class="text-left">
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-white">Cloud Push</p>
                                <p class="text-[9px] font-mono text-slate-500 mt-1 uppercase tracking-widest">Local → Firestore</p>
                            </div>
                        </button>
                    </form>
                </div>
                
                <div class="pt-4 px-2">
                    <form action="{{ route('admin.sync.trigger') }}" method="POST" class="m-0" onsubmit="return confirm('¿Estás seguro? Consumirá mucha cuota de lectura en Firebase.');">
                        @csrf
                        <input type="hidden" name="type" value="full">
                        <button type="submit" class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-slate-600 hover:text-red-400 transition-colors w-full justify-center group">
                            <span class="material-symbols-outlined text-sm group-hover:animate-pulse">bolt</span> Full System Wipe & Sync
                        </button>
                    </form>
                </div>
            </section>

            <!-- Diagnostics -->
            <section class="bg-black/40 p-8 rounded-[2rem] border border-white/5 relative overflow-hidden group">
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-cyan-500/5 blur-3xl rounded-full group-hover:bg-cyan-500/10 transition-colors"></div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-[11px] font-mono font-black uppercase tracking-[0.3em] text-slate-500">System State</h3>
                    <div class="flex gap-1">
                        <span class="w-1 h-1 rounded-full bg-cyan-500 animate-pulse"></span>
                        <span class="w-1 h-1 rounded-full bg-cyan-500 animate-pulse delay-75"></span>
                        <span class="w-1 h-1 rounded-full bg-cyan-500 animate-pulse delay-150"></span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-white/[0.03] p-5 rounded-2xl border-l-[3px] border-cyan-500/50">
                        <p class="text-[11px] font-bold text-white uppercase tracking-wider mb-1">Worker: ONLINE</p>
                        <p class="text-[10px] text-slate-500 leading-relaxed font-mono">Job queue processing on thread #04. Latency: < 50ms.</p>
                    </div>
                </div>
            </section>

        </aside>
    </div>

    <!-- Styles for Animations -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Outfit:wght@400;700;900&display=swap');

        .sync-module {
            font-family: 'Inter', sans-serif;
        }
        .font-headline {
            font-family: 'Outfit', sans-serif !important;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace !important;
        }

        @keyframes data-stream-right {
            0% { transform: translateX(-200%); }
            100% { transform: translateX(500%); }
        }
        @keyframes data-stream-right-slow {
            0% { transform: translateX(-300%); }
            100% { transform: translateX(600%); }
        }
        .animate-data-stream-right { animation: data-stream-right 2s linear infinite; }
        .animate-data-stream-right-slow { animation: data-stream-right-slow 4s linear infinite; }
        
        .animate-spin-slow { animation: spin 12s linear infinite; }
        .animate-spin-reverse-slow { animation: spin-reverse 8s linear infinite; }
        .animate-ping-slow { animation: ping 3s cubic-bezier(0, 0, 0.2, 1) infinite; }
        .animate-pulse-fast { animation: pulse 0.8s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes spin-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }
        .animate-shake { animation: shake 0.5s ease-in-out infinite; }

        .delay-700 { animation-delay: 700ms; }
    </style>
</div>
