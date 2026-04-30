@extends('layouts.app')

@section('content')
<div class="page-transition">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Centro de Control de Sistema</h1>
        <p class="text-slate-500 font-medium">Gestión de mantenimiento y despliegue del VPS (DevOps Dashboard)</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover-card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-primary">
                    <i class="ph ph-cpu text-2xl"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Motor PHP / Laravel</p>
                    <p class="text-lg font-black text-slate-800">{{ $stats['php_version'] }} / {{ $stats['laravel_version'] }}</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-primary w-full opacity-20"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover-card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="ph ph-database text-2xl"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Total Afiliados / Colas</p>
                    <p class="text-lg font-black text-slate-800">{{ number_format($stats['total_afiliados']) }} / {{ $stats['pending_jobs'] }} Pendientes</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-600 w-full opacity-20"></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover-card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="ph ph-arrows-clockwise text-2xl"></i>
                </div>
                <div>
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Última Sincronización</p>
                    <p class="text-lg font-black text-slate-800">{{ $stats['last_sync'] }}</p>
                </div>
            </div>
            <div class="h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-amber-600 w-full opacity-20"></div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl overflow-hidden mb-10">
        <div class="p-8 border-b border-slate-50 bg-slate-50/50">
            <h2 class="text-xl font-black text-slate-800 flex items-center gap-3">
                <i class="ph ph-terminal-window text-2xl text-primary"></i>
                Comandos de Mantenimiento
            </h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Estas acciones afectan directamente la ejecución en el servidor Docker.</p>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Migrate -->
            <div class="flex items-start gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:border-primary/20">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-primary">
                    <i class="ph ph-database text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-black text-slate-800 mb-1">Migrar Base de Datos</h3>
                    <p class="text-[0.75rem] text-slate-500 leading-relaxed mb-4">Ejecuta <code>migrate --force</code>. Úselo después de un despliegue con cambios en tablas.</p>
                    <form action="{{ route('admin.system.run') }}" method="POST" onsubmit="return confirmActionForm(event, '¿Ejecutar Migraciones?', 'Esta acción modificará la estructura de la base de datos.')">
                        @csrf
                        <input type="hidden" name="command" value="migrate">
                        <button type="submit" class="bg-primary text-white text-[0.7rem] font-black uppercase tracking-widest px-6 py-2.5 rounded-full hover:bg-slate-800 transition-all shadow-lg shadow-primary/20">
                            Ejecutar Migraciones
                        </button>
                    </form>
                </div>
            </div>

            <!-- Optimize -->
            <div class="flex items-start gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:border-indigo-200">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-indigo-600">
                    <i class="ph ph-lightning text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-black text-slate-800 mb-1">Optimizar Caché</h3>
                    <p class="text-[0.75rem] text-slate-500 leading-relaxed mb-4">Ejecuta <code>optimize</code>. Refresca rutas, configuración y vistas para máxima velocidad.</p>
                    <form action="{{ route('admin.system.run') }}" method="POST" onsubmit="return confirmActionForm(event, '¿Optimizar Sistema?', 'Esto regenerará la caché de rutas y configuración.')">
                        @csrf
                        <input type="hidden" name="command" value="optimize">
                        <button type="submit" class="bg-indigo-600 text-white text-[0.7rem] font-black uppercase tracking-widest px-6 py-2.5 rounded-full hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                            Refrescar Caché
                        </button>
                    </form>
                </div>
            </div>

            <!-- Queue Restart -->
            <div class="flex items-start gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:border-amber-200">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-amber-600">
                    <i class="ph ph-arrows-clockwise text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-black text-slate-800 mb-1">Reiniciar Workers</h3>
                    <p class="text-[0.75rem] text-slate-500 leading-relaxed mb-4">Ejecuta <code>queue:restart</code>. Obliga a los procesos de fondo a cargar el nuevo código.</p>
                    <form action="{{ route('admin.system.run') }}" method="POST" onsubmit="return confirmActionForm(event, '¿Reiniciar Workers?', 'Esto detendrá los procesos de cola actuales para que carguen el nuevo código.')">
                        @csrf
                        <input type="hidden" name="command" value="queue-restart">
                        <button type="submit" class="bg-amber-600 text-white text-[0.7rem] font-black uppercase tracking-widest px-6 py-2.5 rounded-full hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20">
                            Reiniciar Workers
                        </button>
                    </form>
                </div>
            </div>

            <!-- Clear All -->
            <div class="flex items-start gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-100 transition-all hover:border-rose-200">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-rose-600">
                    <i class="ph ph-trash text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-black text-slate-800 mb-1">Limpiar Todo (Cache)</h3>
                    <p class="text-[0.75rem] text-slate-500 leading-relaxed mb-4">Ejecuta <code>cache:clear</code>. Elimina todos los datos temporales guardados en RAM.</p>
                    <form action="{{ route('admin.system.run') }}" method="POST" onsubmit="return confirmActionForm(event, '¿Limpiar Caché?', 'Esto puede ralentizar el sistema momentáneamente mientras se regenera.')">
                        @csrf
                        <input type="hidden" name="command" value="clear-cache">
                        <button type="submit" class="bg-rose-600 text-white text-[0.7rem] font-black uppercase tracking-widest px-6 py-2.5 rounded-full hover:bg-rose-700 transition-all shadow-lg shadow-rose-600/20">
                            Limpiar Caché
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Output Console (Visible only after command execution) -->
    @if(session('success'))
    <div class="bg-slate-900 rounded-[2rem] border border-slate-800 shadow-2xl overflow-hidden mb-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-white/5">
            <div class="flex items-center gap-3">
                <div class="flex gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                </div>
                <span class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 ml-2">Salida del Servidor</span>
            </div>
            <span class="text-[0.6rem] font-bold text-emerald-500/50">STDOUT: SUCCESS</span>
        </div>
        <div class="p-8 font-mono text-sm leading-relaxed text-emerald-400/90 max-h-64 overflow-y-auto custom-scrollbar bg-black/20">
            <div class="flex gap-4">
                <span class="text-slate-600">admin@safesure:~$</span>
                <div class="flex-1">
                    <p class="whitespace-pre-wrap">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- VPS Info Card -->
    <div class="bg-slate-900 rounded-[2rem] p-10 text-white relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                <i class="ph ph-info text-blue-400"></i>
                Nota sobre el entorno Docker
            </h2>
            <p class="text-slate-400 max-w-2xl leading-relaxed mb-6">
                Al ejecutar estos comandos desde el panel, Laravel los dispara dentro de su propio contenedor aislado. 
                Esto garantiza que no necesites acceso SSH manual para las tareas rutinarias de despliegue. 
                <strong>Dokploy</strong> supervisa que el proceso PHP tenga los permisos necesarios para modificar su propio entorno.
            </p>
            <div class="flex gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-full border border-white/10">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest">Contenedor Activo</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/5 rounded-full border border-white/10">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest">Hostname: {{ gethostname() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
