@extends('layouts.app')

@section('title', 'Portafolio Corporativo')

@section('content')
<div class="space-y-6 animate-page-transition">
    
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">
                Directorio Corporativo <span class="text-blue-500 font-medium text-lg ml-2">ARS / Entidades</span>
            </h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Gestión de relaciones y trazabilidad empresarial</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('empresas.enrich') }}" class="w-10 h-10 bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all flex items-center justify-center shadow-sm" title="Saneamiento de Datos">
                <i class="ph-bold ph-magic-wand text-lg"></i>
            </a>
            <a href="{{ route('empresas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i class="ph-bold ph-plus"></i> Nueva Entidad
            </a>
        </div>
    </div>

    <!-- KPI & Analytics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm group hover:border-blue-500/20 transition-all">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Base de Datos</span>
                <div class="w-7 h-7 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center">
                    <i class="ph ph-database text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-display font-bold text-slate-800 tracking-tight">{{ number_format($stats['total']) }}</h3>
            <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Registros Totales</p>
        </div>

        <!-- Verified Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm group hover:border-emerald-500/20 transition-all">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Entidades Reales</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="ph-fill ph-seal-check text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-display font-bold text-slate-800 tracking-tight">{{ number_format($stats['reales']) }}</h3>
            <p class="text-[9px] text-emerald-500 font-bold uppercase mt-1">Verificadas</p>
        </div>

        <!-- Filial Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm group hover:border-purple-500/20 transition-all">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sucursales ARS</span>
                <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="ph ph-git-fork text-sm"></i>
                </div>
            </div>
            <h3 class="text-2xl font-display font-bold text-slate-800 tracking-tight">{{ number_format($stats['filiales']) }}</h3>
            <p class="text-[9px] text-purple-500 font-bold uppercase mt-1">Dependencias</p>
        </div>

        <!-- Distribution Chart -->
        <div class="bg-slate-900 p-5 rounded-2xl shadow-xl relative overflow-hidden flex flex-col justify-between">
            <div class="absolute right-0 top-0 w-20 h-20 bg-blue-500/10 blur-2xl rounded-full"></div>
            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest relative z-10">Mix Portafolio</span>
            <div class="h-16 relative z-10 mt-2">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Interactive Table Core -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <livewire:empresa.index />
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('distributionChart').getContext('2d');
        const distribution = @json($stats['distribution']);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Verificadas', 'Sucursales', 'Otros'],
                datasets: [{
                    data: [distribution.reales, distribution.filiales, distribution.otros],
                    backgroundColor: ['#2563eb', '#a855f7', '#334155'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '80%',
                plugins: {
                    legend: { display: false }
                },
                maintainAspectRatio: false,
                animation: { duration: 2000, easing: 'easeOutQuart' }
            }
        });
    });
</script>
@endpush
