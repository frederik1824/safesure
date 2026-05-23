<div class="px-6 py-6 h-full flex flex-col">
    <!-- App Header -->
    <div class="flex items-center gap-3 text-slate-800 font-black tracking-tight mb-8">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-600 shadow-sm">
            <i class="ph-bold ph-swap text-xl"></i>
        </div>
        <div>
            <h2 class="text-sm">Traspasos</h2>
            <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold">Workspace</p>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="space-y-1 flex-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700 mb-4">
            <i class="ph-bold ph-house text-lg"></i> 
            Panel Principal
        </a>

        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-4">Gestión de Traspasos</p>
        
        @php
            $currentView = request()->query('view', 'list');
        @endphp

        <a href="{{ route('traspasos.index', ['view' => 'list']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ ($currentView === 'list' && request()->routeIs('traspasos.*')) ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-swap text-lg"></i> 
            Workspace de Traspasos
        </a>

        <a href="{{ route('traspasos.index', ['view' => 'sync']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ ($currentView === 'sync' && request()->routeIs('traspasos.*')) ? 'bg-indigo-50 text-indigo-700 shadow-sm border border-indigo-100/50' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">
            <i class="ph-bold ph-activity text-lg"></i> 
            Telemetría & Sync Engine
        </a>
    </nav>
</div>
