@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Usuarios & Roles</h2>
            <p class="text-slate-500 text-sm mt-1">Configura los permisos granularmente para cada nivel de acceso.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-100">
        <a href="{{ route('usuarios.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-all">
            Usuarios
        </a>
        <a href="{{ route('roles.index') }}" class="px-6 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
            Roles & Permisos
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($roles as $role)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="p-2 bg-blue-50 text-primary rounded-lg">
                        <span class="material-symbols-outlined text-lg">shield</span>
                    </span>
                    <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-300">Nivel de Acceso</span>
                </div>
                <h3 class="text-lg font-black text-slate-700 mb-2">{{ $role->name }}</h3>
                <p class="text-xs text-slate-400 font-medium mb-4 line-clamp-2">
                    Permisos activos: {{ $role->permissions->count() }} de 8.
                </p>
                
                <div class="flex flex-wrap gap-1 mb-6">
                    @forelse($role->permissions->take(3) as $perm)
                        <span class="px-2 py-0.5 bg-slate-50 text-slate-500 rounded text-[0.6rem] font-bold">
                            {{ $perm->name }}
                        </span>
                    @empty
                        <span class="text-[0.65rem] text-slate-300 italic">Sin permisos específicos</span>
                    @endforelse
                    @if($role->permissions->count() > 3)
                        <span class="px-2 py-0.5 bg-slate-50 text-slate-400 rounded text-[0.6rem] font-bold">+{{ $role->permissions->count() - 3 }}</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('roles.edit', $role->id) }}" class="w-full py-2 bg-slate-50 hover:bg-primary hover:text-white text-slate-500 text-xs font-black uppercase tracking-widest rounded-xl transition-all text-center">
                Configurar Permisos
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
