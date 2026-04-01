@extends('layouts.app')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight italic text-shadow-sm">Despacho de Rutas <span class="text-secondary text-2xl NOT-italic opacity-50">/ Delivery Planning</span></h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Planificación y seguimiento de hojas de ruta de mensajería.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('safe.rutas.create') }}" class="px-5 py-2.5 bg-secondary text-white font-bold text-sm rounded-xl shadow-lg shadow-secondary/20 hover:scale-105 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">add_location</span> Nueva Ruta
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($rutas as $ruta)
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group hover:border-secondary hover:shadow-xl transition-all duration-500 flex flex-col items-stretch relative">
        <div class="p-8 pb-4">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-white transition-all">
                    <span class="material-symbols-outlined font-variation-settings: 'FILL' 1;">directions_bike</span>
                </div>
                <span class="px-3 py-1 bg-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-50 text-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-600 text-[0.6rem] font-black uppercase tracking-widest rounded-full border border-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-100 group-hover:scale-110 transition-transform">
                    {{ $ruta->estado }}
                </span>
            </div>

            <h3 class="text-xl font-black text-slate-900 mb-1 leading-tight">{{ $ruta->nombre_ruta }}</h3>
            <div class="flex items-center gap-3 text-slate-400 mb-6">
                <span class="text-[0.65rem] font-bold uppercase tracking-widest flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">person</span> {{ $ruta->mensajero->nombre }}
                </span>
                <span class="text-slate-200">|</span>
                <span class="text-[0.65rem] font-bold uppercase tracking-widest flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $ruta->fecha_programada }}
                </span>
            </div>

            @php
                $total = $ruta->afiliados_count;
                $entregados = $ruta->afiliados()->wherePivot('entregado', true)->count();
                $porcentaje = $total > 0 ? round(($entregados / $total) * 100) : 0;
            @endphp

            <div class="space-y-4 mb-4">
                <div class="flex justify-between items-center text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">
                    <span>Progreso de Entrega</span>
                    <span class="text-secondary">{{ $porcentaje }}%</span>
                </div>
                <div class="h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100">
                    <div class="h-full bg-secondary rounded-full transition-all duration-1000 shadow-sm" style="width:{{ $porcentaje }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[0.65rem] font-bold text-slate-500">
                    <span>{{ $entregados }} Carnets Entregados</span>
                    <span>Total: {{ $total }}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-auto p-2">
            <a href="{{ route('safe.rutas.show', $ruta) }}" class="flex items-center justify-center gap-2 w-full py-4 bg-slate-50 hover:bg-slate-900 hover:text-white rounded-[1.5rem] text-xs font-black text-slate-600 uppercase tracking-widest transition-all">
                <span class="material-symbols-outlined text-lg">visibility</span>
                Gestionar Ruta
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
        <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">map</span>
        <h3 class="text-xl font-bold text-slate-400">No hay rutas despachadas</h3>
        <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">Comienza planificando una ruta de entrega hoy mismo.</p>
    </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $rutas->links() }}
</div>
@endsection
