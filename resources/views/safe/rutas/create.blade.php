@extends('layouts.app')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight italic text-shadow-sm">Planificar Despacho <span class="text-secondary text-2xl NOT-italic opacity-50">/ Route Creation</span></h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Asigne afiliados a un mensajero para iniciar el proceso de entrega.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('safe.rutas.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Volver
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-5xl">
    <form action="{{ route('safe.rutas.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left Column: Route Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Información de Ruta</label>
                    <div class="space-y-6">
                        <div>
                            <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Mensajero Asignado</span>
                            <div class="relative">
                                <select name="mensajero_id" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-secondary appearance-none transition-all">
                                    @if(count($mensajeros) > 0)
                                        <option value="">Seleccione un mensajero...</option>
                                        @foreach($mensajeros as $m)
                                            <option value="{{ $m->id }}">{{ $m->nombre }} ({{ $m->vehiculo_placa }})</option>
                                        @endforeach
                                    @else
                                        <option value="">NO HAY MENSAJEROS ACTIVOS</option>
                                    @endif
                                </select>
                                @if(count($mensajeros) == 0)
                                    <div class="mt-2 text-xs text-rose-500 font-bold italic flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">warning</span>
                                        Debe registrar al menos un mensajero activo.
                                    </div>
                                @endif
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">delivery_dining</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Nombre de la Ruta</span>
                            <input type="text" name="nombre_ruta" required placeholder="Ej: Ruta Norte" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-secondary transition-all">
                        </div>

                        <div>
                            <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1">Fecha Programada</span>
                            <input type="date" name="fecha_programada" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-secondary transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <button type="submit" class="w-full py-5 bg-slate-900 text-white font-black rounded-[2rem] shadow-xl shadow-slate-200 hover:bg-slate-800 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                        <span class="material-symbols-outlined">local_shipping</span>
                        Crear y Despachar
                    </button>
                    <p class="text-[0.6rem] text-slate-400 text-center mt-4 font-bold uppercase tracking-widest opacity-50">Safesure Logistics Engine v1.0</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Affiliate Selection -->
        <div class="lg:col-span-2">
            <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 flex flex-col h-full">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 pb-6 border-b border-slate-50 gap-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Afiliados Disponibles</h3>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tighter">Seleccione los expedientes a incluir en esta ruta</p>
                    </div>
                    
                    <div class="w-full md:w-64 relative">
                        <input type="text" id="afiliadoSearch" placeholder="Filtrar por nombre o cédula..." 
                               class="w-full bg-slate-50 border-none rounded-xl py-3 px-10 text-xs font-bold focus:ring-2 focus:ring-secondary transition-all">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    </div>

                    <div class="px-4 py-2 bg-secondary/10 rounded-2xl border border-secondary/10 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                        <span id="libresCounter" class="text-[0.65rem] font-black text-secondary uppercase tracking-[0.2em]">{{ count($afiliados_libres) }} Libres</span>
                    </div>
                </div>

                <div id="afiliadosGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4 overflow-y-auto max-h-[600px] pr-4 custom-scrollbar">
                    @foreach($afiliados_libres as $a)
                    <label class="relative group cursor-pointer afiliado-item" data-search="{{ strtolower($a->nombre_completo . ' ' . $a->cedula) }}">
                        <input type="checkbox" name="afiliados[]" value="{{ $a->id }}" class="peer absolute opacity-0">
                        <div class="p-6 bg-slate-50/50 rounded-3xl border border-slate-50 peer-checked:border-secondary peer-checked:bg-secondary/5 peer-checked:ring-1 peer-checked:ring-secondary group-hover:bg-white transition-all duration-300 h-full flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-300 peer-checked:text-secondary peer-checked:border-secondary/20 transition-all shrink-0">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-sm font-black text-slate-700 leading-tight truncate group-hover:text-slate-900">{{ $a->nombre_completo }}</p>
                                <div class="flex flex-col mt-1">
                                    <span class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-tighter">{{ $a->cedula }}</span>
                                    <span class="text-[0.65rem] font-black text-secondary/60 uppercase tracking-tighter truncate">{{ $a->provincia_nombre }} - {{ $a->municipio_nombre }}</span>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                
                <div id="noResults" class="hidden py-20 text-center">
                    <span class="material-symbols-outlined text-slate-200 text-6xl mb-4">search_off</span>
                    <h3 class="text-lg font-bold text-slate-400">Sin coincidencias</h3>
                    <p class="text-xs text-slate-400 mt-1 uppercase">No encontramos afiliados con ese nombre o cédula.</p>
                </div>

                @if(count($afiliados_libres) == 0)
                <div class="py-20 text-center">
                    <span class="material-symbols-outlined text-slate-200 text-6xl mb-4">person_off</span>
                    <h3 class="text-lg font-bold text-slate-400">No hay afiliados pendientes</h3>
                    <p class="text-xs text-slate-400 mt-1">Todos los carnets ya están asignados a rutas activas.</p>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('afiliadoSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.afiliado-item');
        let visibleCount = 0;

        items.forEach(item => {
            if (item.getAttribute('data-search').includes(query)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const noResults = document.getElementById('noResults');
        const grid = document.getElementById('afiliadosGrid');
        
        if (visibleCount === 0 && query !== '') {
            noResults.classList.remove('hidden');
            grid.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            grid.classList.remove('hidden');
        }
    });
</script>
@endsection
