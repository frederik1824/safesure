@extends('layouts.app')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight italic text-shadow-sm">Equipo de Mensajería <span class="text-secondary text-2xl NOT-italic opacity-50">/ Safesure Fleet</span></h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Gestión del personal operativo y flota de vehículos.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('safe.mensajeros.create') }}" class="px-5 py-2.5 bg-secondary text-white font-bold text-sm rounded-xl shadow-lg shadow-secondary/20 hover:scale-105 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">person_add</span> Registrar Mensajero
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($mensajeros as $mensajero)
    <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 hover:border-secondary hover:shadow-xl transition-all duration-500 group flex flex-col relative overflow-hidden">
        <div class="absolute -right-4 -top-4 opacity-[0.03] scale-150 transition-transform group-hover:scale-125 duration-700">
            <span class="material-symbols-outlined text-9xl">motorcycle</span>
        </div>
        
        <div class="flex items-center gap-4 mb-8 relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black text-2xl italic group-hover:scale-110 group-hover:bg-secondary transition-all">
                {{ substr($mensajero->nombre, 0, 1) }}{{ substr(strrchr($mensajero->nombre, " "), 1, 1) ?: '' }}
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 leading-tight">{{ $mensajero->nombre }}</h3>
                <span class="px-2 py-0.5 bg-secondary/10 text-secondary rounded text-[0.6rem] font-black uppercase tracking-[0.2em]">ID: SAFE-{{ str_pad($mensajero->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="space-y-4 mb-8 flex-1 relative z-10">
            <div class="flex items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined text-lg opacity-50">smartphone</span>
                <span class="text-xs font-bold">{{ $mensajero->telefono ?? 'Sin Telefono' }}</span>
            </div>
            <div class="flex items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined text-lg opacity-50">directions_bike</span>
                <span class="text-xs font-bold">Placa: {{ $mensajero->vehiculo_placa ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-3 text-slate-500">
                <span class="material-symbols-outlined text-lg opacity-50">account_circle</span>
                <span class="text-[0.65rem] font-bold">Usuario App: {{ $mensajero->user->email ?? 'Pendiente' }}</span>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-50 flex items-center justify-between relative z-10">
            <div class="flex flex-col">
                <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">Entregas Mes</span>
                <span class="text-sm font-black text-slate-900">0</span>
            </div>
            <a href="{{ route('safe.mensajeros.edit', $mensajero->id) }}" class="p-2 text-slate-300 hover:text-secondary hover:bg-secondary/5 rounded-xl transition-all">
                <span class="material-symbols-outlined">edit_square</span>
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border border-dashed border-slate-200">
        <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">motorcycle</span>
        <h3 class="text-xl font-bold text-slate-400">No hay mensajeros registrados</h3>
        <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">Regístrate al personal de Safesure para poder asignarles rutas de despacho.</p>
    </div>
    @endforelse
</div>
@endsection
