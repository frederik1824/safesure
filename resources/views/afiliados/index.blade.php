@extends('layouts.app')
@section('content')
    <div class="p-8 space-y-6">
        <!-- Page Header & Bulk Actions -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface">
                    @if(!$segment)
                        Módulo de Asignaciones (Pendientes)
                    @elseif($segment === 'CMD')
                        Afiliados CMD (Asignados)
                    @else
                        Afiliados Otras Empresas (Asignados)
                    @endif
                </h1>
                <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión y seguimiento del proceso de carnetización.</p>
            </div>
            <div class="flex items-center gap-3">
                <div id="bulk-actions-wrapper" class="hidden animate-in fade-in zoom-in duration-300">
                    <div class="flex items-center bg-primary/5 p-1 rounded-xl border border-primary/20 shadow-sm">
                        <button type="button" onclick="openAssignModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">person_add</span> Asignar
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openStatusModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">sync</span> Estado
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openCompanyModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">domain</span> Empresa
                        </button>
                    </div>
                </div>
                <form action="{{ route('afiliados.sanitize') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-500 font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-2" title="Normaliza abreviaturas en direcciones (C/ -> Calle, No. -> #)">
                        <span class="material-symbols-outlined text-sm">cleaning_services</span>
                        Normalizar Direcciones
                    </button>
                </form>
                <a href="{{ route('afiliados.export', request()->all()) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg hover:bg-slate-800 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Exportar XLSX
                </a>
                <a href="{{ route('afiliados.create', ['segment' => $segment]) }}" class="bg-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20 text-sm font-semibold flex items-center gap-2 hover:bg-blue-800 transition-colors">
                    <span class="material-symbols-outlined text-lg">add</span> Nuevo
                </a>
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
        <form id="filterForm" method="GET" action="{{ route(Route::currentRouteName()) }}" class="bg-surface-container-low p-4 rounded-xl flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nombre / Cédula" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 focus:ring-2 ring-blue-500/10">
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="estado_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Estado: Todos</option>
                    @foreach(\App\Models\Estado::all() as $est)
                        <option value="{{ $est->id }}" {{ request('estado_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="corte_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Corte: Todos</option>
                    @foreach(\App\Models\Corte::all() as $c)
                        <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>


            <div class="flex-1 min-w-[200px] relative">
                <input type="text" name="rnc_empresa" value="{{ request('rnc_empresa') }}" list="empresas_filter_list" placeholder="Empresa / RNC" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 focus:ring-2 ring-blue-500/10">
                <datalist id="empresas_filter_list">
                    @foreach(\App\Models\Empresa::orderBy('nombre')->get(['nombre', 'rnc']) as $emp)
                        <option value="{{ $emp->rnc }}">{{ $emp->nombre }}</option>
                    @endforeach
                </datalist>
            </div>

            <div class="flex-1 min-w-[120px] relative">
                <select name="sexo" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Sexo: Todos</option>
                    <option value="M" {{ request('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ request('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="lote_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Lote: Todos</option>
                    @foreach(\App\Models\Lote::orderBy('created_at', 'desc')->get() as $lote)
                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[150px] relative">
                <select name="reasignado" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Auditoría: Todos</option>
                    <option value="1" {{ request('reasignado') == '1' ? 'selected' : '' }}>Reasignados</option>
                    <option value="0" {{ request('reasignado') == '0' ? 'selected' : '' }}>Originales</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">repeat</span>
            </div>

            <div class="flex-1 min-w-[150px] relative">
                <select name="company_status" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Empresa: Todos</option>
                    <option value="none" {{ request('company_status') == 'none' ? 'selected' : '' }}>Sin Empresa</option>
                    <option value="assigned" {{ request('company_status') == 'assigned' ? 'selected' : '' }}>Con Empresa</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">domain_disabled</span>
            </div>

            <button type="submit" class="bg-primary text-white p-2.5 rounded-lg hover:bg-primary-container transition-colors">
                <span class="material-symbols-outlined text-xl">search</span>
            </button>
            <a href="{{ route(Route::currentRouteName()) }}" class="bg-surface-container-high text-on-surface-variant p-2.5 rounded-lg hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-xl">clear_all</span>
            </a>
        </form>

        <!-- Table Container -->
        <div id="tableContainer" class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800 transition-opacity duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
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
                            <td class="py-4 px-6">
                                <input name="selected[]" value="{{ $afiliado->id }}" class="rounded text-primary focus:ring-primary border-slate-300 w-4 h-4 cursor-pointer affiliate-checkbox" type="checkbox"/>
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm font-semibold text-on-surface dark:text-slate-200">{{ $afiliado->nombre_completo }}</span>
                                            @if($afiliado->sexo)
                                                <span class="material-symbols-outlined text-[14px] {{ $afiliado->sexo === 'M' ? 'text-blue-500' : 'text-pink-500' }}" title="{{ $afiliado->sexo === 'M' ? 'Masculino' : 'Femenino' }}">
                                                    {{ $afiliado->sexo === 'M' ? 'male' : 'female' }}
                                                </span>
                                            @endif
                                            @if($afiliado->reasignado)
                                                <span class="px-1.5 py-0.5 bg-rose-50 text-rose-500 rounded text-[9px] font-black uppercase tracking-tighter border border-rose-100 flex items-center gap-0.5 shadow-sm" title="Registro Reasignado">
                                                    <span class="material-symbols-outlined text-[11px]">repeat</span> R
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[0.75rem] text-slate-500">{{ $afiliado->cedula }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-bold text-on-surface dark:text-slate-200">{{ $afiliado->contrato ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4 w-32">
                                <span class="text-[0.7rem] font-mono font-bold text-slate-600 bg-slate-100/50 px-2 py-1 rounded border border-slate-200/50">{{ $afiliado->rnc_empresa ?? '----------' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($afiliado->empresa_id)
                                    <a href="{{ route('empresas.show', $afiliado->empresaModel) }}" class="text-xs font-bold text-primary hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-building text-[10px]"></i>
                                        {{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}
                                        @if($afiliado->empresaModel?->es_verificada)
                                            <span class="material-symbols-outlined text-blue-500 text-[14px]" title="Empresa Verificada (Salida Inmediata)">verified_user</span>
                                        @endif
                                        @if($afiliado->empresaModel?->es_real)
                                            <span class="material-symbols-outlined text-emerald-500 text-[14px]" title="Ubicación Real Confirmada">verified</span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-xs font-medium text-on-surface dark:text-slate-200">{{ $afiliado->empresa ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs text-on-surface-variant dark:text-slate-400">{{ $afiliado->corte->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    @if($afiliado->responsable)
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-700">
                                        {{ substr($afiliado->responsable->nombre, 0, 2) }}
                                    </div>
                                    <span class="text-xs text-on-surface dark:text-slate-300">{{ $afiliado->responsable->nombre }}</span>
                                    @else
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-500">?</div>
                                    <span class="text-[0.6875rem] text-slate-400 italic">Sin asignar</span>
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
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @php
                                        $docs = $afiliado->evidenciasAfiliado;
                                        $hasAcuse = $docs->where('tipo_documento', 'acuse_recibo')->count() > 0;
                                        $hasForm = $docs->where('tipo_documento', 'formulario_firmado')->count() > 0;
                                    @endphp
                                    <span class="material-symbols-outlined text-[1.2rem] {{ $hasAcuse ? 'text-primary' : 'text-slate-200' }}" title="Acuse">description</span>
                                    <span class="material-symbols-outlined text-[1.2rem] {{ $hasForm ? 'text-primary' : 'text-slate-200' }}" title="Formulario">assignment_turned_in</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border {{ $afiliado->status_color_class }} uppercase transition-all">
                                    {{ $afiliado->estado->nombre ?? 'Pendiente' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1 transition-opacity">
                                    <a href="{{ route('afiliados.show', $afiliado) }}" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Detalle"><span class="material-symbols-outlined text-[1.25rem]">visibility</span></a>
                                    <a href="{{ route('afiliados.edit', $afiliado) }}" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar"><span class="material-symbols-outlined text-[1.25rem]">edit</span></a>
                                    @if($afiliado->estado?->nombre !== 'Completado')
                                    <button type="button" onclick="quickComplete('{{ $afiliado->uuid }}', '{{ $afiliado->nombre_completo }}')" class="p-2 text-slate-400 hover:text-emerald-500 transition-colors" title="Marcar Completado">
                                        <span class="material-symbols-outlined text-[1.25rem]">check_circle</span>
                                    </button>
                                    @endif
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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Summary Card -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col justify-between border border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[0.6875rem] font-bold tracking-widest uppercase text-slate-400">Completado Global</span>
                        <span class="material-symbols-outlined text-primary-container">fact_check</span>
                    </div>
                    {{-- TODO Lógica de Progreso Dashboard --}}
                    <h3 class="text-3xl font-bold text-primary">0%</h3>
                    <p class="text-xs text-on-surface-variant mt-1">Afiliados con documentación validada.</p>
                </div>
                <div class="mt-6 w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Assignment Pulse -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.6875rem] font-bold tracking-widest uppercase text-slate-400">Pendientes</span>
                    <span class="material-symbols-outlined text-amber-500">bolt</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-medium text-on-surface dark:text-slate-300">Asignados Activos</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-slate-200">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-medium text-on-surface dark:text-slate-300">Sin Asignar</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-slate-200">0</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action CTA Card -->
            <div class="bg-primary-container p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="text-lg font-bold mb-2">Cierre de Documentos</h4>
                    <p class="text-blue-100 text-xs mb-6">Módulo para carga y validación de acuses y formularios físicos.</p>
                    <a href="{{ route('cierre.index') }}" class="bg-white text-primary px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-800 hover:text-white transition-all inline-block">Ir a Módulo</a>
                </div>
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-white/10 text-8xl group-hover:scale-110 transition-transform">folder_zip</span>
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
