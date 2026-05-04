@extends('layouts.app')
@section('content')
<div class="p-8 max-w-[1600px] mx-auto space-y-8">
    <!-- Header & Quick Filters -->
    <div class="flex flex-col gap-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Tablero de Operaciones</h2>
                <p class="text-slate-500 text-sm mt-1">Monitoreo en tiempo real del ciclo de carnetización.</p>
            </div>
            <div class="w-full sm:w-auto">
                <a href="{{ route('import.index') }}" class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                    <i class="ph ph-cloud-arrow-up text-xl"></i> Cargar Lote
                </a>
            </div>
        </div>

        {{-- Sync Heartbeat Indicator --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100/50 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-emerald-200 rounded-full shadow-sm w-fit">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[0.6rem] font-black text-emerald-600 uppercase tracking-widest">Sincronización Activa</span>
            </div>
            <p class="text-[0.65rem] text-emerald-800 font-medium leading-relaxed">Conectado a Firebase Realtime • Webhook SSL Habilitado • Latencia < 500ms</p>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-6 gap-4 lg:gap-6">
        <!-- Total Affiliates -->
        <div class="col-span-2 sm:col-span-1 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <i class="ph ph-users text-7xl text-slate-400"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <span class="text-slate-400 font-black uppercase text-[0.6rem] tracking-widest">Total Afiliados</span>
                <span class="text-3xl lg:text-4xl font-black text-slate-800 mt-2">{{ number_format($totalAfiliados) }}</span>
                <p class="text-[0.6rem] text-slate-400 mt-2 font-bold uppercase tracking-tighter">Registros históricos</p>
            </div>
        </div>

        <!-- Empresas FILIAL Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <i class="ph-fill ph-buildings text-7xl text-primary"></i>
            </div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <span class="text-primary font-black uppercase text-[0.6rem] tracking-widest">Empresas ARS</span>
                <span class="text-2xl lg:text-3xl font-black text-slate-800 mt-3">{{ number_format($confirmadosFilial) }}</span>
                <div class="mt-3">
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $totalFilial > 0 ? ($confirmadosFilial / $totalFilial) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OTRAS Card -->
        <div class="bg-white p-6 rounded-2xl border-l-4 border-blue-400 shadow-sm hover-card relative overflow-hidden group">
            <div class="absolute -right-2 -top-2 opacity-5 scale-150 transition-transform group-hover:scale-120 duration-700">
                <span class="material-symbols-outlined text-8xl text-blue-400">corporate_fare</span>
            </div>
            <div class="flex flex-col h-full justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 bg-blue-50 rounded-lg"><span class="material-symbols-outlined text-blue-600 text-sm">corporate_fare</span></span>
                    <span class="text-blue-400 font-bold uppercase text-[0.625rem] tracking-wider">Otras Empresas</span>
                </div>
                <span class="text-3xl font-extrabold font-headline text-slate-800 mt-3">{{ number_format($totalOtras) }}</span>
                <div class="mt-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[0.6rem] text-blue-500 font-bold uppercase">Completados</span>
                        <span class="text-[0.6rem] text-slate-500 font-bold">{{ $terminadosOtras }}/{{ $totalOtras }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                        <div class="bg-blue-400 h-full transition-all duration-1000" style="width: {{ $totalOtras > 0 ? ($terminadosOtras / $totalOtras) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asignados -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-secondary shadow-sm">
            <span class="text-slate-400 font-bold uppercase text-[0.625rem] tracking-wider">Ya Asignados</span>
            <div class="mt-4">
                <span class="text-2xl font-bold font-headline text-slate-800">{{ number_format($totalAsignados) }}</span>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-secondary h-full" style="width: {{ $totalAfiliados > 0 ? round(($totalAsignados/$totalAfiliados)*100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Entregados -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-amber-500 shadow-sm">
            <span class="text-slate-400 font-bold uppercase text-[0.625rem] tracking-wider">Pend. Evidencia</span>
            <div class="mt-4">
                <span class="text-2xl font-bold font-headline text-slate-800">{{ number_format($totalEntregados) }}</span>
                <p class="text-[0.65rem] text-amber-600 font-black uppercase mt-1">En tránsito</p>
            </div>
        </div>

        <!-- Completados -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl border-l-4 border-emerald-500 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-2 -top-2 opacity-5 scale-150 transition-transform group-hover:scale-125 duration-700">
                <span class="material-symbols-outlined text-8xl text-emerald-500">check_circle</span>
            </div>
            <div class="flex flex-col h-full justify-between">
                <span class="text-slate-400 font-bold uppercase text-[0.625rem] tracking-wider">Completados</span>
                <span class="text-3xl font-extrabold font-headline text-slate-800 mt-2">{{ number_format($totalCompletados) }}</span>
                <p class="text-[0.65rem] text-emerald-600 font-black uppercase mt-1">{{ $porcentajeCompletado }}% Efectividad</p>
            </div>
        </div>

        <!-- Por Liquidar -->
        <div class="bg-white p-6 rounded-2xl border-l-4 border-amber-500 shadow-sm hover-card relative overflow-hidden group">
            <div class="absolute -right-2 -top-2 opacity-5 scale-150 transition-transform group-hover:scale-120 duration-700">
                <span class="material-symbols-outlined text-8xl text-amber-500">payments</span>
            </div>
            <div class="flex flex-col h-full justify-between">
                <span class="text-slate-400 font-bold uppercase text-[0.625rem] tracking-wider">Por Liquidar</span>
                <div class="mt-2 space-y-1">
                    <div class="flex justify-between items-center text-[0.7rem] font-bold">
                        <span class="text-slate-500">ARS:</span>
                        <span class="text-slate-800">${{ number_format($montoArs, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[0.7rem] font-bold">
                        <span class="text-slate-500">OTR:</span>
                        <span class="text-slate-600">${{ number_format($montoNoArs, 0) }}</span>
                    </div>
                </div>
                <p class="text-[0.65rem] text-amber-600 mt-2 font-bold uppercase">Total: ${{ number_format($montoArs + $montoNoArs, 0) }}</p>
            </div>
        </div>

        <!-- Empresas Verificadas -->
        <div class="bg-emerald-50 p-6 rounded-2xl border-l-4 border-emerald-600 shadow-sm hover-card relative overflow-hidden group">
            <div class="absolute -right-2 -top-2 opacity-5 scale-150 transition-transform group-hover:scale-120 duration-700">
                <span class="material-symbols-outlined text-8xl text-emerald-600">verified</span>
            </div>
            <div class="flex flex-col h-full justify-between">
                <span class="text-emerald-600 font-bold uppercase text-[0.625rem] tracking-wider">Empresas Verificadas</span>
                <span class="text-3xl font-extrabold font-headline text-emerald-800 mt-2">{{ number_format($confirmadosVerificadas) }}</span>
                <div class="mt-2">
                    <div class="flex justify-between items-center text-[0.6rem] font-black text-emerald-600 uppercase mb-1">
                        <span>CONFIRMADOS</span>
                        <span>{{ $confirmadosVerificadas }}/{{ $totalVerificadas }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-emerald-200 h-1 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full" style="width: {{ $totalVerificadas > 0 ? ($confirmadosVerificadas / $totalVerificadas) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-[0.6rem] font-black text-emerald-600">{{ $totalVerificadas > 0 ? round(($confirmadosVerificadas / $totalVerificadas) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fuera de SLA -->
        <div class="bg-rose-50 p-6 rounded-2xl border-l-4 border-rose-500 shadow-sm hover-card relative overflow-hidden group">
            <div class="absolute -right-2 -top-2 opacity-5 scale-150 transition-transform group-hover:scale-120 duration-700">
                <span class="material-symbols-outlined text-8xl text-rose-500">alarm_off</span>
            </div>
            <div class="flex flex-col h-full justify-between">
                <span class="text-rose-400 font-bold uppercase text-[0.625rem] tracking-wider">Fuera de SLA</span>
                <span class="text-4xl font-extrabold font-headline text-rose-700 mt-2">{{ $fueraSlaCount }}</span>
                <p class="text-[0.6rem] text-rose-600 font-black uppercase mt-2 animate-pulse">Críticos (>20 días)</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfica de Estados (Doughnut) -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Distribución por Estados</h3>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Global</span>
            </div>
            <div class="relative h-[300px] flex items-center justify-center">
                <canvas id="estadoChart"></canvas>
            </div>
        </div>

        <!-- Gráfica de Tendencia (Line) -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-800">Tendencia de Carga</h3>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Últimos 6 Meses</span>
            </div>
            <div class="relative h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente y Productividad -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Productividad por Responsable (Simplificado) -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-lg font-bold text-slate-800 mb-6">Rendimiento por Equipo</h3>
            <div class="space-y-6">
                @forelse($productividadResponsables as $resp)
                    <div class="space-y-2">
                        <div class="flex justify-between text-[0.7rem] font-black text-slate-500 uppercase tracking-tighter">
                            <span>{{ $resp->responsable->nombre }}</span>
                            <span>{{ $resp->porcentaje }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $resp->porcentaje }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">Sin datos de responsables.</p>
                @endforelse
            </div>
        </div>

        <!-- Tabla Actividad Reciente -->
        <div class="bg-white p-6 lg:p-8 rounded-3xl shadow-sm border border-slate-200/60 lg:col-span-2">
            <h3 class="text-lg font-black text-slate-800 mb-6">Actividad Operativa Reciente</h3>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left responsive-table">
                    <thead class="hidden lg:table-header-group">
                        <tr class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="pb-4">Afiliado</th>
                            <th class="pb-4">Evento</th>
                            <th class="pb-4">Usuario</th>
                            <th class="pb-4 text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($actividadReciente as $act)
                        <tr class="group hover:bg-slate-50/50 transition-all">
                            <td class="py-4 afiliado-cell">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 group-hover:text-primary transition-colors">{{ $act->afiliado->nombre_completo ?? 'Afiliado no accesible' }}</span>
                                    <span class="text-[0.6rem] text-slate-400 font-bold uppercase tracking-tighter">{{ $act->afiliado->cedula_formatted ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="py-4" data-label="Evento">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wider">{{ $act->estadoNuevo->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4" data-label="Operador">
                                <span class="text-xs text-slate-500 font-bold">{{ $act->user->name ?? 'Sistema' }}</span>
                            </td>
                            <td class="py-4 text-right" data-label="Hace">
                                <span class="text-[10px] text-slate-400 font-black uppercase">{{ $act->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SLA Alertas Center has been moved to a dedicated page (/reportes/alertas-sla) for performance --}}
</div>

<!-- Contenedores de Datos Ocultos (Para evitar errores de linter) -->
<div id="dashboard-data" class="hidden"
    data-estados-labels='{{ json_encode($afiliadosPorEstado->map(fn($i) => $i->estado->nombre)) }}'
    data-estados-total='{{ json_encode($afiliadosPorEstado->pluck("total")) }}'
    data-trend-labels='{{ json_encode($statsPorMes->pluck("mes")) }}'
    data-trend-total='{{ json_encode($statsPorMes->pluck("total")) }}'>
</div>

<!-- Scripts de Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataContainer = document.getElementById('dashboard-data');
    if (!dataContainer) return;

    // 1. Gráfica de Estados (Doughnut)
    const ctxEstado = document.getElementById('estadoChart').getContext('2d');
    new Chart(ctxEstado, {
        type: 'doughnut',
        data: {
            labels: JSON.parse(dataContainer.dataset.estadosLabels),
            datasets: [{
                data: JSON.parse(dataContainer.dataset.estadosTotal),
                backgroundColor: [
                    '#00346f', '#0288d1', '#10b981', '#f59e0b', '#ba1a1a', '#6366f1', '#8b5cf6'
                ],
                hoverOffset: 15,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, font: { weight: 'bold', size: 10 } }
                }
            },
            cutout: '70%'
        }
    });

    // 2. Gráfica de Tendencia (Line)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: JSON.parse(dataContainer.dataset.trendLabels),
            datasets: [{
                label: 'Nuevos Afiliados',
                data: JSON.parse(dataContainer.dataset.trendTotal),
                borderColor: '#0288d1',
                backgroundColor: 'rgba(2, 136, 209, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endsection
