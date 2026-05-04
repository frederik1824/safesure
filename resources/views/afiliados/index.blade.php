@extends('layouts.app')
@section('content')
    <div class="p-8 space-y-6">
        <!-- Page Header & Bulk Actions -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">
                    @if(!$segment)
                        Asignaciones <span class="text-primary/50 text-xl font-medium">(Pendientes)</span>
                    @elseif($segment === 'CMD')
                        Afiliados CMD
                    @else
                        Afiliados Otras Empresas
                    @endif
                </h1>
                <p class="text-slate-500 text-sm font-medium mt-1">Gestión y seguimiento del proceso de carnetización.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div id="bulk-actions-wrapper" class="hidden animate-in fade-in zoom-in duration-300 w-full lg:w-auto">
                    <div class="flex items-center justify-around bg-primary/5 p-1 rounded-2xl border border-primary/20 shadow-sm w-full lg:w-auto">
                        <button type="button" onclick="openAssignModal()" class="flex-1 lg:flex-none px-4 py-2.5 text-xs font-black text-primary hover:bg-primary hover:text-white rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="ph-fill ph-user-plus text-lg"></i> Asignar
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openStatusModal()" class="flex-1 lg:flex-none px-4 py-2.5 text-xs font-black text-primary hover:bg-primary hover:text-white rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="ph-fill ph-arrows-counter-clockwise text-lg"></i> Estado
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openCompanyModal()" class="flex-1 lg:flex-none px-4 py-2.5 text-xs font-black text-primary hover:bg-primary hover:text-white rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="ph-fill ph-buildings text-lg"></i> Empresa
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full lg:w-auto">
                    <form action="{{ route('afiliados.sanitize') }}" method="POST" class="contents">
                        @csrf
                        <button type="submit" class="px-4 py-3 bg-white border border-slate-200 hover:border-primary/30 hover:bg-primary/5 text-slate-600 font-bold text-xs rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i class="ph ph-magic-wand text-lg"></i>
                            <span class="hidden sm:inline">Normalizar</span>
                        </button>
                    </form>
                    <a href="{{ route('afiliados.export', request()->all()) }}" class="px-4 py-3 bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg hover:bg-slate-900 transition-all flex items-center justify-center gap-2">
                        <i class="ph ph-download-simple text-lg"></i>
                        <span class="hidden sm:inline">Exportar</span>
                    </a>
                    <a href="{{ route('afiliados.create', ['segment' => $segment]) }}" class="col-span-2 sm:col-span-1 bg-primary text-white px-6 py-3 rounded-xl shadow-xl shadow-primary/20 text-sm font-bold flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="ph ph-plus-circle text-lg"></i> Nuevo Afiliado
                    </a>
                </div>
            </div>
        </div>

        @if(isset($statsPorPeriodo) && $statsPorPeriodo->count() > 0)
        <!-- Panel de Avance por Periodo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($statsPorPeriodo as $stat)
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[0.6rem] font-black uppercase text-slate-400 tracking-wider">{{ $stat->nombre }}</span>
                    <span class="text-xs font-bold text-primary">{{ $stat->porcentaje }}%</span>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-lg font-black text-slate-800">{{ $stat->total }}</p>
                        <p class="text-[0.6rem] text-slate-400 font-bold uppercase">Total</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-amber-600">{{ $stat->pendiente }}</p>
                        <p class="text-[0.6rem] text-slate-400 font-bold uppercase">Pendiente</p>
                    </div>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: {{ $stat->porcentaje }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Filter Bar -->
        <div x-data="{ filtersOpen: false }" class="space-y-4">
            <button @click="filtersOpen = !filtersOpen" class="lg:hidden w-full flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 text-slate-700 font-bold text-sm rounded-2xl border border-slate-200 transition-all active:scale-95">
                <i class="ph ph-sliders text-xl"></i>
                <span x-text="filtersOpen ? 'Ocultar Filtros' : 'Mostrar Filtros de Búsqueda'"></span>
            </button>

            <form id="filterForm" method="GET" action="{{ route(Route::currentRouteName()) }}" 
                  class="bg-white p-6 rounded-2xl flex flex-wrap items-center gap-4 border border-slate-200/60 shadow-sm transition-all duration-300"
                  :class="filtersOpen ? 'flex' : 'hidden lg:flex'">
                
                <div class="w-full lg:flex-1 lg:min-w-[250px] relative">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nombre / Cédula" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium pl-12 pr-4 py-3 focus:ring-4 focus:ring-primary/10 transition-all">
                </div>

                <div class="w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:min-w-[200px] relative">
                    <select name="estado_id" class="w-full appearance-none bg-slate-50 border-slate-200 rounded-xl text-sm font-medium px-4 py-3 pr-10 focus:ring-4 focus:ring-primary/10 transition-all">
                        <option value="">Estado: Todos</option>
                        @foreach(\App\Models\Estado::all() as $est)
                            <option value="{{ $est->id }}" {{ request('estado_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:min-w-[200px] relative">
                    <select name="corte_id" class="w-full appearance-none bg-slate-50 border-slate-200 rounded-xl text-sm font-medium px-4 py-3 pr-10 focus:ring-4 focus:ring-primary/10 transition-all">
                        <option value="">Corte: Todos</option>
                        @foreach(\App\Models\Corte::all() as $c)
                            <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:min-w-[200px] relative">
                    <input type="text" name="rnc_empresa" value="{{ request('rnc_empresa') }}" list="empresas_filter_list" placeholder="Empresa / RNC" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium px-4 py-3 focus:ring-4 focus:ring-primary/10 transition-all">
                    <datalist id="empresas_filter_list">
                        @foreach(\App\Models\Empresa::orderBy('nombre')->get(['nombre', 'rnc']) as $emp)
                            <option value="{{ $emp->rnc }}">{{ $emp->nombre }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:min-w-[120px] relative">
                    <select name="sexo" class="w-full appearance-none bg-slate-50 border-slate-200 rounded-xl text-sm font-medium px-4 py-3 pr-10 focus:ring-4 focus:ring-primary/10 transition-all">
                        <option value="">Sexo: Todos</option>
                        <option value="M" {{ request('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ request('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 w-full lg:w-auto ml-auto">
                    <button type="submit" class="flex-1 lg:flex-none bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/10">
                        <i class="ph ph-funnel text-lg"></i> Filtrar
                    </button>
                    <a href="{{ route(Route::currentRouteName()) }}" class="p-3 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all" title="Limpiar filtros">
                        <i class="ph ph-arrow-counter-clockwise text-xl"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div id="tableContainer" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200/60 transition-all duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse responsive-table">
                    <thead>
                        <tr class="bg-surface-container-high dark:bg-slate-800/50">
                            <th class="py-4 px-6 border-b border-slate-200 dark:border-slate-700">
                                <input id="selectAll" class="rounded text-primary focus:ring-primary border-slate-300 w-4 h-4 cursor-pointer" type="checkbox"/>
                            </th>
                            <th class="py-4 px-2 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('sort') === 'nombre' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Afiliado @if(request('sort') === 'nombre') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'contrato', 'direction' => request('sort') === 'contrato' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Contrato @if(request('sort') === 'contrato') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 w-32">RNC</th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'empresa', 'direction' => request('sort') === 'empresa' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Empresa @if(request('sort') === 'empresa') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">Corte</th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'responsable', 'direction' => request('sort') === 'responsable' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Responsable @if(request('sort') === 'responsable') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 text-center">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'entrega', 'direction' => request('sort') === 'entrega' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center justify-center gap-1 hover:text-primary transition-colors">
                                    Entrega @if(request('sort') === 'entrega') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 text-center">Docs</th>
                            <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'estado', 'direction' => request('sort') === 'estado' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                    Estado @if(request('sort') === 'estado') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-4 px-6 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-50 dark:divide-slate-800/50 transition-opacity duration-300">
                        <!-- Skeleton Rows (Hidden by default) -->
                        <template id="skeleton-row">
                            <tr class="animate-pulse">
                                <td class="py-4 px-6"><div class="w-4 h-4 bg-slate-100 rounded"></div></td>
                                <td class="py-4 px-2">
                                    <div class="space-y-2">
                                        <div class="h-4 bg-slate-100 rounded w-3/4 skeleton"></div>
                                        <div class="h-3 bg-slate-50 rounded w-1/2 skeleton"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-2/3 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-24 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-32 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-16 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-8 bg-slate-50 rounded-full w-24 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-20 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-4 bg-slate-50 rounded w-12 skeleton"></div></td>
                                <td class="py-4 px-4"><div class="h-6 bg-slate-50 rounded-full w-20 skeleton"></div></td>
                                <td class="py-4 px-6 text-right"><div class="h-8 bg-slate-50 rounded-xl w-16 ml-auto skeleton"></div></td>
                            </tr>
                        </template>

                        @forelse($afiliados as $afiliado)
                        <tr class="hover:bg-slate-50/80 transition-all group border-b border-slate-100 last:border-0 dark:border-slate-800">
                            <td class="py-4 px-6 afiliado-cell">
                                <div class="flex items-center gap-3">
                                    <input name="selected[]" value="{{ $afiliado->id }}" class="rounded text-primary focus:ring-primary border-slate-300 w-5 h-5 cursor-pointer affiliate-checkbox" type="checkbox"/>
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-sm font-bold text-slate-800">{{ $afiliado->nombre_completo }}</span>
                                            @if($afiliado->sexo)
                                                <i class="ph-fill {{ $afiliado->sexo === 'M' ? 'ph-gender-male text-blue-500' : 'ph-gender-female text-pink-500' }} text-base"></i>
                                            @endif
                                            @if($afiliado->reasignado)
                                                <span class="px-1.5 py-0.5 bg-rose-50 text-rose-600 rounded-lg text-[9px] font-black uppercase border border-rose-100">REASIGNADO</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-500 font-medium">{{ $afiliado->cedula_formatted }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4" data-label="Contrato">
                                <span class="text-xs font-bold text-slate-700">{{ $afiliado->contrato ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4" data-label="RNC">
                                <span class="text-[0.7rem] font-mono font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200">
                                    {{ $afiliado->empresaModel->rnc ?? ($afiliado->rnc_empresa ?? '----------') }}
                                </span>
                            </td>
                            <td class="py-4 px-4" data-label="Empresa">
                                @if($afiliado->empresa_id)
                                    <a href="{{ route('empresas.show', $afiliado->empresaModel) }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                        <i class="ph-fill ph-building text-base"></i>
                                        {{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}
                                        @if($afiliado->empresaModel?->es_verificada)
                                            <i class="ph-fill ph-seal-check text-blue-500 text-base"></i>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-xs font-medium text-slate-600">{{ $afiliado->empresa ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4" data-label="Corte">
                                <span class="text-xs font-bold text-slate-600">{{ $afiliado->corte->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4" data-label="Responsable">
                                <div class="flex items-center gap-2 justify-end lg:justify-start">
                                    @if($afiliado->responsable)
                                    <span class="text-xs font-bold text-slate-700">{{ $afiliado->responsable->nombre }}</span>
                                    @else
                                    <span class="text-[0.65rem] text-slate-400 italic">Sin asignar</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex flex-col items-center justify-center">
                                    @if($afiliado->fecha_entrega_safesure)
                                        <div class="flex items-center gap-1.5">
                                            @php
                                                $sla = $afiliado->sla_status;
                                                $color = $sla === 'critico' ? 'bg-rose-500' : ($sla === 'alerta' ? 'bg-amber-500' : ($sla === 'completado' ? 'bg-emerald-500' : 'bg-blue-500'));
                                            @endphp
                                            <div class="w-2.5 h-2.5 rounded-full {{ $color }} animate-pulse"></div>
                                            <span class="text-[0.7rem] font-bold text-on-surface">{{ $afiliado->fecha_entrega_safesure->format('d/m/y') }}</span>
                                        </div>
                                        <span class="text-[0.6rem] text-slate-500 uppercase font-black tracking-tighter">{{ $afiliado->dias_transcurridos }} Días</span>
                                    @else
                                        <span class="text-[0.65rem] text-slate-400 italic">No entregado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center" data-label="Estado">
                                <x-status-badge :estado="$afiliado->estado" />
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @php
                                        $hasAcuse = $afiliado->evidenciasAfiliado->where('tipo_documento', 'acuse')->isNotEmpty();
                                    @endphp
                                    
                                    @if(!$hasAcuse)
                                    <button type="button" onclick="quickPhysicalAcuse('{{ $afiliado->id }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-emerald-100 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm hover:shadow-emerald-200" 
                                        title="Acuse Recibido (Físico)">
                                        <i class="ph-bold ph-file-check text-xl"></i>
                                    </button>
                                    @endif

                                    <button type="button" onclick="quickComplete('{{ $afiliado->uuid }}', '{{ addslashes($afiliado->nombre_completo) }}')" 
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm hover:shadow-blue-200" 
                                        title="Marcar Completado">
                                        <i class="ph-bold ph-check-circle text-xl"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">
                                No se encontraron afiliados según los filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex flex-col justify-between border-t border-slate-50 dark:border-slate-800 bg-surface-container-low/50 dark:bg-slate-800/30">
                {{ $afiliados->links() }}
            </div>
        </div>
        
        <!-- Tablero de Resumen (Bento) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Summary Card -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm flex flex-col justify-between border border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[0.65rem] font-black tracking-widest uppercase text-slate-400">Completado Global</span>
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <i class="ph ph-shield-check text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tighter">0%</h3>
                    <p class="text-[0.65rem] text-slate-500 mt-1 font-bold uppercase tracking-tight">Afiliados Validados</p>
                </div>
                <div class="mt-6 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Assignment Pulse -->
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.65rem] font-black tracking-widest uppercase text-slate-400">Carga Operativa</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                        <i class="ph ph-lightning text-xl"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></span>
                            <span class="text-xs font-bold text-slate-600">Asignados Activos</span>
                        </div>
                        <span class="text-xs font-black text-slate-800">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200"></span>
                            <span class="text-xs font-bold text-slate-400">Sin Asignar</span>
                        </div>
                        <span class="text-xs font-black text-slate-400">0</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action CTA Card -->
            <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl text-white relative overflow-hidden group md:col-span-2 lg:col-span-1">
                <div class="absolute right-0 top-0 w-32 h-32 bg-primary/20 blur-3xl rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-black mb-1">Cierre de Documentos</h4>
                        <p class="text-slate-400 text-[0.65rem] font-medium leading-relaxed mb-6">Módulo para carga y validación de acuses físicos.</p>
                    </div>
                    <a href="{{ route('cierre.index') }}" class="w-fit bg-white text-slate-900 px-6 py-3 rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-primary hover:text-white transition-all shadow-lg shadow-white/5">
                        Ir al Módulo
                    </a>
                </div>
                <i class="ph ph-folder-open absolute -bottom-6 -right-6 text-white/5 text-9xl group-hover:scale-110 transition-transform duration-700"></i>
            </div>
        </div>

    </div>

    <!-- Modal para Asignar Responsable -->
    <div id="assignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('afiliados.bulk_assign') }}" id="assignForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Asignar Responsable</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione el responsable a asignar para los afiliados seleccionados (<span id="selectedCountDisplay">0</span>).</p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Responsable</label>
                <select name="responsable_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach(\App\Models\Responsable::all() as $resp)
                        <option value="{{ $resp->id }}">{{ $resp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Aquí se inyectarán los inputs hidden con los IDs seleccionados -->
            <div id="hiddenSelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Asignación</button>
            </div>
        </form>
    </div>

    <!-- Modal para Cambiar Estado Masivo -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('afiliados.bulk_status') }}" id="statusForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Cambiar Estado</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione el nuevo estado para los afiliados seleccionados (<span id="statusCountDisplay">0</span>).</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nuevo Estado</label>
                <select name="estado_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach(\App\Models\Estado::all() as $est)
                        <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Acción Rápida (Opcional)</label>
                <select name="motivo_rapido" class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">-- Personalizar observación --</option>
                    <option value="Documentos recibidos físicamente">Documentos recibidos físicamente</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Observación Adicional (Opcional)</label>
                <textarea name="observacion" rows="2" placeholder="Notas adicionales..." class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm"></textarea>
            </div>

            <div id="hiddenStatusSelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Cambio</button>
            </div>
        </form>
    </div>

    <!-- Modal para Asignar Empresa Masivo -->
    <div id="companyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('afiliados.bulk_company') }}" id="companyForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Asignar Empresa</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione la empresa a asignar para los afiliados seleccionados (<span id="companyCountDisplay">0</span>).</p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Empresa</label>
                <select name="empresa_id" id="bulk_empresa_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione una...</option>
                    @foreach(\App\Models\Empresa::orderBy('nombre')->get() as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nombre }} (RNC: {{ $emp->rnc }})</option>
                    @endforeach
                </select>
            </div>

            <div id="hiddenCompanySelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeCompanyModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Empresa</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const tableBody = document.getElementById('tableBody');
            const tableContainer = document.getElementById('tableContainer');
            const bulkActionsWrapper = document.getElementById('bulk-actions-wrapper');
            let timeout = null;

            // --- Persistencia de Selección ---
            const selectedIds = new Set();
            const ESTADO_COMPLETADO_ID = {{ \App\Models\Estado::where('nombre', 'Completado')->first()->id ?? 9 }};

            function updateBulkActionsVisibility() {
                if (selectedIds.size > 1) {
                    bulkActionsWrapper.classList.remove('hidden');
                } else {
                    bulkActionsWrapper.classList.add('hidden');
                }
            }

            function syncCheckboxes() {
                const checkboxes = document.querySelectorAll('.affiliate-checkbox');
                let allCheckedInView = checkboxes.length > 0;
                
                checkboxes.forEach(cb => {
                    const id = cb.value;
                    if (selectedIds.has(id)) {
                        cb.checked = true;
                    } else {
                        cb.checked = false;
                        allCheckedInView = false;
                    }
                });

                const selectAll = document.getElementById('selectAll');
                if (selectAll) selectAll.checked = allCheckedInView && checkboxes.length > 0;
            }

            function showSkeletons() {
                tableBody.style.opacity = '0';
                setTimeout(() => {
                    tableBody.innerHTML = '';
                    for (let i = 0; i < 8; i++) {
                        tableBody.appendChild(skeletonTemplate.content.cloneNode(true));
                    }
                    tableBody.style.opacity = '1';
                }, 150);
            }

            function fetchResults(urlParam = null) {
                let url;
                if(urlParam) {
                    url = new URL(urlParam);
                } else {
                    url = new URL(form.action);
                    const formData = new FormData(form);
                    const searchParams = new URLSearchParams(formData);
                    url.search = searchParams.toString();
                }

                showSkeletons();

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    setTimeout(() => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('tableContainer').innerHTML;
                        
                        tableContainer.style.opacity = '0';
                        setTimeout(() => {
                            tableContainer.innerHTML = newContent;
                            tableContainer.style.opacity = '1';
                            // Re-apply transitions class to new content
                            document.querySelector('#tableBody')?.classList.add('page-transition');
                            window.history.replaceState({}, '', url);
                            syncCheckboxes();
                        }, 150);
                    }, 300); // Artificial delay for smooth skeleton visibility
                });
            }

            form.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 500); 
            });

            form.addEventListener('change', (e) => {
                if(e.target.tagName === 'SELECT') {
                    clearTimeout(timeout);
                    fetchResults();
                }
            });

            // Handle pagination dynamically
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('nav[role="navigation"] a, .pagination a');
                if (link) {
                    e.preventDefault();
                    fetchResults(link.href);
                }
            });

            // Delegación de eventos para checkboxes (Atrapa cambios en tabla dinámica)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'selectAll') {
                    const checkboxes = document.querySelectorAll('.affiliate-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = e.target.checked;
                        if (cb.checked) selectedIds.add(cb.value);
                        else selectedIds.delete(cb.value);
                    });
                    updateBulkActionsVisibility();
                }

                if (e.target && e.target.classList.contains('affiliate-checkbox')) {
                    const id = e.target.value;
                    if (e.target.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                    
                    syncCheckboxes();
                    updateBulkActionsVisibility();
                }
            });

            // Initialize Tom Select for Bulk Company Modal
            new TomSelect('#bulk_empresa_id', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Escriba para buscar empresa...'
            });
        });

        // --- Acciones de Cambio Masivo ---
        function clearSelection() {
            selectedIds.clear();
            syncCheckboxes();
            updateBulkActionsVisibility();
        }

        function openAssignModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('selectedCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenSelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('assignModal').classList.remove('hidden');
            document.getElementById('assignModal').classList.add('flex');
        }

        function openStatusModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('statusCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenStatusSelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }

        // Acciones Individuales
        function quickComplete(uuid, name) {
            Swal.fire({
                title: 'Finalizar Carnetización',
                html: `<p class="text-sm">¿Estás seguro de marcar como <strong>Completado</strong> a:<br><span class="text-primary font-bold">${name}</span>?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, Completar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/afiliados/${uuid}/estado_single`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    
                    const estado = document.createElement('input');
                    estado.type = 'hidden';
                    estado.name = 'estado_id';
                    estado.value = {{ \App\Models\Estado::where('nombre', 'Completado')->first()->id ?? 9 }};
                    
                    const motivo = document.createElement('input');
                    motivo.type = 'hidden';
                    motivo.name = 'motivo_rapido';
                    motivo.value = 'Finalizado mediante acción rápida desde el listado.';

                    form.appendChild(csrf);
                    form.appendChild(estado);
                    form.appendChild(motivo);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function quickPhysicalAcuse(id, name) {
            Swal.fire({
                title: 'Validación Física',
                html: `<p class="text-sm">¿Confirmas que recibiste físicamente el Acuse de Recibo de:<br><span class="text-primary font-bold">${name}</span>?</p>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, Validar Físicamente',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('evidencias.physical') }}`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                    
                    const afiliadoInput = document.createElement('input');
                    afiliadoInput.type = 'hidden';
                    afiliadoInput.name = 'afiliado_id';
                    afiliadoInput.value = id;
                    
                    const tipoInput = document.createElement('input');
                    tipoInput.type = 'hidden';
                    tipoInput.name = 'tipo_documento';
                    tipoInput.value = 'acuse_recibo';

                    form.appendChild(csrf);
                    form.appendChild(afiliadoInput);
                    form.appendChild(tipoInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function openCompanyModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('companyCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenCompanySelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('companyModal').classList.remove('hidden');
            document.getElementById('companyModal').classList.add('flex');
        }

        function closeCompanyModal() {
            document.getElementById('companyModal').classList.add('hidden');
            document.getElementById('companyModal').classList.remove('flex');
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
            document.getElementById('assignModal').classList.remove('flex');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }
    </script>
@endsection
