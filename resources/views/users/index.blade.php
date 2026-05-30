@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Usuarios & Roles</h2>
            <p class="text-slate-500 text-sm mt-1">Administra los accesos y roles del personal del sistema.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20 text-sm font-semibold flex items-center gap-2 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">person_add</span> Nuevo Usuario
        </a>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-100">
        <a href="{{ route('usuarios.index') }}" class="px-6 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
            Usuarios
        </a>
        <a href="{{ route('roles.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Roles & Permisos
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-wrap gap-4 items-center justify-between">
        <form method="GET" action="{{ route('usuarios.index') }}" class="flex items-center gap-3 w-full md:w-auto">
            <span class="material-symbols-outlined text-slate-400">filter_list</span>
            <label for="responsable_id" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filtrar por Entidad / Responsable:</label>
            <select name="responsable_id" id="responsable_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                <option value="">Todos los Usuarios</option>
                <option value="safe" {{ request('responsable_id') === 'safe' ? 'selected' : '' }}>Solo SAFESURE (Gestores)</option>
                <option value="cmd" {{ request('responsable_id') === 'cmd' ? 'selected' : '' }}>Solo ARS CMD (Externos)</option>
                <optgroup label="Responsable Asignado">
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                    @endforeach
                </optgroup>
            </select>
        </form>
        
        <div class="text-xs font-semibold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
            Total: <span class="font-bold text-slate-700">{{ $users->count() }}</span> usuario(s)
        </div>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Usuario</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Rol</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Responsable</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-primary font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700">{{ $user->name }}</span>
                                <span class="text-xs text-slate-400 font-medium">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @foreach($user->getRoleNames() as $role)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[0.65rem] font-bold uppercase tracking-wider">
                            {{ $role }}
                        </span>
                        @endforeach
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600 font-medium">
                        {{ $user->responsable->nombre ?? 'N/A' }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('usuarios.edit', $user) }}" class="p-2 text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('usuarios.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete_forever</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
