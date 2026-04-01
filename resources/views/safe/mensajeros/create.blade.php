@extends('layouts.app')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight italic text-shadow-sm">Nuevo Mensajero <span class="text-secondary text-2xl NOT-italic opacity-50">/ Registration Form</span></h2>
        <p class="text-slate-500 text-sm mt-1 font-medium">Complete los datos para habilitar el acceso a la App de Entregas.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('safe.mensajeros.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Cancelar
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('safe.mensajeros.store') }}" method="POST" class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-100">
        @csrf
        <div class="space-y-8">
            <!-- Sección Personal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-full">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Nombre Completo</label>
                    <input type="text" name="nombre" required placeholder="Ej: Juan Pérez" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-secondary transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Teléfono / WhatsApp</label>
                    <input type="text" name="telefono" placeholder="809-XXX-XXXX" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-secondary transition-all">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Placa del Vehículo</label>
                    <input type="text" name="vehiculo_placa" placeholder="K000000" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-secondary transition-all text-center uppercase tracking-widest">
                </div>
            </div>

            <div class="h-[1px] bg-slate-50 my-4"></div>

            <!-- Sección de Sistema -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Vincular Usuario Administrativo</label>
                <div class="relative">
                    <select name="user_id" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-secondary appearance-none transition-all">
                        <option value="">-- No vincular por ahora --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
                <p class="text-[0.65rem] text-slate-400 mt-2 font-medium italic">Permite al mensajero iniciar sesión en la plataforma de Gestión.</p>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full py-4 bg-slate-900 text-white font-black rounded-2xl shadow-xl shadow-slate-200 hover:bg-slate-800 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">save</span>
                    Guardar y Habilitar Mensajero
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
