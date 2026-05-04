@props(['estado', 'final' => false])

@php
    $nombre = strtolower($estado->nombre ?? 'pendiente');
    
    $classes = match(true) {
        ($estado->es_final ?? $final) => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        $nombre === 'pendiente' => 'bg-amber-100 text-amber-700 border-amber-200',
        in_array($nombre, ['carnet entregado', 'entregado']) => 'bg-blue-100 text-blue-700 border-blue-200',
        $nombre === 'cancelado' => 'bg-rose-100 text-rose-700 border-rose-200',
        $nombre === 'en proceso' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 rounded-full text-[0.65rem] font-black uppercase tracking-widest border {$classes}"]) }}>
    {{ $estado->nombre ?? 'Pendiente' }}
</span>
