<div class="px-6 py-6 h-full flex flex-col">
    <!-- App Header -->
    <div class="flex items-center gap-3 text-slate-800 font-black tracking-tight mb-8">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 border border-slate-800 flex items-center justify-center text-white shadow-sm">
            <i class="ph-bold ph-gear-six text-xl"></i>
        </div>
        <div>
            <h2 class="text-sm">Configuración</h2>
            <p class="text-[9px] text-slate-400 uppercase tracking-widest font-bold">Workspace</p>
        </div>
    </div>
    
    <!-- Navigation Links -->
    <nav class="space-y-1 flex-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-500 hover:bg-slate-100 hover:text-slate-700 mb-4">
            <i class="ph-bold ph-house text-lg"></i> 
            Panel Principal
        </a>

        <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 mt-4">Sistema Global</p>
        
        @canany(['manage_users', 'access_admin_panel'])
        <a href="{{ route('usuarios.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('usuarios.*') ? 'bg-slate-100 text-slate-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
            <i class="ph-bold ph-users-three text-lg"></i> 
            Usuarios y Permisos
        </a>
        <a href="{{ route('admin.sync.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.sync.*') ? 'bg-slate-100 text-slate-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
            <i class="ph-bold ph-cloud-arrow-up text-lg"></i> 
            Sincronización Cloud
        </a>
        <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.audit.*') ? 'bg-slate-100 text-slate-800 shadow-sm border border-slate-200' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' }}">
            <i class="ph-bold ph-clipboard-text text-lg"></i> 
            Auditoría
        </a>
        @endcanany
    </nav>
</div>
