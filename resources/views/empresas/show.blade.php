@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-8 animate-fade-in pb-20">
    {{-- Breadcrumbs & Actions --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <a href="{{ route('empresas.index') }}" class="hover:text-primary transition-colors">Empresas</a>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary">{{ $empresa->nombre }}</span>
            </nav>
            <div class="flex items-center gap-4">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">{{ $empresa->nombre }}</h2>
                <div class="px-2.5 py-1 bg-slate-900/5 text-slate-500 border border-slate-900/10 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[14px]">cloud_done</span>
                    Real-time Sync
                </div>
                @if($empresa->es_verificada)
                    <span class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-600 flex items-center justify-center border border-blue-500/20 shadow-sm" title="Empresa Verificada">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                    </span>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('empresas.edit', $empresa) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-50 transition-all hover:shadow-lg shadow-sm">
                <span class="material-symbols-outlined text-sm">edit_note</span>
                Editar Perfil
            </a>
            <button onclick="window.print()" class="w-11 h-11 flex items-center justify-center bg-slate-800 text-white rounded-2xl hover:bg-slate-900 transition-all shadow-lg shadow-slate-200">
                <span class="material-symbols-outlined text-lg">print</span>
            </button>
        </div>
    </div>

    {{-- Main Dashboard Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT COLUMN: Basic Info & Contacts --}}
        <div class="lg:col-span-1 space-y-8">
            {{-- Profile Card --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group">
                <div class="h-2 bg-gradient-to-r from-primary to-secondary"></div>
                <div class="p-8">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center text-primary border border-slate-100 shadow-inner group-hover:scale-105 transition-transform duration-500">
                            <span class="material-symbols-outlined text-4xl">business</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1 font-headline">RNC Contribuyente</p>
                            <p class="text-xl font-black text-slate-700 tracking-tighter">{{ $empresa->rnc ?? 'S/R' }}</p>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($empresa->es_filial)
                                <span class="px-2 py-0.5 bg-purple-50 text-purple-600 text-[0.55rem] font-black uppercase tracking-widest border border-purple-100 rounded-md">Filial</span>
                                @endif
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[0.55rem] font-black uppercase tracking-widest border border-slate-200 rounded-md">{{ $empresa->estado_contacto ?? 'Nuevo' }}</span>
                                
                                @if($empresa->firebase_synced_at)
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[0.55rem] font-black uppercase tracking-widest border border-emerald-100 rounded-md flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[10px]">cloud_sync</span>
                                    Sync: {{ $empresa->firebase_synced_at->diffForHumans() }}
                                </span>
                                @endif
                                
                                {{-- SLA Status Badge --}}
                                @php $sla = $empresa->sla_status; @endphp
                                <span class="px-2 py-0.5 bg-{{ $sla->color }}-50 text-{{ $sla->color }}-600 text-[0.55rem] font-black uppercase tracking-widest border border-{{ $sla->color }}-100 rounded-md flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-{{ $sla->color }}-500 {{ $sla->level === 'good' ? '' : 'animate-ping' }}"></span>
                                    SLA: {{ $sla->message }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-blue-500 shadow-sm border border-slate-50 shrink-0">
                                <span class="material-symbols-outlined text-lg">location_on</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-0.5">Ubicación</p>
                                <p class="text-[0.7rem] font-bold text-slate-700 leading-tight">
                                    {{ $empresa->provinciaRel->nombre ?? 'N/A' }}{{ $empresa->municipioRel ? ', ' . $empresa->municipioRel->nombre : '' }}
                                </p>
                                <p class="text-[0.65rem] text-slate-500 truncate mt-1">{{ $empresa->direccion ?: 'Sin dirección detallada' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-slate-50 shrink-0">
                                <span class="material-symbols-outlined text-lg">call</span>
                            </div>
                            <div>
                                <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-0.5">Central Telefónica</p>
                                <p class="text-[0.75rem] font-black text-slate-700">{{ $empresa->telefono ?? 'Sin Registrar' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- REFERENTE EJECUTIVO (Highlighted Section) --}}
            <div class="bg-slate-900 rounded-[2.5rem] p-1 shadow-2xl shadow-primary/20 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl opacity-50 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="bg-slate-900 rounded-[2.3rem] p-8 relative z-10">
                    <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.3em] mb-8 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        Referente Ejecutivo
                    </h4>

                    @if($empresa->contacto_nombre)
                    <div class="space-y-8">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary/20 to-primary/40 border-4 border-slate-800 flex items-center justify-center text-white mb-4 shadow-xl">
                                <span class="material-symbols-outlined text-4xl">account_circle</span>
                            </div>
                            <h5 class="text-xl font-black text-white leading-tight font-headline tracking-tight">{{ $empresa->contacto_nombre }}</h5>
                            <span class="text-[0.65rem] font-bold text-primary tracking-widest uppercase mt-2 bg-primary/10 px-3 py-1 rounded-full border border-primary/20">
                                {{ $empresa->contacto_puesto ?: 'Representante' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <a href="tel:{{ $empresa->contacto_telefono }}" class="flex items-center gap-4 bg-white/5 p-4 rounded-[1.5rem] border border-white/5 hover:bg-white/10 transition-all group/call">
                                <div class="w-9 h-9 rounded-xl bg-primary/20 flex items-center justify-center text-primary group-hover/call:bg-primary group-hover/call:text-white transition-all">
                                    <span class="material-symbols-outlined text-lg">local_phone</span>
                                </div>
                                <span class="text-sm font-bold text-slate-300 group-hover/call:text-white">{{ $empresa->contacto_telefono ?: 'Sin Teléfono' }}</span>
                            </a>
                            
                            <a href="mailto:{{ $empresa->contacto_email }}" class="flex items-center gap-4 bg-white/5 p-4 rounded-[1.5rem] border border-white/5 hover:bg-white/10 transition-all group/mail">
                                <div class="w-9 h-9 rounded-xl bg-secondary/20 flex items-center justify-center text-secondary group-hover/mail:bg-secondary group-hover/mail:text-white transition-all">
                                    <span class="material-symbols-outlined text-lg">alternate_email</span>
                                </div>
                                <span class="text-sm font-bold text-slate-300 group-hover/mail:text-white truncate">{{ $empresa->contacto_email ?: 'Sin Email' }}</span>
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="py-12 text-center text-slate-500 italic text-sm border border-slate-800 border-dashed rounded-3xl">
                        Información de contacto <br/> no disponible.
                    </div>
                    @endif

                    {{-- Internal Promoter Link --}}
                    <div class="mt-10 pt-8 border-t border-white/5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-slate-500 shadow-inner">
                                <span class="material-symbols-outlined text-xl">shield_person</span>
                            </div>
                            <div>
                                <p class="text-[0.55rem] font-black uppercase text-slate-500 tracking-widest mb-0.5">Asignado Interno</p>
                                <p class="text-xs font-bold text-slate-300">{{ $empresa->promotor->name ?? 'Sistema' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CENTER & RIGHT: Operational & Interactions --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Metrics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Affiliates Summary --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-slate-50 rounded-full group-hover:bg-primary/5 transition-colors"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-4">Volumen de Operación</p>
                            <h3 class="text-5xl font-black text-slate-800 tracking-tighter">{{ number_format($empresa->afiliados_count ?? $empresa->afiliados()->count()) }}</h3>
                            <p class="text-[0.65rem] font-bold text-slate-500 mt-2 uppercase tracking-wide">Afiliados Vinculados</p>
                        </div>
                        <div class="w-16 h-16 rounded-[1.5rem] bg-primary/10 flex items-center justify-center text-primary transform group-hover:rotate-12 transition-transform duration-500">
                            <span class="material-symbols-outlined text-3xl">group</span>
                        </div>
                    </div>
                </div>

                {{-- Performance Card --}}
                @php
                    $completados = collect($statusBreakdown)->where('label', 'Completado')->first()['value'] ?? 0;
                    $totalAfiliados = $empresa->afiliados()->count();
                    $percent = $totalAfiliados > 0 ? round(($completados / $totalAfiliados) * 100) : 0;
                @endphp
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-slate-50 rounded-full group-hover:bg-emerald-500/5 transition-colors"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-4">Índice de Cierre</p>
                            <div class="flex items-baseline gap-1">
                                <h3 class="text-5xl font-black text-slate-800 tracking-tighter">{{ $percent }}</h3>
                                <span class="text-xl font-black text-emerald-500">%</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-[0.65rem] font-bold text-emerald-600 uppercase tracking-wide">Efectividad</span>
                            </div>
                        </div>
                        <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-100/50 flex items-center justify-center text-emerald-600 transform group-hover:-rotate-12 transition-transform duration-500">
                            <span class="material-symbols-outlined text-3xl">task_alt</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HISTORIAL DE INTERACCIONES (Prominent Content Area) --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col min-h-[600px]">
                <div class="p-8 border-b border-slate-50 bg-slate-50/20 flex items-center justify-between">
                    <div>
                        <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Registro de Relaciones</h4>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Historial de Interacciones</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                        <span class="material-symbols-outlined">forum</span>
                    </div>
                </div>


                <div class="p-8 space-y-8 flex-1">
                    {{-- Quick Note Form --}}
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100/50 relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div class="absolute right-0 top-0 w-24 h-24 bg-primary/5 rounded-full blur-2xl -translate-y-1/2 translate-x-1/2"></div>
                        
                        <form action="{{ route('empresas.interaction', $empresa) }}" method="POST" class="relative z-10 flex flex-col md:flex-row gap-4 items-end">
                            @csrf
                            <div class="flex-1 w-full">
                                <label class="block text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-2 pl-1 italic">Agregar Nueva Nota de Gestión</label>
                                <div class="relative overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white focus-within:ring-4 focus-within:ring-primary/5 focus-within:border-primary transition-all group/form">
                                    <div class="flex items-center px-4 pt-1">
                                        <select name="tipo" class="text-[0.65rem] font-black text-primary uppercase tracking-widest bg-primary/5 px-3 py-1 rounded-full border border-primary/10 focus:ring-0 cursor-pointer outline-none hover:bg-primary/10 transition-colors">
                                            <option value="Llamada">Llamada</option>
                                            <option value="Servicio">Servicio</option>
                                            <option value="Reunión">Reunión</option>
                                            <option value="General">Nota</option>
                                        </select>
                                        <div class="w-px h-4 bg-slate-200 mx-3"></div>
                                        <input type="text" name="descripcion" required placeholder="Describe los detalles de la interacción..." 
                                            class="flex-1 py-4 bg-transparent border-0 text-sm font-bold text-slate-700 placeholder:text-slate-300 placeholder:italic focus:ring-0 outline-none">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="h-14 px-8 bg-slate-900 text-white rounded-[1.5rem] font-black text-[0.65rem] uppercase tracking-[0.2em] hover:bg-primary transition-all hover:shadow-xl shadow-lg hover:-translate-y-1 active:scale-95 flex items-center gap-3 group/btn">
                                Registrar Acta
                                <span class="material-symbols-outlined text-lg group-hover/btn:translate-x-1 transition-transform">send</span>
                            </button>
                        </form>
                    </div>

                    @php
                        $timeline = collect($interacciones)->map(fn($item) => (object)[
                            'type' => $item->tipo,
                            'title' => $item->tipo,
                            'description' => $item->descripcion,
                            'user' => $item->user->name ?? 'Sistema',
                            'date' => $item->fecha_contacto,
                            'is_audit' => false
                        ])->merge(
                            collect($auditorias)->map(fn($item) => (object)[
                                'type' => 'AUDIT',
                                'title' => 'Cambio de Sistema',
                                'description' => $item->action . ': ' . $item->details,
                                'user' => $item->user->name ?? 'Sistema',
                                'date' => $item->created_at,
                                'is_audit' => true
                            ])
                        )->sortByDesc('date');
                    @endphp

                    {{-- Timeline Components --}}
                    <div class="relative space-y-6 max-h-[600px] overflow-y-auto pr-4 custom-scrollbar">
                        {{-- Center Line Decoration --}}
                        <div class="absolute left-[20px] top-4 bottom-4 w-px bg-slate-100"></div>

                        @forelse($timeline as $event)
                            <div class="relative flex gap-8 items-start group">
                                {{-- Timeline Marker --}}
                                @php
                                    $markerStyle = $event->is_audit ? ['bg-slate-800 shadow-slate-900/20', 'settings_suggest'] : match($event->type) {
                                        'Llamada' => ['bg-blue-500 shadow-blue-500/30', 'call'],
                                        'Reunión' => ['bg-amber-500 shadow-amber-500/30', 'groups'],
                                        'Servicio' => ['bg-emerald-500 shadow-emerald-500/30', 'support_agent'],
                                        default => ['bg-indigo-500 shadow-indigo-500/30', 'description'],
                                    };
                                @endphp
                                <div class="z-10 w-10 h-10 rounded-2xl {{ $markerStyle[0] }} text-white flex items-center justify-center shadow-lg border-4 border-white transform group-hover:scale-110 transition-transform">
                                    <span class="material-symbols-outlined text-lg">{{ $markerStyle[1] }}</span>
                                </div>

                                {{-- Information Bubble --}}
                                <div @class(['flex-1 border p-5 rounded-[1.5rem] transition-all relative overflow-hidden group-hover:-translate-y-1', 'bg-white border-slate-100 shadow-sm' => !$event->is_audit, 'bg-slate-50 border-slate-200/50 shadow-none opacity-80' => $event->is_audit])>
                                    <div class="relative z-10">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-3">
                                                <span @class(['text-[0.6rem] font-black uppercase tracking-widest px-2 py-0.5 rounded-full border', 'bg-primary/5 text-primary border-primary/10' => !$event->is_audit, 'bg-slate-200 text-slate-500 border-slate-300' => $event->is_audit])>
                                                    {{ $event->title }}
                                                </span>
                                                <span class="text-[0.65rem] font-bold text-on-surface flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-xs text-slate-400">person</span>
                                                    {{ $event->user }}
                                                </span>
                                            </div>
                                            <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">{{ $event->date->format('d M, Y • h:i A') }}</span>
                                        </div>
                                        <p @class(['text-sm font-bold leading-relaxed', 'text-slate-600' => !$event->is_audit, 'text-slate-500 italic font-medium' => $event->is_audit])>
                                            {{ $event->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-20 border-2 border-dashed border-slate-50 rounded-[2.5rem]">
                                <span class="material-symbols-outlined text-3xl text-slate-200">history</span>
                                <p class="text-sm font-bold text-slate-300 italic mt-2">Sin actividad reciente.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- OPERATIONAL NOMINA --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group">
                <div class="p-8 border-b border-slate-50 bg-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined">table_chart</span>
                        </div>
                        <div>
                            <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Información de Campo</h4>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Nómina de Afiliados</h3>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-primary/5 text-primary text-[0.65rem] font-black rounded-lg border border-primary/10 tracking-widest uppercase">
                            {{ $afiliados->total() }} Registros
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-left text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                                <th class="px-8 py-5 text-center text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Documento ID</th>
                                <th class="px-8 py-5 text-center text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Estado de Proceso</th>
                                <th class="px-8 py-5 text-right text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Gestión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($afiliados as $afiliado)
                                <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer group/row" onclick="window.location='{{ route('afiliados.show', $afiliado) }}'">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-bold group-hover/row:bg-primary group-hover/row:text-white transition-all">
                                                {{ substr($afiliado->nombre_completo, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-700 leading-tight">{{ $afiliado->nombre_completo }}</p>
                                                <p class="text-[0.65rem] text-slate-400 mt-1 font-medium italic">Poliza: {{ $afiliado->poliza ?: 'N/D' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="text-[0.7rem] font-black text-slate-600 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 tracking-tighter">{{ $afiliado->cedula }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $badgeInfo = match(strtolower($afiliado->estado?->nombre ?? 'pendiente')) {
                                                'completado' => ['bg-emerald-50 text-emerald-600 border-emerald-100', 'check_circle'],
                                                'entregado' => ['bg-blue-50 text-blue-600 border-blue-100', 'local_shipping'],
                                                'en proceso' => ['bg-amber-50 text-amber-600 border-amber-100', 'sync'],
                                                default => ['bg-slate-50 text-slate-500 border-slate-100', 'hourglass_empty'],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-[0.6rem] font-black uppercase tracking-widest border {{ $badgeInfo[0] }}">
                                            <span class="material-symbols-outlined text-[14px]">{{ $badgeInfo[1] }}</span>
                                            {{ $afiliado->estado->nombre ?? 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 text-slate-400 group-hover/row:bg-primary group-hover/row:text-white transition-all shadow-sm">
                                            <span class="material-symbols-outlined text-lg">chevron_right</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic text-sm">Empresa sin historial operativo en el sistema.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($afiliados->hasPages())
                <div class="px-8 py-6 bg-slate-50/20 border-t border-slate-50 pagination-premium">
                    {{ $afiliados->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    
    @media print {
        .no-print { display: none; }
        .bg-white, .bg-slate-50 { background-color: transparent !important; }
        .shadow-sm, .shadow-xl { box-shadow: none !important; }
        .p-6, .lg:p-10 { padding: 0 !important; }
    }
</style>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const data = @json($statusBreakdown);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    label: 'Afiliados',
                    data: data.map(d => d.value),
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#10b981', // emerald
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#6366f1'  // indigo
                    ],
                    borderRadius: 8,
                    maxBarThickness: 40
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        display: false,
                        grid: { display: false }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 10, weight: 'bold', family: 'Inter' },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
