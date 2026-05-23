@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-8 animate-fade-in pb-20">
    {{-- Header & Filters --}}
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div>
                <nav class="flex items-center gap-2 mb-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                    <span class="text-primary/60">Monitoreo Operativo</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-primary">Supervisión Ejecutiva</span>
                </nav>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Supervisión Ejecutiva</h2>
                <p class="text-slate-400 text-sm font-medium mt-1.5">Métricas estratégicas y control del flujo de carnetización en tiempo real.</p>
            </div>
            
            <form method="GET" action="{{ route('reportes.supervision') }}" class="flex flex-wrap items-center gap-3 bg-slate-50 p-2.5 rounded-[2rem] border border-slate-100 w-full lg:w-auto">
                <div class="flex items-center gap-2 px-3 border-r border-slate-200/60">
                    <span class="material-symbols-outlined text-slate-400 text-sm">calendar_month</span>
                    <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 p-1 w-28 outline-none">
                    <span class="text-slate-300">/</span>
                    <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 p-1 w-28 outline-none">
                </div>

                <select name="corte_id" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 px-3 cursor-pointer outline-none">
                    <option value="">Cortes: Todos</option>
                    @foreach($cortes as $c)
                        <option value="{{ $c->id }}" {{ $corte_id == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>

                <select name="responsable_id" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 px-3 cursor-pointer outline-none border-l border-slate-200/60">
                    <option value="">Responsables</option>
                    @foreach($responsables as $r)
                        <option value="{{ $r->id }}" {{ $responsable_id == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-slate-900 text-white p-2.5 rounded-xl hover:bg-primary transition-all shadow-md shadow-slate-900/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg">filter_alt</span>
                </button>

                <a href="{{ route('reportes.export', request()->all()) }}" class="bg-white text-slate-600 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all flex items-center justify-center shrink-0" title="Exportar Reporte">
                    <span class="material-symbols-outlined text-lg">download</span>
                </a>
            </form>
        </div>
    </div>

    {{-- Interactive Logistics Funnel (Highlight Section) --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100/80 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl opacity-40 group-hover:opacity-60 transition-opacity duration-700"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl opacity-40 group-hover:opacity-60 transition-opacity duration-700"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter pl-1 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Embudo de Conversión Operativa
                    </h3>
                    <p class="text-slate-400 text-xs mt-1">Monitoreo de expedientes activos distribuidos por etapa del pipeline.</p>
                </div>
                <div class="px-3.5 py-1.5 bg-slate-50 border border-slate-200/60 rounded-xl text-[0.65rem] font-mono font-bold tracking-widest text-slate-600 uppercase shadow-inner flex items-center gap-1.5 self-start sm:self-auto">
                    <span class="material-symbols-outlined text-xs">all_inbox</span>
                    {{ number_format(array_sum($funnel_stats)) }} Totales
                </div>
            </div>

            {{-- Funnel Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Funnel Stage 1: Ingreso --}}
                <div class="bg-slate-50/60 p-6 rounded-3xl border border-slate-100 relative overflow-hidden hover:bg-slate-100/50 transition-all">
                    <div class="absolute -right-4 -bottom-4 text-slate-200/20 text-[5rem] font-black pointer-events-none select-none">01</div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1 font-headline">Paso 1: Ingreso</p>
                    <h4 class="text-sm font-black text-slate-700 uppercase tracking-tight mb-4">Registro Inicial</h4>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($funnel_stats['ingreso']) }}</div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-200/40 text-[0.65rem] font-bold text-slate-500 font-headline">
                        <span>Sin Procesar</span>
                        <span class="text-slate-700 font-extrabold">{{ array_sum($funnel_stats) > 0 ? round(($funnel_stats['ingreso'] / array_sum($funnel_stats)) * 100) : 0 }}%</span>
                    </div>
                </div>

                {{-- Funnel Stage 2: Impresión/Proceso --}}
                <div class="bg-slate-50/60 p-6 rounded-3xl border border-slate-100 relative overflow-hidden hover:bg-slate-100/50 transition-all">
                    <div class="absolute -right-4 -bottom-4 text-slate-200/20 text-[5rem] font-black pointer-events-none select-none">02</div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1 font-headline">Paso 2: Proceso</p>
                    <h4 class="text-sm font-black text-slate-700 uppercase tracking-tight mb-4">Impresión y Validación</h4>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($funnel_stats['proceso']) }}</div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-200/40 text-[0.65rem] font-bold text-slate-500 font-headline">
                        <span>En Producción</span>
                        <span class="text-slate-700 font-extrabold">{{ array_sum($funnel_stats) > 0 ? round(($funnel_stats['proceso'] / array_sum($funnel_stats)) * 100) : 0 }}%</span>
                    </div>
                </div>

                {{-- Funnel Stage 3: Tránsito --}}
                <div class="bg-slate-50/60 p-6 rounded-3xl border border-slate-100 relative overflow-hidden hover:bg-slate-100/50 transition-all">
                    <div class="absolute -right-4 -bottom-4 text-slate-200/20 text-[5rem] font-black pointer-events-none select-none">03</div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1 font-headline">Paso 3: Tránsito</p>
                    <h4 class="text-sm font-black text-slate-700 uppercase tracking-tight mb-4">Asignado a Courier</h4>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($funnel_stats['transito']) }}</div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-200/40 text-[0.65rem] font-bold text-slate-500 font-headline">
                        <span>En Ruta</span>
                        <span class="text-slate-700 font-extrabold">{{ array_sum($funnel_stats) > 0 ? round(($funnel_stats['transito'] / array_sum($funnel_stats)) * 100) : 0 }}%</span>
                    </div>
                </div>

                {{-- Funnel Stage 4: Completado --}}
                <div class="bg-emerald-50 p-6 rounded-3xl border border-emerald-100/80 relative overflow-hidden hover:bg-emerald-100/40 transition-all">
                    <div class="absolute -right-4 -bottom-4 text-emerald-500/10 text-[5rem] font-black pointer-events-none select-none">04</div>
                    <p class="text-[0.6rem] font-black text-emerald-600 uppercase tracking-widest mb-1 font-headline">Paso 4: Fin</p>
                    <h4 class="text-sm font-black text-emerald-800 uppercase tracking-tight mb-4">Cierre con Acuse</h4>
                    <div class="text-3xl font-black text-emerald-600 tracking-tighter">{{ number_format($funnel_stats['completado']) }}</div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-emerald-500/20 text-[0.65rem] font-bold text-emerald-700 font-headline">
                        <span>Entregas Exitosas</span>
                        <span class="text-emerald-600 font-extrabold">{{ array_sum($funnel_stats) > 0 ? round(($funnel_stats['completado'] / array_sum($funnel_stats)) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Premium KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        {{-- KPI 1: Ingresos --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-blue-500/5 rounded-full group-hover:scale-125 transition-all"></div>
            <div class="relative z-10">
                <span class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[20px]">inbox_customize</span>
                </span>
                <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-widest mb-1">Total Ingresos</p>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['ingresos']) }}</div>
                <p class="text-[0.65rem] font-bold text-slate-400 mt-2">En el periodo seleccionado</p>
            </div>
        </div>

        {{-- KPI 2: Salidas --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-emerald-500/5 rounded-full group-hover:scale-125 transition-all"></div>
            <div class="relative z-10">
                <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[20px]">outbox_alt</span>
                </span>
                <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-widest mb-1">Carnets Listos</p>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['salidas']) }}</div>
                <p class="text-[0.65rem] font-bold text-emerald-500 mt-2 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-xs">task_alt</span> Proceso finalizado
                </p>
            </div>
        </div>

        {{-- KPI 3: OTD Rate (On-Time Delivery) --}}
        <div class="bg-slate-900 p-6 rounded-3xl relative overflow-hidden group text-white">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-primary/20 rounded-full group-hover:scale-125 transition-all"></div>
            <div class="relative z-10">
                <span class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[20px]">speed</span>
                </span>
                <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-widest mb-1">Entrega a Tiempo (OTD)</p>
                <div class="text-3xl font-black text-white tracking-tight">{{ $stats['otd_rate'] }}%</div>
                <div class="w-full bg-white/15 h-1 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-400 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['otd_rate'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Tiempo Medio de Ciclo --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-indigo-500/5 rounded-full group-hover:scale-125 transition-all"></div>
            <div class="relative z-10">
                <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[20px]">timelapse</span>
                </span>
                <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-widest mb-1">Tiempo de Ciclo</p>
                <div class="text-3xl font-black text-slate-800 tracking-tight">{{ $stats['avg_cycle_time'] }} <span class="text-xs text-slate-400 font-bold uppercase">Días</span></div>
                <p class="text-[0.65rem] font-bold text-slate-400 mt-2">Promedio desde creación</p>
            </div>
        </div>

        {{-- KPI 5: SLA Crítico --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-rose-500/5 rounded-full group-hover:scale-125 transition-all"></div>
            <div class="relative z-10">
                <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-4 {{ $stats['critico_sla'] > 0 ? 'animate-pulse' : '' }}">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                </span>
                <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-widest mb-1">SLA Excedido</p>
                <div class="text-3xl font-black text-slate-800 tracking-tight {{ $stats['critico_sla'] > 0 ? 'text-rose-600' : '' }}">{{ number_format($stats['critico_sla']) }}</div>
                <p class="text-[0.65rem] font-bold text-slate-400 mt-2 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-xs text-rose-400">hourglass_bottom</span> Más de 20 días activos
                </p>
            </div>
        </div>
    </div>

    {{-- Main Layout Grid: Charts & Leaderboard --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Trend Chart Card --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Tendencia de Ingresos</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Volumen diario ingresado</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">show_chart</span>
                </span>
            </div>
            <div class="h-80 w-full">
                <canvas id="tendenciaChart"></canvas>
            </div>
        </div>

        {{-- Hotspots Regional Leaderboard --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Hotspots Geográficos</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Mayor volumen de entregas</p>
                </div>
                <a href="{{ route('reportes.heatmap') }}" class="p-2 bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm shadow-blue-500/5" title="Ver Monitor Geográfico Completo">
                    <span class="material-symbols-outlined text-sm">map</span>
                </a>
            </div>

            <div class="space-y-6">
                @forelse($densidad_provincias as $p)
                <div class="group">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 font-bold text-xs uppercase group-hover:bg-primary group-hover:text-white transition-all shadow-inner border border-slate-100">
                                {{ substr($p->provinciaRel?->nombre ?? '?', 0, 2) }}
                            </div>
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $p->provinciaRel?->nombre ?? 'Desconocida' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black text-slate-800">{{ number_format($p->total) }}</span>
                            <span class="text-[0.6rem] font-bold text-slate-400 uppercase block leading-none">Afiliados</span>
                        </div>
                    </div>
                    {{-- Progress Bar --}}
                    @php 
                        $maxTotal = $densidad_provincias->max('total');
                        $percent = $maxTotal > 0 ? ($p->total / $maxTotal) * 100 : 0;
                    @endphp
                    <div class="w-full bg-slate-50 h-1.5 rounded-full overflow-hidden border border-slate-100/50">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 group-hover:from-emerald-500 group-hover:to-emerald-400 rounded-full transition-all duration-700" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center py-16 border-2 border-dashed border-slate-50 rounded-[2rem] text-slate-300 italic text-xs font-bold">
                    <span class="material-symbols-outlined text-2xl mb-1">map</span> <br>
                    Sin datos geográficos registrados
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Secondary Grid: Operational & Productivity Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Status Distribution Card --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Estado Operativo</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Distribución del flujo</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">pie_chart</span>
                </span>
            </div>
            <div class="h-56 w-full flex items-center justify-center">
                <canvas id="estadoChart"></canvas>
            </div>
            <div class="mt-6 space-y-2.5 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($estados->sortByDesc('afiliados_count') as $e)
                <div class="flex items-center justify-between text-[0.7rem] py-2 border-b border-slate-50 last:border-0 font-bold">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#94a3b8'][$loop->index % 6] }}"></span>
                        <span class="text-slate-500 uppercase tracking-tight">{{ $e->nombre }}</span>
                    </div>
                    <span class="text-slate-800 font-black">{{ number_format($e->afiliados_count) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Performance by Responsable --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Top Responsables</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Productividad y asignaciones</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">assignment_ind</span>
                </span>
            </div>
            <div class="h-[20rem] w-full">
                <canvas id="responsableChart"></canvas>
            </div>
        </div>

        {{-- Production by Corte --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100/80">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Producción por Corte</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Lotes de carnetización</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                </span>
            </div>
            <div class="h-[20rem] w-full">
                <canvas id="corteChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared global chart config
        const fontConfig = {
            family: 'Outfit, Inter, system-ui, -apple-system, sans-serif',
            size: 10,
            weight: 'bold'
        };

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: fontConfig,
                    bodyFont: fontConfig,
                    boxWidth: 8,
                    boxHeight: 8,
                    usePointStyle: true
                }
            }
        };

        // 1. Tendencia Chart (Gradient Line)
        const tendenciaCanvas = document.getElementById('tendenciaChart');
        if (tendenciaCanvas) {
            const ctx = tendenciaCanvas.getContext('2d');
            const blueGradient = ctx.createLinearGradient(0, 0, 0, 300);
            blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
            blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($tendencia->pluck('fecha')) !!},
                    datasets: [{
                        label: 'Ingresos',
                        data: {!! json_encode($tendencia->pluck('total_ingreso')) !!},
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        backgroundColor: blueGradient,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointHoverRadius: 6,
                        pointHoverBorderWidth: 3
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: { 
                            grid: { display: false }, 
                            ticks: { font: fontConfig, color: '#94a3b8' } 
                        },
                        y: { 
                            border: { dash: [4, 4] },
                            grid: { color: '#f1f5f9' },
                            ticks: { font: fontConfig, color: '#94a3b8' }
                        }
                    }
                }
            });
        }

        // 2. Estado Chart (Borderless Ring cutout)
        const estadoCanvas = document.getElementById('estadoChart');
        if (estadoCanvas) {
            new Chart(estadoCanvas, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($estados->pluck('nombre')) !!},
                    datasets: [{
                        data: {!! json_encode($estados->pluck('afiliados_count')) !!},
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#94a3b8'],
                        borderWidth: 0,
                        borderRadius: 8,
                        cutout: '80%'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: { display: false }
                    }
                }
            });
        }

        // 3. Responsable Chart (Horizontal Border-radius Bars)
        const responsableCanvas = document.getElementById('responsableChart');
        if (responsableCanvas) {
            new Chart(responsableCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($responsables_data->map(fn($r) => explode(' ', $r->nombre)[0])) !!},
                    datasets: [{
                        data: {!! json_encode($responsables_data->pluck('afiliados_count')) !!},
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        maxBarThickness: 16
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    scales: {
                        x: { display: false },
                        y: { 
                            grid: { display: false },
                            ticks: { font: fontConfig, color: '#64748b' }
                        }
                    }
                }
            });
        }

        // 4. Corte Chart (Vertical Bar charts)
        const corteCanvas = document.getElementById('corteChart');
        if (corteCanvas) {
            new Chart(corteCanvas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($cortes_data->pluck('nombre')) !!},
                    datasets: [{
                        data: {!! json_encode($cortes_data->pluck('afiliados_count')) !!},
                        backgroundColor: '#f59e0b',
                        borderRadius: 8,
                        maxBarThickness: 18
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { font: fontConfig, color: '#64748b' }
                        },
                        y: { display: false }
                    }
                }
            });
        }
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
