
@extends('layouts.app')
@section('title', 'Gestión de Afiliados')

@section('content')
<div class="space-y-6 animate-page-transition">
    
    <!-- Page Header & Global Stats -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight">
                @if(!$segment)
                    Control de Asignaciones <span class="text-blue-500 font-medium text-lg ml-2">Pendientes</span>
                @else
                    Módulo de Afiliados <span class="text-slate-400 font-medium text-lg ml-2">{{ $segment }}</span>
                @endif
            </h1>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Monitoreo y despacho de flota logística</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Removed bulk actions from header, now floating -->

            <div class="flex items-center gap-2">
                <form action="{{ route('afiliados.sanitize') }}" method="POST" class="contents">
                    @csrf
                    <button type="submit" class="w-10 h-10 bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-xl transition-all flex items-center justify-center shadow-sm" title="Normalizar Datos">
                        <i class="ph-bold ph-magic-wand text-lg"></i>
                    </button>
                </form>
                <a href="{{ route('afiliados.export', request()->all()) }}" class="w-10 h-10 bg-white border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 rounded-xl transition-all flex items-center justify-center shadow-sm" title="Exportar Excel">
                    <i class="ph-bold ph-download-simple text-lg"></i>
                </a>
                <a href="{{ route('afiliados.create', ['segment' => $segment]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2 ml-2">
                    <i class="ph-bold ph-plus"></i> Registro Manual
                </a>
            </div>
        </div>
    </div>

    @if(isset($statsPorPeriodo) && $statsPorPeriodo->count() > 0)
    <!-- Period Progress -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statsPorPeriodo as $stat)
        <div class="bg-white p-5 rounded-2xl border border-slate-200/60 shadow-sm group hover:border-blue-500/20 transition-all">
            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $stat->nombre }}</span>
                <span class="text-[11px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{{ $stat->porcentaje }}%</span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-2xl font-display font-bold text-slate-800 tracking-tight">{{ number_format($stat->total) }}</p>
                    <p class="text-[9px] text-slate-400 font-bold uppercase">Total Base</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-amber-600">{{ number_format($stat->pendiente) }}</p>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Pendiente</p>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-4 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: {{ $stat->porcentaje }}%"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Filters Section -->
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="px-8 py-4 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center cursor-pointer select-none" @click="open = !open">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                    <i class="ph ph-funnel text-base"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Filtros Avanzados</span>
            </div>
            <i class="ph ph-caret-down text-xs text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
        </div>
        
        <div x-show="open" x-collapse>
            <form id="filterForm" method="GET" action="{{ route(Route::currentRouteName()) }}" class="p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Búsqueda General</label>
                    <div class="relative">
                        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o Cédula..." class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-medium pl-10 pr-4 py-2.5 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Estado de Proceso</label>
                    <select name="estado_id" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-medium px-4 py-2.5 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                        <option value="">Cualquier estado</option>
                        @foreach(\App\Models\Estado::all() as $est)
                            <option value="{{ $est->id }}" {{ request('estado_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Corte Operativo</label>
                    <select name="corte_id" class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-medium px-4 py-2.5 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                        <option value="">Todos los cortes</option>
                        @foreach(\App\Models\Corte::all() as $c)
                            <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Entidad / RNC</label>
                    <input type="text" name="rnc_empresa" value="{{ request('rnc_empresa') }}" placeholder="Filtrar empresa..." class="w-full bg-slate-50 border-slate-200 rounded-xl text-xs font-medium px-4 py-2.5 focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm">
                        Aplicar Filtros
                    </button>
                    <a href="{{ route(Route::currentRouteName()) }}" class="w-10 h-10 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-xl flex items-center justify-center transition-all" title="Limpiar">
                        <i class="ph ph-arrow-counter-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

        <!-- Table Container (Modern Floating Cards) -->
        <div id="tableContainer" class="transition-all duration-300 flex flex-col h-[650px]">
            <div class="overflow-x-auto overflow-y-auto custom-scrollbar flex-1 pr-2">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead class="sticky top-0 z-20">
                        <tr class="bg-slate-100/50 backdrop-blur-md">
                            <th class="py-4 px-6 text-[10px] font-black tracking-widest uppercase text-slate-400">
                                <input id="selectAll" class="rounded text-slate-900 focus:ring-slate-500 border-slate-300 w-4 h-4 cursor-pointer" type="checkbox"/>
                            </th>
                            <th class="py-4 px-2 text-[10px] font-black tracking-widest uppercase text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('sort') === 'nombre' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-900 transition-colors">
                                    Afiliado @if(request('sort') === 'nombre') <i class="ph ph-caret-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400">Contrato</th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400">Entidad</th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400">Corte</th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400 text-center">Responsable</th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400 text-center">Entrega</th>
                            <th class="py-4 px-4 text-[10px] font-black tracking-widest uppercase text-slate-400 text-center">Estado</th>
                            <th class="py-4 px-6 text-[10px] font-black tracking-widest uppercase text-slate-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach($afiliados as $afiliado)
                        <tr class="group transition-all duration-300 hover:-translate-y-0.5">
                            <td class="bg-white py-4 px-6 rounded-l-[20px] shadow-sm group-hover:shadow-md transition-all border-y border-l border-slate-100 group-hover:border-blue-200 relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-100 group-hover:bg-blue-600 transition-all"></div>
                                <input name="selected[]" value="{{ $afiliado->id }}" class="rounded text-slate-900 focus:ring-slate-500 border-slate-300 w-4 h-4 cursor-pointer affiliate-checkbox" type="checkbox"/>
                            </td>
                            <td class="bg-white py-4 px-2 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200">
                                <div class="flex items-center gap-3">
                                    <button type="button" onclick="openQuickView('{{ route('afiliados.show', $afiliado) }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                            class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-500 text-sm font-black border border-slate-100 transition-all hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-inner">
                                        {{ substr($afiliado->nombre_completo, 0, 1) }}{{ substr(strrchr($afiliado->nombre_completo, " "), 1, 1) ?: '' }}
                                    </button>
                                    <div class="flex flex-col">
                                        <button type="button" onclick="openQuickView('{{ route('afiliados.show', $afiliado) }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                                class="text-sm font-black text-slate-800 hover:text-blue-600 transition-all text-left uppercase tracking-tight leading-tight">
                                            {{ $afiliado->nombre_completo }}
                                        </button>
                                        <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-tighter">{{ $afiliado->cedula_formatted }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200">
                                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">{{ $afiliado->contrato ?? '---' }}</span>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-700 truncate max-w-[150px] uppercase">{{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}</span>
                                    <span class="text-[9px] font-mono font-bold text-slate-400">{{ $afiliado->empresaModel->rnc ?? 'S/RNC' }}</span>
                                </div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100">{{ $afiliado->corte->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-tighter">{{ explode(' ', $afiliado->responsable->nombre ?? 'S/A')[0] }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">Gestor</span>
                                </div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200 text-center">
                                @if($afiliado->fecha_entrega_safesure)
                                    <div class="inline-flex flex-col items-center bg-slate-50 px-3 py-1 rounded-xl border border-slate-100">
                                        <span class="text-[10px] font-black text-slate-700">{{ $afiliado->fecha_entrega_safesure->format('d/m/y') }}</span>
                                        <span class="text-[8px] font-black text-blue-500 uppercase">{{ $afiliado->dias_transcurridos }} Días</span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Pendiente</span>
                                @endif
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm group-hover:shadow-md transition-all border-y border-slate-100 group-hover:border-blue-200 text-center">
                                <x-status-badge :estado="$afiliado->estado" class="scale-90" />
                            </td>
                            <td class="bg-white py-4 px-6 rounded-r-[20px] shadow-sm group-hover:shadow-md transition-all border-y border-r border-slate-100 group-hover:border-blue-200 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-0 translate-x-4">
                                    <button type="button" onclick="openQuickView('{{ route('afiliados.show', $afiliado) }}', '{{ addslashes($afiliado->nombre_completo) }}')" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-900 hover:text-white transition-all" title="Vista Rápida">
                                        <i class="ph-bold ph-eye"></i>
                                    </button>
                                    @if($afiliado->estado_id != 11)
                                    <button type="button" onclick="quickAcuse('{{ $afiliado->uuid }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all" title="Acuse de Recibo">
                                        <i class="ph-bold ph-check-square"></i>
                                    </button>
                                    @endif
                                    <button type="button" onclick="quickComplete('{{ $afiliado->uuid }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all" title="Marcar como Completado">
                                        <i class="ph-bold ph-check-circle"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        <tr id="emptyRow" class="{{ count($afiliados) > 0 ? 'hidden' : '' }}">
                            <td colspan="9" class="py-32 text-center">
                                <div class="flex flex-col items-center gap-4 max-w-sm mx-auto animate-page-transition">
                                    <div class="relative">
                                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center border border-slate-100 shadow-inner">
                                            <i class="ph-bold ph-magnifying-glass text-4xl text-slate-300"></i>
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-50">
                                            <i class="ph-bold ph-ghost text-xl text-blue-500"></i>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Búsqueda sin éxito</h3>
                                        <p class="text-[11px] text-slate-400 font-bold uppercase leading-relaxed tracking-tight">No encontramos expedientes que coincidan con tus criterios. Intenta limpiar los filtros.</p>
                                    </div>
                                    <button type="button" onclick="location.reload()" class="mt-4 px-6 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-slate-900/20 hover:bg-blue-600 hover:shadow-blue-600/20 transition-all">
                                        Reiniciar Búsqueda
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $afiliados->links() }}
            </div>
        </div>
        
        <!-- Bottom Bento Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Eficacia de Entrega</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="ph ph-trend-up text-base"></i>
                        </div>
                    </div>
                    <h3 class="text-3xl font-display font-bold text-slate-800 tracking-tight">84.2%</h3>
                    <p class="text-[10px] text-slate-400 mt-1 font-bold uppercase">KPI de cumplimiento SLA</p>
                </div>
                <div class="mt-6 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: 84.2%;"></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Distribución de Carga</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="ph ph-users-three text-base"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500">Asignados</span>
                        <span class="text-[11px] font-mono font-bold text-slate-800">{{ number_format($afiliados->total()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500">En Tránsito</span>
                        <span class="text-[11px] font-mono font-bold text-slate-800">142</span>
                    </div>
                    <div class="w-full bg-slate-100 h-px"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-600">Total Operativo</span>
                        <span class="text-[11px] font-mono font-black text-blue-600">{{ number_format($afiliados->total()) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 blur-3xl rounded-full"></div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-bold tracking-tight mb-1">Cierre de Lote</h4>
                        <p class="text-slate-400 text-[10px] leading-relaxed font-medium">Procesamiento masivo de acuses físicos y validación de entregas finales.</p>
                    </div>
                    <a href="{{ route('cierre.index') }}" class="w-fit bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all shadow-lg shadow-blue-600/20">
                        Abrir Módulo de Cierre
                    </a>
                </div>
                <i class="ph ph-folder-open absolute -bottom-6 -right-6 text-white/5 text-8xl transition-transform duration-500 group-hover:scale-110"></i>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar (Enterprise Bulk Actions) -->
    <div id="bulk-actions-wrapper" class="hidden fixed bottom-8 left-1/2 -translate-x-1/2 z-[90] animate-in slide-in-from-bottom-8 duration-300">
        <div class="bg-slate-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-700/50 p-2 flex items-center gap-4">
            <div class="pl-4 pr-2 py-1 flex items-center gap-2 border-r border-slate-700">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-500 text-[10px] font-black text-white" id="floatingSelectedCount">0</span>
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Seleccionados</span>
            </div>
            <div class="flex items-center gap-1 pr-2">
                <button type="button" onclick="openAssignModal()" class="px-4 py-2 bg-slate-800 hover:bg-blue-600 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 group">
                    <i class="ph-bold ph-user-plus text-slate-400 group-hover:text-blue-200"></i> Reasignar
                </button>
                <button type="button" onclick="openStatusModal()" class="px-4 py-2 bg-slate-800 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 group">
                    <i class="ph-bold ph-arrows-clockwise text-slate-400 group-hover:text-amber-200"></i> Cambiar Estado
                </button>
                <button type="button" onclick="clearSelection()" class="px-3 py-2 text-slate-400 hover:text-white transition-colors" title="Cancelar">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modals Section -->
    <div id="assignModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <form method="POST" action="{{ route('afiliados.bulk_assign') }}" id="assignForm" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 scale-in-center">
            @csrf
            @if(isset($segment)) <input type="hidden" name="segment" value="{{ $segment }}"> @endif
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="ph-bold ph-user-plus text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-800 tracking-tight">Asignar Responsable</h3>
                    <p class="text-xs text-slate-400 font-medium">Procesando <span id="selectedCountDisplay" class="font-bold text-blue-600">0</span> registros seleccionados</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Seleccionar Agente</label>
                    <select name="responsable_id" required class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/10 focus:border-blue-500 p-3 text-xs font-bold text-slate-700 transition-all">
                        <option value="">Seleccione uno...</option>
                        @foreach(\App\Models\Responsable::all() as $resp)
                            <option value="{{ $resp->id }}">{{ $resp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="hiddenSelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-8">
                <button type="button" onclick="closeAssignModal()" class="px-5 py-2.5 text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">Confirmar Operación</button>
            </div>
        </form>
    </div>


    <!-- Quick View Modal (Global) -->
    <div id="quickViewModal" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-white animate-in zoom-in duration-300">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                        <i class="ph-bold ph-user-focus text-xl"></i>
                    </div>
                    <div>
                        <h3 id="quickViewTitle" class="font-black text-slate-800 text-lg uppercase tracking-tight">Expediente</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Vista rápida de auditoría</p>
                    </div>
                </div>
                <button onclick="closeQuickView()" class="w-10 h-10 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-900 transition-all flex items-center justify-center">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>
            <div id="quickViewContent" class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <!-- Content loaded via AJAX -->
                <div class="flex flex-col items-center justify-center py-20 gap-4 opacity-20">
                    <i class="ph-bold ph-circle-notch animate-spin text-5xl"></i>
                    <p class="font-black text-xs uppercase tracking-widest">Cargando expediente...</p>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-3">
                <button onclick="closeQuickView()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all">Cerrar</button>
                <a id="quickViewFullBtn" href="#" class="px-6 py-2.5 bg-slate-900 hover:bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-slate-900/20">Ver Perfil Completo</a>
            </div>
        </div>
    </div>

    <!-- JavaScript optimized for Safesure Pro -->
    <script>
        window.openQuickView = (url, name) => {
            const modal = document.getElementById('quickViewModal');
            const title = document.getElementById('quickViewTitle');
            const content = document.getElementById('quickViewContent');
            const fullBtn = document.getElementById('quickViewFullBtn');
            
            title.innerText = name;
            fullBtn.href = url;
            content.innerHTML = `
                <div class="flex flex-col items-center justify-center py-20 gap-4 opacity-20">
                    <i class="ph-bold ph-circle-notch animate-spin text-5xl"></i>
                    <p class="font-black text-xs uppercase tracking-widest">Cargando expediente...</p>
                </div>`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    // We extract only the relevant part of the show view if possible, 
                    // or just show the whole thing if it's responsive enough.
                    content.innerHTML = doc.body.innerHTML; 
                })
                .catch(err => {
                    content.innerHTML = `<div class="p-10 text-center text-rose-500 font-bold">Error al cargar el expediente: ${err.message}</div>`;
                });
        };

        window.closeQuickView = () => {
            document.getElementById('quickViewModal').classList.add('hidden');
            document.getElementById('quickViewModal').classList.remove('flex');
        };

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const tableContainer = document.getElementById('tableContainer');
            const bulkActionsWrapper = document.getElementById('bulk-actions-wrapper');
            const selectedIds = new Set();
            let timeout = null;

            // Sync visual state of checkboxes
            const syncCheckboxes = () => {
                document.querySelectorAll('.affiliate-checkbox').forEach(cb => {
                    cb.checked = selectedIds.has(cb.value);
                });
                const selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    const viewCheckboxes = document.querySelectorAll('.affiliate-checkbox');
                    selectAll.checked = viewCheckboxes.length > 0 && Array.from(viewCheckboxes).every(cb => cb.checked);
                }
                const floatingCount = document.getElementById('floatingSelectedCount');
                if(floatingCount) floatingCount.innerText = selectedIds.size;
                bulkActionsWrapper.classList.toggle('hidden', selectedIds.size === 0);
                bulkActionsWrapper.classList.toggle('flex', selectedIds.size > 0);
            };

            window.clearSelection = () => {
                selectedIds.clear();
                syncCheckboxes();
            };

            // AJAX Result Fetching with Smooth Transitions
            const fetchResults = (urlParam = null) => {
                const url = urlParam ? new URL(urlParam) : new URL(form.action);
                if (!urlParam) {
                    const searchParams = new URLSearchParams(new FormData(form));
                    url.search = searchParams.toString();
                }

                const tableBody = document.getElementById('tableBody');
                const showSkeletons = () => {
                    const skeletonRow = `
                        <tr class="animate-pulse">
                            <td class="bg-white py-4 px-6 rounded-l-[20px] shadow-sm border-y border-l border-slate-50">
                                <div class="h-4 w-4 bg-slate-50 rounded"></div>
                            </td>
                            <td class="bg-white py-4 px-2 shadow-sm border-y border-slate-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-slate-50 rounded-2xl"></div>
                                    <div class="space-y-2">
                                        <div class="h-3 w-32 bg-slate-50 rounded"></div>
                                        <div class="h-2 w-20 bg-slate-50/50 rounded"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm border-y border-slate-50">
                                <div class="h-3 w-16 bg-slate-50 rounded"></div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm border-y border-slate-50">
                                <div class="h-3 w-24 bg-slate-50 rounded"></div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm border-y border-slate-50 text-center">
                                <div class="h-2 w-12 bg-slate-50 rounded mx-auto"></div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm border-y border-slate-50 text-center">
                                <div class="h-2 w-16 bg-slate-50 rounded mx-auto"></div>
                            </td>
                            <td class="bg-white py-4 px-4 shadow-sm border-y border-slate-50 text-center">
                                <div class="h-4 w-20 bg-slate-50 rounded-full mx-auto"></div>
                            </td>
                            <td class="bg-white py-4 px-6 rounded-r-[20px] shadow-sm border-y border-r border-slate-50">
                                <div class="flex justify-end gap-2">
                                    <div class="h-8 w-8 bg-slate-50 rounded-lg"></div>
                                    <div class="h-8 w-8 bg-slate-50 rounded-lg"></div>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML = skeletonRow.repeat(6);
                };

                showSkeletons();
                tableContainer.classList.add('opacity-70', 'pointer-events-none');
                
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('tableContainer').innerHTML;
                    
                    setTimeout(() => {
                        tableContainer.innerHTML = newTable;
                        tableContainer.classList.remove('opacity-50', 'pointer-events-none');
                        window.history.replaceState({}, '', url);
                        syncCheckboxes();
                        
                        // Re-initialize any Alpine components inside the new table content
                        if (window.Alpine) {
                            window.Alpine.discoverUninitializedComponents();
                        }
                    }, 200);
                });
            };

            // Event Listeners
            form.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 400);
            });

            document.addEventListener('change', (e) => {
                if (e.target.id === 'selectAll') {
                    document.querySelectorAll('.affiliate-checkbox').forEach(cb => {
                        e.target.checked ? selectedIds.add(cb.value) : selectedIds.delete(cb.value);
                    });
                } else if (e.target.classList.contains('affiliate-checkbox')) {
                    e.target.checked ? selectedIds.add(e.target.value) : selectedIds.delete(e.target.value);
                }
                syncCheckboxes();
            });

            // Global access for Modals
            window.openAssignModal = () => {
                if (selectedIds.size === 0) return;
                document.getElementById('selectedCountDisplay').innerText = selectedIds.size;
                const container = document.getElementById('hiddenSelectedInputs');
                container.innerHTML = '';
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'selected[]'; input.value = id;
                    container.appendChild(input);
                });
                document.getElementById('assignModal').classList.remove('hidden');
                document.getElementById('assignModal').classList.add('flex');
            };

            window.closeAssignModal = () => {
                document.getElementById('assignModal').classList.add('hidden');
                document.getElementById('assignModal').classList.remove('flex');
            };

            window.quickAcuse = (uuid, name) => {
                Swal.fire({
                    title: 'Acuse de Recibo',
                    text: `¿Marcar acuse de recibo para ${name}?`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'Sí, confirmar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const f = document.createElement('form');
                        f.method='POST'; f.action=`/afiliados/${uuid}/estado_single`;
                        const csrf = document.createElement('input');
                        csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
                        const est = document.createElement('input');
                        est.type='hidden'; est.name='estado_id'; est.value='11'; // Acuse de Recibo
                        const obs = document.createElement('input');
                        obs.type='hidden'; obs.name='observacion'; obs.value='Acuse de recibo confirmado desde la vista rápida.';
                        f.appendChild(csrf); f.appendChild(est); f.appendChild(obs);
                        document.body.appendChild(f); f.submit();
                    }
                });
            };

            window.quickComplete = (uuid, name) => {
                Swal.fire({
                    title: 'Finalizar Registro',
                    text: `¿Marcar a ${name} como completado?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Sí, finalizar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Logic to submit complete status...
                        const f = document.createElement('form');
                        f.method='POST'; f.action=`/afiliados/${uuid}/estado_single`;
                        const csrf = document.createElement('input');
                        csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
                        const est = document.createElement('input');
                        est.type='hidden'; est.name='estado_id'; est.value='9'; // Completado
                        f.appendChild(csrf); f.appendChild(est);
                        document.body.appendChild(f); f.submit();
                    }
                });
            };
        });
    </script>
@endsection
