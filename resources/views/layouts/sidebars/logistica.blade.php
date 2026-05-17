<div class="px-6 py-6 h-full flex flex-col">
    <!-- App Header -->
    <div class="flex items-center gap-3 text-slate-800 font-black tracking-tight mb-8">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
            <i class="ph-bold ph-truck text-xl"></i>
        </div>
        <div>
            <h2 class="text-sm">Logística</h2>
            <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold">Workspace</p>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="space-y-1 flex-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700 mb-4">
            <i class="ph-bold ph-house text-lg"></i> 
            Panel Principal
        </a>

        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-4">Control Operativo</p>
        
        @canany(['manage_logistics', 'manage_closures'])
        <a href="{{ route('logistica.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('logistica.dashboard') ? 'bg-amber-50 text-amber-700 shadow-sm border border-amber-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-chart-line-up text-lg"></i> 
            Monitor Global
        </a>
        <a href="{{ route('lotes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('lotes.*') ? 'bg-amber-50 text-amber-700 shadow-sm border border-amber-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-package text-lg"></i> 
            Control de Lotes
        </a>
        <a href="{{ route('despachos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('despachos.*') ? 'bg-amber-50 text-amber-700 shadow-sm border border-amber-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-paper-plane-tilt text-lg"></i> 
            Despachos
        </a>
        @endcanany

        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-8">Fuerza de Trabajo</p>
        <a href="{{ route('mensajeros.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('mensajeros.*') ? 'bg-amber-50 text-amber-700 shadow-sm border border-amber-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-moped text-lg"></i> 
            Mensajería
        </a>
    </nav>
</div>
