<div class="px-6 py-6 h-full flex flex-col">
    <!-- App Header -->
    <div class="flex items-center gap-3 text-slate-800 font-black tracking-tight mb-8">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 flex items-center justify-center text-blue-600 shadow-sm">
            <i class="ph-bold ph-tray text-xl"></i>
        </div>
        <div>
            <h2 class="text-sm">Admisión</h2>
            <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold">Workspace</p>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="space-y-1 flex-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700 mb-4">
            <i class="ph-bold ph-house text-lg"></i> 
            Panel Principal
        </a>

        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-4">Gestión Principal</p>
        
        @can('manage_affiliates')
        <a href="{{ route('afiliados.otros') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('afiliados.otros', 'afiliados.create', 'afiliados.edit') ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-users text-lg"></i> 
            Expedientes
        </a>
        <a href="{{ route('import.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('import.*') ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-file-csv text-lg"></i> 
            Importación Masiva
        </a>
        <a href="{{ route('afiliados.salida_inmediata') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('afiliados.salida_inmediata') ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <div class="flex items-center gap-3">
                <i class="ph-bold ph-lightning text-lg"></i> 
                Salida Inmediata
            </div>
            @php $countSalida = \App\Models\Afiliado::whereHas('empresaModel', function($q) { $q->where('es_verificada', true); })->whereNull('fecha_entrega_safesure')->count(); @endphp
            @if($countSalida > 0) 
                <span class="bg-rose-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-black animate-pulse">{{ $countSalida }}</span> 
            @endif
        </a>
        @endcan
        
        @can('manage_companies')
        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-8">Catálogos</p>
        <a href="{{ route('empresas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('empresas.*') ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-buildings text-lg"></i> 
            Empresas B2B
        </a>
        @endcan
    </nav>
</div>
