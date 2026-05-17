@extends('layouts.app')

@section('title', 'Safesure Enterprise Hub')

@section('content')
<div class="space-y-10 pb-12 animate-in fade-in duration-700">
    <!-- Header & Greeting -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6">
        <div>
            <h1 class="text-4xl font-display font-black text-slate-900 tracking-tight mb-2">Bienvenido, {{ Auth::user()->name }}</h1>
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]"></span>
                Centro de Operaciones Safesure Enterprise
            </p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
            <div class="px-4 py-2 text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Estado del Sistema</p>
                <p class="text-xs font-black text-slate-800">Sincronización Activa</p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">
                <i class="ph-bold ph-check-circle text-2xl"></i>
            </div>
        </div>
    </div>
    <!-- Executive KPI Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <!-- KPI: Total Afiliados -->
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200/60 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                <i class="ph-bold ph-users text-7xl text-cyan-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Total Afiliados</p>
            <h3 class="text-5xl font-black text-cyan-600 tracking-tighter">{{ number_format($totalAfiliados) }}</h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400">Expedientes Personales</span>
                <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-500" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- KPI: Total Empresas -->
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200/60 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                <i class="ph-bold ph-buildings text-7xl text-purple-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Total Empresas</p>
            <h3 class="text-5xl font-black text-purple-600 tracking-tighter">{{ number_format($totalEmpresas) }}</h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400">Registros Patronales</span>
                <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- KPI: Acuses Recibidos -->
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200/60 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                <i class="ph-bold ph-shield-check text-7xl text-blue-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Acuses Recibidos</p>
            <h3 class="text-5xl font-black text-blue-600 tracking-tighter">{{ number_format($totalAcusesRecibidos) }}</h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="text-[10px] font-bold text-blue-400">{{ $porcentajeAcuses }}% con acuse</span>
                <div class="flex-1 h-1 bg-blue-50 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 shadow-[0_0_8px_#3b82f6]" style="width: {{ $porcentajeAcuses }}%"></div>
                </div>
            </div>
        </div>

        <!-- KPI: Completados -->
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200/60 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:scale-110 transition-transform">
                <i class="ph-bold ph-check-square text-7xl text-emerald-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Completados</p>
            <h3 class="text-5xl font-black text-emerald-600 tracking-tighter">{{ number_format($totalCompletados) }}</h3>
            <div class="mt-6 flex items-center gap-2">
                <span class="text-[10px] font-bold text-emerald-600">{{ $porcentajeCompletado }}% Tasa Éxito</span>
                <div class="flex-1 h-1 bg-emerald-50 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 shadow-[0_0_8px_#10b981]" style="width: {{ $porcentajeCompletado }}%"></div>
                </div>
            </div>
        </div>

        <!-- KPI: Por Liquidar -->
        <div class="bg-slate-900 rounded-[2rem] p-8 border border-slate-800 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                <i class="ph-bold ph-coins text-7xl text-amber-400"></i>
            </div>
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Por Liquidar (ARS)</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">RD$ {{ number_format($montoArs, 0) }}</h3>
            <div class="mt-6">
                <a href="{{ route('liquidacion.index') }}" class="text-[10px] font-black text-amber-400 uppercase tracking-widest hover:underline flex items-center gap-2">
                    Ir a liquidación <i class="ph-bold ph-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- App Grid (Launcher) -->
    <div>
        <div class="flex items-center gap-4 mb-8">
            <div class="h-px flex-1 bg-slate-200"></div>
            <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Aplicaciones y Workspaces</h2>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <!-- App: Admisión -->
            @can('manage_affiliates')
            <a href="{{ route('afiliados.otros') }}" class="group block bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-blue-300 transition-all text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl flex items-center justify-center text-blue-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all shadow-inner border border-blue-200/50">
                    <i class="ph-bold ph-tray text-5xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Admisión</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Gestión de expedientes, importación masiva y validaciones de campo.</p>
            </a>
            @endcan

            <!-- App: Logística -->
            @canany(['manage_logistics', 'manage_closures'])
            <a href="{{ route('logistica.dashboard') }}" class="group block bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-amber-300 transition-all text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-amber-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-amber-50 to-amber-100 rounded-3xl flex items-center justify-center text-amber-600 mb-8 group-hover:scale-110 group-hover:-rotate-3 transition-all shadow-inner border border-amber-200/50">
                    <i class="ph-bold ph-truck text-5xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Logística</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Control de lotes, despachos, mensajeros y cierre operativo de rutas.</p>
            </a>
            @endcanany

            <!-- App: Analíticas -->
            <a href="{{ route('reportes.supervision') }}" class="group block bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-300 transition-all text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-3xl flex items-center justify-center text-emerald-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all shadow-inner border border-emerald-200/50">
                    <i class="ph-bold ph-chart-line-up text-5xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Analíticas</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Monitoreo de SLAs, KPIs ejecutivos y reportes gerenciales en tiempo real.</p>
            </a>

            <!-- App: Traspasos -->
            @can('access_admin_panel')
            <a href="{{ route('traspasos.index') }}" class="group block bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-indigo-300 transition-all text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-3xl flex items-center justify-center text-indigo-600 mb-8 group-hover:scale-110 group-hover:rotate-3 transition-all shadow-inner border border-indigo-200/50">
                    <i class="ph-bold ph-swap text-5xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Traspasos</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Módulo Receptor de Traspasos: consulta de expedientes, filtros por agente y estatus Unipago.</p>
            </a>
            @endcan

            <!-- App: Configuración -->
            @canany(['manage_users', 'access_admin_panel'])
            <a href="{{ route('admin.sync.index') }}" class="group block bg-white rounded-[2.5rem] p-10 border border-slate-200/60 shadow-sm hover:shadow-2xl hover:-translate-y-2 hover:border-slate-400 transition-all text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-slate-900 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="w-24 h-24 mx-auto bg-gradient-to-br from-slate-100 to-slate-200 rounded-3xl flex items-center justify-center text-slate-700 mb-8 group-hover:scale-110 group-hover:-rotate-3 transition-all shadow-inner border border-slate-300/50">
                    <i class="ph-bold ph-gear-six text-5xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Configuración</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Gestión de usuarios, auditoría, sincronización cloud y ajustes del sistema.</p>
            </a>
            @endcanany
        </div>
    </div>
</div>
@endsection
