@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-slate-900/20">
                <span class="material-symbols-outlined">history</span>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">Auditoría del Sistema</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Historial de cambios y actividad global</p>
            </div>
        </div>

        {{-- Filtros Rápidos --}}
        <form action="{{ route('admin.audit.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="user_id" class="bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:ring-primary/20 focus:border-primary px-4 py-2 opacity-80 hover:opacity-100 transition-opacity">
                <option value="">Todos los Usuarios</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            
            <select name="event" class="bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:ring-primary/20 focus:border-primary px-4 py-2 opacity-80 hover:opacity-100 transition-opacity">
                <option value="">Eventos</option>
                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Creado</option>
                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Editado</option>
                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
            </select>

            <button type="submit" class="p-2 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-md">
                <span class="material-symbols-outlined text-sm">search</span>
            </button>
            
            @if(request()->anyFilled(['user_id', 'event', 'model']))
                <a href="{{ route('admin.audit.index') }}" class="p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span>
                </a>
            @endif
        </form>
    </div>

    {{-- Timeline Content --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Sujeto / Acción</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Modelo Afectado</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Cambios Realizados</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Fecha y Origen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 overflow-hidden border-2 border-white shadow-sm">
                                        @if($log->user?->avatar)
                                            <img src="{{ Storage::url($log->user->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-bold text-xs uppercase">{{ substr($log->user?->name ?? '?', 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 leading-tight">{{ $log->user?->name ?? 'Usuario Desconocido' }}</span>
                                        <span class="flex items-center gap-1.5 mt-1">
                                            @php
                                                $badgeClass = match($log->event) {
                                                    'created' => 'bg-emerald-100 text-emerald-700 font-black',
                                                    'updated' => 'bg-amber-100 text-amber-700 font-black',
                                                    'deleted' => 'bg-rose-100 text-rose-700 font-black',
                                                    default => 'bg-slate-100 text-slate-700 font-black'
                                                };
                                                $eventLabel = match($log->event) {
                                                    'created' => 'CREACIÓN',
                                                    'updated' => 'EDICIÓN',
                                                    'deleted' => 'ELIMINACIÓN',
                                                    default => strtoupper($log->event)
                                                };
                                            @endphp
                                            <span class="text-[0.6rem] px-2 py-0.5 rounded-full {{ $badgeClass }} tracking-wider">
                                                {{ $eventLabel }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ class_basename($log->model_type) }}</span>
                                    <span class="text-[0.7rem] font-bold text-primary mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">fingerprint</span> ID: {{ $log->model_id }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="max-w-md">
                                    @if($log->event === 'updated')
                                        <div class="flex flex-col gap-2">
                                            @foreach($log->new_values as $key => $newValue)
                                                @php
                                                    $oldValue = $log->old_values[$key] ?? 'N/A';
                                                    if (is_array($newValue)) $newValue = json_encode($newValue);
                                                    if (is_array($oldValue)) $oldValue = json_encode($oldValue);
                                                @endphp
                                                <div class="text-[0.7rem] leading-relaxed">
                                                    <span class="font-black text-slate-500 uppercase tracking-tighter mr-1">{{ $key }}:</span>
                                                    <span class="line-through text-slate-400 opacity-60">{{ Str::limit($oldValue, 30) }}</span>
                                                    <span class="mx-1 text-primary">→</span>
                                                    <span class="font-bold text-slate-800">{{ Str::limit($newValue, 50) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($log->event === 'created')
                                        <div class="text-[0.7rem] text-emerald-600 font-bold italic">
                                            Registro inicial creado con éxito.
                                        </div>
                                    @else
                                        <div class="text-[0.7rem] text-rose-600 font-bold italic">
                                            Vaciado de registro (Eliminación).
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-end text-right">
                                    <span class="text-sm font-bold text-slate-700">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="text-[0.6rem] font-medium text-slate-400 mt-1 flex items-center gap-1 group-hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[10px]">public</span> {{ $log->ip_address }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">No se encontraron logs</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
