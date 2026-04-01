@extends('layouts.app')

@section('header')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <span class="px-3 py-1 bg-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-50 text-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-600 text-[0.6rem] font-black uppercase tracking-widest rounded-full border border-{{ $ruta->estado === 'Abierta' ? 'amber' : ($ruta->estado === 'Cerrada' ? 'emerald' : 'blue') }}-100">
                {{ $ruta->estado }}
            </span>
            <span class="text-slate-300 text-xs font-bold leading-tight uppercase tracking-widest italic opacity-50">/ Despacho #{{ str_pad($ruta->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight italic text-shadow-sm">{{ $ruta->nombre_ruta }}</h2>
        <div class="flex items-center gap-4 text-slate-500 text-xs font-bold mt-2 uppercase tracking-widest">
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">motorcycle</span> {{ $ruta->mensajero->nombre }}</span>
            <span class="text-slate-200">|</span>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $ruta->fecha_programada }}</span>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('safe.rutas.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-200 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Volver al Listado
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Main Route Detail -->
    <div class="lg:col-span-3 space-y-8">
        <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-100">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8 italic">Hoja de Ruta <span class="text-secondary NOT-italic opacity-50">/ Delivery Checklist</span></h3>
            
            <div class="space-y-4">
                @foreach($ruta->afiliados as $index => $a)
                <div class="flex items-center gap-6 p-6 rounded-[2rem] border border-slate-50 {{ $a->pivot->entregado ? 'bg-emerald-50/30' : 'bg-slate-50/30' }} hover:border-secondary transition-all group">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-slate-400 font-black italic relative overflow-hidden group-hover:scale-110 transition-transform">
                        @if($a->pivot->entregado)
                            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    
                    <div class="flex-1 overflow-hidden">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 tracking-tight truncate">{{ $a->nombre_completo }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-tighter">{{ $a->cedula }}</span>
                                    <span class="text-[0.65rem] font-bold text-secondary uppercase tracking-tighter">{{ $a->provincia_nombre }} - {{ $a->municipio_nombre }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($a->pivot->entregado)
                            <div class="text-right flex flex-col items-end">
                                <span class="text-[0.65rem] font-black text-emerald-600 uppercase tracking-widest mb-1">Entregado</span>
                                <span class="text-[0.6rem] font-bold text-slate-400 px-2 py-0.5 bg-white rounded-lg border border-slate-50">{{ \Carbon\Carbon::parse($a->pivot->fecha_entrega_real)->diffForHumans() }}</span>
                            </div>
                        @else
                            <button type="button" onclick="markAsDelivered({{ $a->id }}, '{{ $a->nombre_completo }}')" class="px-5 py-2.5 bg-slate-900 text-white font-black text-[0.65rem] rounded-xl hover:bg-emerald-600 hover:scale-105 transition-all shadow-md shadow-slate-200 uppercase tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                Reportar Entrega
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Stats & Actions Side -->
    <div class="lg:col-span-1 space-y-6">
        @php
            $total = $ruta->afiliados->count();
            $entregados = $ruta->afiliados->where('pivot.entregado', true)->count();
            $porcentaje = $total > 0 ? round(($entregados / $total) * 100) : 0;
        @endphp
        
        <div class="bg-slate-950 p-10 rounded-[2.5rem] shadow-xl relative overflow-hidden text-white flex flex-col justify-between h-auto lg:h-[400px]">
            <div class="absolute inset-0 bg-gradient-to-br from-secondary/10 to-transparent pointer-events-none"></div>
            <div class="relative z-10 flex-col h-full justify-between">
                <div>
                    <span class="p-2 bg-secondary/10 text-secondary rounded-xl material-symbols-outlined mb-6 scale-150 transform inline-block">analytics</span>
                    <h4 class="text-2xl font-black tracking-tight mb-2">Resumen de Operación</h4>
                    <p class="text-slate-400 text-xs font-medium leading-relaxed italic">Seguimiento de cumplimiento de la hoja de ruta despachada.</p>
                </div>

                <div class="space-y-6 mt-auto">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">
                            <span>Cumplimiento</span>
                            <span class="text-secondary">{{ $porcentaje }}%</span>
                        </div>
                        <div class="h-2 bg-white/5 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-secondary rounded-full shadow-lg shadow-secondary/20 transition-all duration-1000" style="width:{{ $porcentaje }}%"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-[0.6rem] font-black text-slate-500 uppercase tracking-widest block mb-1">Entregados</span>
                            <p class="text-2xl font-black text-emerald-400 leading-none">{{ $entregados }}</p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <span class="text-[0.6rem] font-black text-slate-500 uppercase tracking-widest block mb-1">Pendientes</span>
                            <p class="text-2xl font-black text-amber-400 leading-none">{{ $total - $entregados }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-dashed border-slate-200 text-center">
            <span class="material-symbols-outlined text-slate-200 text-4xl mb-2">qr_code_2</span>
            <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest leading-tight">Escanea este código para ver en app móvil</p>
        </div>
    </div>
</div>

<template id="deliveryModal">
    <form id="deliveryForm" class="p-6 space-y-4">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-50">
            <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined">description</span>
            </div>
            <div>
                <h4 id="modalAfiliadoName" class="text-lg font-black text-slate-900 tracking-tight leading-tight"></h4>
                <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">Confirmación de Entrega</p>
            </div>
        </div>
        
        <div>
            <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.1em] mb-2 px-1">Notas de Entrega (Opcional)</label>
            <textarea name="notas" rows="3" placeholder="Ej: Entregado personal en recepción..." class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-secondary transition-all"></textarea>
        </div>

        <p class="text-[0.65rem] text-slate-400 leading-tight italic py-2">
            * Al confirmar, se registrará la ubicación GPS y la fecha actual en el expediente del afiliado.
        </p>

        <div class="pt-4 grid grid-cols-2 gap-3">
            <button type="button" class="swal2-cancel py-4 bg-slate-100 text-slate-600 font-black rounded-2xl text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
            <button type="submit" class="swal2-confirm py-4 bg-slate-900 text-white font-black rounded-2xl text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">Confirmar</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
function markAsDelivered(afiliadoId, nombre) {
    const template = document.getElementById('deliveryModal').content.cloneNode(true);
    const form = template.getElementById('deliveryForm');
    template.getElementById('modalAfiliadoName').innerText = nombre;

    Swal.fire({
        html: form,
        showConfirmButton: false,
        padding: '0',
        width: '450px',
        customClass: {
            container: 'rounded-[3rem]',
            popup: 'rounded-[3rem] overflow-hidden'
        },
        willOpen: () => {
            const currentForm = Swal.getHtmlContainer().querySelector('#deliveryForm');
            const cancelBtn = currentForm.querySelector('.swal2-cancel');
            cancelBtn.onclick = () => Swal.close();

            currentForm.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(currentForm);
                const notes = formData.get('notas');

                const submitForm = document.createElement('form');
                submitForm.method = 'POST';
                submitForm.action = `/safe/rutas/{{ $ruta->id }}/afiliado/${afiliadoId}/update-progress`;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = "{{ csrf_token() }}";
                
                const notesInput = document.createElement('input');
                notesInput.type = 'hidden';
                notesInput.name = 'notas';
                notesInput.value = notes;

                submitForm.appendChild(csrf);
                submitForm.appendChild(notesInput);
                document.body.appendChild(submitForm);
                submitForm.submit();
            };
        }
    });
}
</script>
@endpush
@endsection
