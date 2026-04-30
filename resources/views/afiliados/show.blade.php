@extends('layouts.app')
@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto min-h-screen bg-slate-50/50">
    <!-- Breadcrumbs & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <nav class="flex items-center gap-2">
            <a href="{{ route('afiliados.index') }}" class="text-xs text-slate-500 hover:text-primary transition-colors uppercase tracking-wider font-bold">Afiliados</a>
            <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
            <span class="text-xs text-primary uppercase tracking-wider font-extrabold mr-2">Detalle de Perfil</span>
            <div class="px-2.5 py-1 bg-slate-900/5 text-slate-500 border border-slate-900/10 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[14px]">cloud_done</span>
                Real-time Sync
            </div>
        </nav>
        <div class="flex gap-3">
            @if(strtolower($afiliado->estado?->nombre) !== 'completado')
                <button type="button" onclick="document.getElementById('estadoModal').style.display='flex'" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">sync_alt</span>
                    Actualizar Estado
                </button>
                <button type="button" onclick="document.getElementById('reassignModal').style.display='flex'" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">person_add</span>
                    Reasignar
                </button>
                <a href="{{ route('afiliados.edit', $afiliado) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-sm rounded-xl shadow-lg hover:bg-slate-800 transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">edit</span>
                    Editar Perfil
                </a>
            @else
                <div class="flex items-center gap-2">
                    <div class="px-5 py-2.5 bg-emerald-100 text-emerald-800 font-bold text-sm rounded-xl flex items-center gap-2 border border-emerald-200">
                        <span class="material-symbols-outlined text-lg">lock</span>
                        Expediente Completado e Inmutable
                    </div>
                    <button type="button" onclick="document.getElementById('reopenModal').style.display='flex'" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 text-slate-500 font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">history_toggle_off</span>
                        Solicitar Reapertura
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Left Sidebar: Profile Overview -->
        <div class="xl:col-span-4 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 flex flex-col items-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-primary/10 to-transparent"></div>
                
                <div class="w-28 h-28 bg-white p-1 rounded-full shadow-md z-10 mb-4 ring-4 ring-primary/5">
                    <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-4xl font-black text-slate-400">
                        {{ strtoupper(substr($afiliado->nombre_completo, 0, 1)) }}
                    </div>
                </div>

                <div class="z-10 text-center w-full">
                    <h2 class="text-2xl font-extrabold text-slate-800 mb-1">{{ $afiliado->nombre_completo }}</h2>
                    <p class="text-sm font-semibold text-slate-500 mb-4 flex justify-center items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">badge</span> {{ $afiliado->cedula_formatted }}
                    </p>
                    
                    <div class="inline-flex items-center px-4 py-2 border rounded-full {{ $afiliado->status_color_class }} shadow-sm">
                        <div class="w-1.5 h-1.5 rounded-full mr-2 
                            {{ (str_contains($afiliado->status_color_class, 'emerald') || str_contains($afiliado->status_color_class, 'blue')) ? 'bg-current' : 'bg-current animate-pulse' }}"></div>
                        <span class="text-[0.65rem] font-black uppercase tracking-widest">{{ $afiliado->estado?->nombre ?? 'Sin Estado' }}</span>
                    </div>

                    @if($afiliado->firebase_synced_at)
                        <div class="mt-2 text-[0.55rem] font-bold text-slate-400 uppercase tracking-tighter flex items-center justify-center gap-1">
                            <span class="material-symbols-outlined text-[10px]">sync</span>
                            Sincronizado: {{ $afiliado->firebase_synced_at->diffForHumans() }}
                        </div>
                    @endif

                    @if($afiliado->reasignado)
                        <div class="mt-3 inline-flex items-center px-3 py-1 bg-rose-50 text-rose-600 border border-rose-100 rounded-full shadow-sm mx-auto">
                            <span class="material-symbols-outlined text-[14px] mr-1.5">repeat</span>
                            <span class="text-[0.6rem] font-black uppercase tracking-widest">Reasignado para Auditoría</span>
                        </div>
                    @endif
                </div>

                <div class="w-full mt-8 pt-6 border-t border-slate-100 space-y-4 z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                            <span class="material-symbols-outlined text-sm">phone</span>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Teléfono Móvil</p>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $afiliado->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Ubicación</p>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $afiliado->provincia ?? 'N/D' }}, {{ $afiliado->municipio ?? 'N/D' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $afiliado->direccion ?? 'Sin dirección' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insurance & Contract Info -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <h3 class="flex items-center gap-2 font-bold text-slate-800 text-lg mb-6">
                    <span class="material-symbols-outlined text-primary">verified</span> Datos de Cobertura
                </h3>
                
                <div class="space-y-5">
                    <div class="bg-slate-50 rounded-xl p-4 flex items-center justify-between border border-slate-100">
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Empresa / Cliente</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">
                                @if($afiliado->empresa_id)
                                    <a href="{{ route('empresas.show', $afiliado->empresaModel) }}" class="text-primary hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-building text-xs"></i>
                                        {{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}
                                    </a>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $afiliado->empresaModel->rnc ?? $afiliado->rnc_empresa }}</p>
                                @else
                                    {{ $afiliado->empresa ?? 'No aplica' }}
                                    @if($afiliado->rnc_empresa)
                                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $afiliado->rnc_empresa }}</p>
                                    @endif
                                @endif
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-slate-300">work</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Contrato</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">{{ $afiliado->contrato ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Póliza</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">{{ $afiliado->poliza ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bento Stats Summary (Mini) -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary-container p-4 rounded-2xl text-white shadow-sm border border-primary/20">
                    <p class="text-[0.6rem] font-black uppercase tracking-widest opacity-80 mb-1">Días en Proceso</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black">{{ $afiliado->dias_transcurridos }}</span>
                        <span class="text-[0.65rem] font-bold bg-white/20 px-2 py-0.5 rounded-full">SLA</span>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[0.6rem] font-black uppercase tracking-widest text-slate-400 mb-1">Tickets / Notas</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-slate-800">{{ $afiliado->notas->count() }}</span>
                        <span class="material-symbols-outlined text-amber-500 text-lg">sticky_note_2</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Tabs & Details -->
        <div class="xl:col-span-8 flex flex-col gap-8">
            
            <!-- Operations Flow -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-bold text-slate-800 text-lg">Progreso Operativo</h3>
                    <div class="flex items-center gap-2 bg-slate-50 p-1 pl-3 pr-1 rounded-full border border-slate-100 text-xs text-slate-500 font-semibold">
                        Responsable Actual:
                        @if($afiliado->responsable)
                            <span class="bg-white shadow-sm border border-slate-200 px-3 py-1 rounded-full text-slate-700 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-primary">account_circle</span>
                                {{ $afiliado->responsable?->nombre }}
                            </span>
                        @else
                            <span class="bg-white px-3 py-1 rounded-full text-slate-400">Sin asignar</span>
                        @endif
                    </div>
                </div>

                @php
                    $stepIndex = min(intval($afiliado->estado_id), 4); // Fake logic just for visual
                @endphp
                <div class="relative flex justify-between px-4 pb-4">
                    <div class="absolute top-5 left-10 right-10 h-1 bg-slate-100 rounded-full z-0"></div>
                    <div class="absolute top-5 left-10 h-1 bg-gradient-to-r from-primary to-primary-container rounded-full z-0 transition-all duration-1000" style="width: {{ ($stepIndex / 4) * 100 }}%"></div>
                    
                    <!-- Steps -->
                    @php 
                        $steps = ['Registrado', 'En Producción', 'Enviado', 'Entregado', 'Cierre'];
                    @endphp
                    @foreach($steps as $idx => $step)
                    <div class="relative z-10 flex flex-col items-center gap-3 w-20">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 border-white shadow-sm transition-colors duration-500 
                            {{ $idx <= $stepIndex ? 'bg-primary text-white' : 'bg-slate-50 text-slate-300' }}">
                            @if($idx < $stepIndex)
                                <span class="material-symbols-outlined text-sm font-bold">check</span>
                            @elseif($idx == $stepIndex)
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                            @else
                                <span class="text-xs font-bold">{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        <span class="text-[0.65rem] font-bold uppercase tracking-wider text-center {{ $idx <= $stepIndex ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
                        <!-- Documents Section -->
            <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                            <i class="ph-bold ph-folder-open text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Documentación Operativa</h3>
                            <p class="text-xs text-slate-400 font-medium tracking-tight">Soportes requeridos para el cierre del expediente</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $acuse = $afiliado->evidenciasAfiliado->where('tipo_documento', 'acuse_recibo')->first();
                        $formulario = $afiliado->evidenciasAfiliado->where('tipo_documento', 'formulario_firmado')->first();
                    @endphp

                    <!-- Card Acuse -->
                    <div class="group relative overflow-hidden border {{ $acuse ? 'border-blue-100 bg-blue-50/30' : 'border-slate-100 bg-slate-50/50' }} rounded-[24px] p-6 transition-all hover:shadow-xl hover:shadow-blue-900/5">
                        @if($acuse)
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl"></div>
                        @endif
                        
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 {{ $acuse ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'bg-white text-slate-300 border border-slate-100 shadow-sm' }} rounded-2xl flex items-center justify-center transition-all group-hover:scale-110">
                                <i class="ph-bold ph-file-dashed text-2xl"></i>
                            </div>
                            @if($acuse)
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1 bg-white text-blue-700 rounded-full text-[0.6rem] font-black uppercase tracking-widest shadow-sm border border-blue-100">Digitalizado</span>
                                    <span class="text-[0.55rem] font-bold text-blue-400">ID: #{{ $acuse->id }}</span>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-white text-slate-400 rounded-full text-[0.6rem] font-black uppercase tracking-widest shadow-sm border border-slate-100">Requerido</span>
                            @endif
                        </div>

                        <h4 class="font-bold text-slate-800 mb-1 text-base">Acuse de Recibo</h4>
                        <p class="text-[0.75rem] text-slate-500 mb-8 leading-relaxed font-medium">Comprobante oficial de recepción del carnet firmado por el afiliado.</p>
                        
                        @if($acuse)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-white/80 backdrop-blur-md px-4 py-3 rounded-2xl border border-blue-100/50 shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $acuse->status === 'validado' ? 'bg-emerald-500' : ($acuse->status === 'rechazado' ? 'bg-rose-500' : 'bg-amber-500 animate-pulse') }}"></div>
                                        <span class="text-[0.65rem] font-black uppercase tracking-wider text-slate-400">Estado:</span>
                                    </div>
                                    <span class="text-[0.7rem] font-black uppercase {{ $acuse->status === 'validado' ? 'text-emerald-600' : ($acuse->status === 'rechazado' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ $acuse->status ?? 'Recibido' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $acuse->file_path) }}" target="_blank" class="flex-1 py-3 bg-white hover:bg-slate-900 hover:text-white border border-slate-200 text-slate-700 font-black text-[0.65rem] uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <i class="ph-bold ph-eye"></i> Ver Expediente
                                    </a>
                                </div>
                                @if(strtolower($afiliado->estado?->nombre) !== 'completado' && $acuse->status !== 'validado')
                                <form action="{{ route('evidencias.update_status', $acuse->id) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <input type="hidden" name="status" value="validado">
                                    <button type="submit" class="flex-1 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">
                                        Confirmar Validación
                                    </button>
                                </form>
                                @endif
                            </div>
                        @elseif(strtolower($afiliado->estado?->nombre) !== 'completado')
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('afiliados.upload_evidencia', $afiliado) }}" method="POST" enctype="multipart/form-data" class="relative group/upload">
                                    @csrf
                                    <input type="hidden" name="tipo_documento" value="acuse_recibo">
                                    <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                    <div class="w-full py-3 bg-white border-2 border-dashed border-slate-200 text-slate-500 font-black text-[0.65rem] uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 transition-all group-hover/upload:border-blue-400 group-hover/upload:text-blue-600 group-hover/upload:bg-blue-50">
                                        <i class="ph-bold ph-cloud-arrow-up text-lg"></i> Subir Digital
                                    </div>
                                </form>
                                
                                <div class="flex items-center gap-3 py-2">
                                    <div class="flex-1 h-[1px] bg-slate-100"></div>
                                    <span class="text-[0.6rem] font-black text-slate-300 uppercase tracking-widest">Alternativa</span>
                                    <div class="flex-1 h-[1px] bg-slate-100"></div>
                                </div>

                                <form action="{{ route('evidencias.physical') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                                    <input type="hidden" name="tipo_documento" value="acuse_recibo">
                                    <button type="submit" class="w-full py-3 bg-white hover:bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl font-black text-[0.65rem] uppercase tracking-widest flex items-center justify-center gap-2 transition-all shadow-sm">
                                        <i class="ph-bold ph-handshake text-lg"></i> Validación Física en Oficina
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Card Formulario -->
                    <div class="group relative overflow-hidden border {{ $formulario ? 'border-indigo-100 bg-indigo-50/30' : 'border-slate-100 bg-slate-50/50' }} rounded-[24px] p-6 transition-all hover:shadow-xl hover:shadow-indigo-900/5">
                        @if($formulario)
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl"></div>
                        @endif

                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 {{ $formulario ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-300 border border-slate-100 shadow-sm' }} rounded-2xl flex items-center justify-center transition-all group-hover:scale-110">
                                <i class="ph-bold ph-signature text-2xl"></i>
                            </div>
                            @if($formulario)
                                <div class="flex flex-col items-end gap-1">
                                    <span class="px-3 py-1 bg-white text-indigo-700 rounded-full text-[0.6rem] font-black uppercase tracking-widest shadow-sm border border-indigo-100">Registrado</span>
                                    <span class="text-[0.55rem] font-bold text-indigo-400">ID: #{{ $formulario->id }}</span>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-white text-slate-400 rounded-full text-[0.6rem] font-black uppercase tracking-widest shadow-sm border border-slate-100">Pendiente</span>
                            @endif
                        </div>

                        <h4 class="font-bold text-slate-800 mb-1 text-base">Formulario Firmado</h4>
                        <p class="text-[0.75rem] text-slate-500 mb-8 leading-relaxed font-medium">Formulario de registro con huella y firma dactilar del beneficiario.</p>
                        
                        @if($formulario)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between bg-white/80 backdrop-blur-md px-4 py-3 rounded-2xl border border-indigo-100/50 shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $formulario->status === 'validado' ? 'bg-emerald-500' : ($formulario->status === 'rechazado' ? 'bg-rose-500' : 'bg-amber-500 animate-pulse') }}"></div>
                                        <span class="text-[0.65rem] font-black uppercase tracking-wider text-slate-400">Estado:</span>
                                    </div>
                                    <span class="text-[0.7rem] font-black uppercase {{ $formulario->status === 'validado' ? 'text-emerald-600' : ($formulario->status === 'rechazado' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ $formulario->status ?? 'Recibido' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $formulario->file_path) }}" target="_blank" class="flex-1 py-3 bg-white hover:bg-slate-900 hover:text-white border border-slate-200 text-slate-700 font-black text-[0.65rem] uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <i class="ph-bold ph-file-text"></i> Ver Documento
                                    </a>
                                </div>
                                @if(strtolower($afiliado->estado?->nombre) !== 'completado' && $formulario->status !== 'validado')
                                <form action="{{ route('evidencias.update_status', $formulario->id) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <input type="hidden" name="status" value="validado">
                                    <button type="submit" class="flex-1 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">
                                        Validar Registro
                                    </button>
                                </form>
                                @endif
                            </div>
                        @elseif(strtolower($afiliado->estado?->nombre) !== 'completado')
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('afiliados.upload_evidencia', $afiliado) }}" method="POST" enctype="multipart/form-data" class="relative group/upload">
                                    @csrf
                                    <input type="hidden" name="tipo_documento" value="formulario_firmado">
                                    <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf" onchange="this.form.submit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                    <div class="w-full py-3 bg-white border-2 border-dashed border-slate-200 text-slate-500 font-black text-[0.65rem] uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 transition-all group-hover/upload:border-indigo-400 group-hover/upload:text-indigo-600 group-hover/upload:bg-indigo-50">
                                        <i class="ph-bold ph-upload-simple text-lg"></i> Subir Formulario
                                    </div>
                                </form>

                                <div class="flex items-center gap-3 py-2">
                                    <div class="flex-1 h-[1px] bg-slate-100"></div>
                                    <span class="text-[0.6rem] font-black text-slate-300 uppercase tracking-widest">Alternativa</span>
                                    <div class="flex-1 h-[1px] bg-slate-100"></div>
                                </div>

                                <form action="{{ route('evidencias.physical') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                                    <input type="hidden" name="tipo_documento" value="formulario_firmado">
                                    <button type="submit" class="w-full py-3 bg-white hover:bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl font-black text-[0.65rem] uppercase tracking-widest flex items-center justify-center gap-2 transition-all shadow-sm">
                                        <i class="ph-bold ph-stamp text-lg"></i> Recepción Física (Mensajería)
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
         </div>

             <!-- Combined Audit Timeline (Audit Trail & Notes) -->
            <div class="bg-white rounded-[32px] p-8 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg shadow-slate-900/10">
                            <i class="ph-bold ph-clock-counter-clockwise text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Historial Operativo</h3>
                            <p class="text-xs text-slate-400 font-medium tracking-tight">Registro completo de auditoría y seguimiento</p>
                        </div>
                    </div>
                    @if(strtolower($afiliado->estado?->nombre) !== 'completado')
                    <button onclick="document.getElementById('noteSection').scrollIntoView({behavior: 'smooth'})" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-[0.65rem] font-black uppercase tracking-widest flex items-center gap-2 transition-all border border-slate-200/50">
                        <i class="ph-bold ph-note-pencil text-sm"></i> Nueva Nota
                    </button>
                    @endif
                </div>

                <div class="space-y-10 relative before:absolute before:left-[17px] before:top-4 before:bottom-4 before:w-[2px] before:bg-slate-100/80">
                    {{-- Combine notes, history, evidence and creation sorted by date --}}
                    @php
                        $timeline = collect();
                        
                        // 1. Registro Inicial
                        $timeline->push([
                            'type' => 'creation', 
                            'date' => $afiliado->created_at, 
                            'title' => 'Registro de Expediente',
                            'description' => 'El expediente ha sido ingresado al sistema.',
                            'icon' => 'ph-file-plus',
                            'color' => 'slate',
                            'user' => 'Sistema CMD'
                        ]);

                        // 2. Historial de Estados
                        foreach($afiliado->historialEstados as $h) {
                            $timeline->push([
                                'type' => 'status', 
                                'date' => $h->created_at, 
                                'title' => 'Cambio de Estado',
                                'description' => ($h->estadoAnterior?->nombre ?? 'N/D') . ' → ' . ($h->estadoNuevo?->nombre ?? 'N/D'),
                                'obs' => $h->observacion,
                                'icon' => 'ph-git-pull-request',
                                'color' => 'blue',
                                'user' => $h->user->name ?? 'Sistema'
                            ]);
                        }

                        // 3. Notas
                        foreach($afiliado->notas as $n) {
                            $timeline->push([
                                'type' => 'note', 
                                'date' => $n->created_at, 
                                'title' => 'Nota de Seguimiento',
                                'description' => $n->contenido,
                                'icon' => 'ph-chat-circle-dots',
                                'color' => 'amber',
                                'user' => $n->user->name ?? 'Usuario'
                            ]);
                        }

                        // 4. Evidencias
                        foreach($afiliado->evidenciasAfiliado as $e) {
                            $timeline->push([
                                'type' => 'evidence', 
                                'date' => $e->created_at, 
                                'title' => 'Documentación: ' . strtoupper(str_replace('_', ' ', $e->tipo_documento)),
                                'description' => 'Documento marcado como: ' . strtoupper($e->status),
                                'obs' => $e->observaciones,
                                'icon' => 'ph-folder-simple-star',
                                'color' => 'emerald',
                                'user' => $e->user->name ?? 'Validador'
                            ]);
                        }

                        $timeline = $timeline->sortByDesc('date');
                    @endphp

                    @forelse($timeline as $item)
                        <div class="relative pl-12 group animate-in slide-in-from-bottom-2 duration-500">
                            <!-- Timeline Node -->
                            <div class="absolute left-0 top-1.5 w-[36px] h-[36px] rounded-xl bg-white border-2 border-slate-100 flex items-center justify-center z-10 shadow-sm transition-all group-hover:scale-110 group-hover:shadow-md
                                @if($item['color'] === 'blue') border-blue-200 @elseif($item['color'] === 'amber') border-amber-200 @elseif($item['color'] === 'emerald') border-emerald-200 @else border-slate-200 @endif">
                                <i class="{{ $item['icon'] }} text-lg 
                                    @if($item['color'] === 'blue') text-blue-600 @elseif($item['color'] === 'amber') text-amber-600 @elseif($item['color'] === 'emerald') text-emerald-600 @else text-slate-500 @endif">
                                </i>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[0.6rem] font-black uppercase tracking-widest px-2 py-0.5 rounded-full
                                                @if($item['color'] === 'blue') bg-blue-50 text-blue-700 @elseif($item['color'] === 'amber') bg-amber-50 text-amber-700 @elseif($item['color'] === 'emerald') bg-emerald-50 text-emerald-700 @else bg-slate-50 text-slate-700 @endif">
                                                {{ $item['title'] }}
                                            </span>
                                            <span class="text-[0.65rem] font-bold text-slate-300">•</span>
                                            <span class="text-[0.65rem] font-bold text-slate-400">{{ $item['date']->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[0.85rem] font-bold text-slate-800 leading-tight">{{ $item['description'] }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-tighter">{{ $item['date']->format('d/m/Y') }}</p>
                                        <p class="text-[0.6rem] font-bold text-slate-300">{{ $item['date']->format('h:i A') }}</p>
                                    </div>
                                </div>
                                
                                @if(isset($item['obs']) && $item['obs'])
                                    <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100/50 group-hover:bg-white group-hover:border-slate-200 transition-all">
                                        <p class="text-[0.8rem] text-slate-600 font-medium leading-relaxed italic">"{{ $item['obs'] }}"</p>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-5 h-5 rounded-md bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-500 border border-slate-200">
                                        {{ strtoupper(substr($item['user'], 0, 1)) }}
                                    </div>
                                    <span class="text-[0.65rem] font-bold text-slate-400">Por: <span class="text-slate-600">{{ $item['user'] }}</span></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 bg-slate-50/50 rounded-[32px] border-2 border-dashed border-slate-200">
                            <i class="ph-bold ph-ghost text-5xl text-slate-200 mb-4"></i>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Sin registros de historia</p>
                        </div>
                    @endforelse
                </div>

                <!-- Note Form Integrated at Bottom of Timeline -->
                @if(strtolower($afiliado->estado?->nombre) !== 'completado')
                <div id="noteSection" class="mt-16 pt-12 border-t border-slate-100">
                    <div class="bg-slate-900 rounded-[32px] p-8 shadow-2xl shadow-slate-900/20 relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                        
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-white/10 backdrop-blur-md text-white rounded-2xl flex items-center justify-center shadow-inner">
                                <i class="ph-bold ph-plus-circle text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Nueva Nota de Seguimiento</h4>
                                <p class="text-xs text-white/50 font-medium">Registra incidentes o comentarios importantes</p>
                            </div>
                        </div>
                        <form action="{{ route('notas.store') }}" method="POST" class="space-y-5 relative z-10">
                            @csrf
                            <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                            <textarea name="contenido" rows="3" required placeholder="Escribe aquí los detalles del seguimiento..." 
                                class="w-full bg-white/5 border border-white/10 rounded-2xl p-5 text-sm font-medium text-white placeholder-white/30 focus:ring-4 focus:ring-white/5 focus:border-white/20 outline-none transition-all resize-none"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="px-8 py-3.5 bg-white text-slate-900 hover:bg-slate-100 rounded-xl text-[0.7rem] font-black uppercase tracking-widest shadow-xl transition-all flex items-center gap-3 hover:scale-[1.02] active:scale-95">
                                    Publicar Comentario <i class="ph-bold ph-paper-plane-tilt"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Cambio de Estado Individual -->
<div id="estadoModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('afiliados.update_status', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">sync</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800">Actualizar Progreso</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Cambia el estado actual de <strong class="text-slate-700">{{ $afiliado->nombre_completo }}</strong> para reflejar el avance en el proceso operativo.</p>
        
        <div class="space-y-5">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Nuevo Estado</label>
                <select name="estado_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">Seleccione el siguiente paso...</option>
                    @foreach(\App\Models\Estado::all() as $est)
                        @if($est->id != ($afiliado->estado_id ?? 0))
                            <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Acción Rápida (Opcional)</label>
                <select name="motivo_rapido" class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">-- Personalizar observación --</option>
                    <option value="Documentos recibidos físicamente">Documentos recibidos físicamente</option>
                </select>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Observación Adicional (Opcional)</label>
                <textarea name="observacion" rows="3" placeholder="Notas adicionales sobre el cambio..." class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm transition-all outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('estadoModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span> Guardar Estado
            </button>
        </div>
    </form>
</div>
<!-- Modal Reapertura -->
<div id="reopenModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('afiliados.reopen', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">history_toggle_off</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800 uppercase tracking-tighter">Solicitar Reapertura</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Estás intentando abrir un expediente que ya ha sido auditado y cerrado. **Debes justificar esta acción** para el registro de auditoría.</p>
        
        <div class="space-y-5">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Motivo de Auditoría</label>
                <textarea name="motivo" rows="4" required minlength="10" placeholder="Ej: Error en el RNC de la empresa, se requiere subir nueva evidencia..." class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 p-3.5 text-sm transition-all outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('reopenModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 transition-colors shadow-lg shadow-rose-600/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">lock_open</span> Confirmar Reapertura
            </button>
        </div>
    </form>
</div>

<!-- Modal Reasignar Responsable Individual -->
<div id="reassignModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('afiliados.reassign', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-sm border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">person_add</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800">Cambiar Responsable</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Selecciona el nuevo responsable para <strong class="text-slate-700">{{ $afiliado->nombre_completo }}</strong>. Esto quedará registrado en el historial.</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Nuevo Responsable</label>
                <select name="responsable_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">Seleccione un responsable...</option>
                    @foreach(\App\Models\Responsable::all() as $resp)
                        <option value="{{ $resp->id }}" {{ ($afiliado->responsable_id == $resp->id) ? 'selected' : '' }}>
                            {{ $resp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('reassignModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 flex items-center gap-2">
                Actualizar Responsable
            </button>
        </div>
    </form>
</div>
@endsection
