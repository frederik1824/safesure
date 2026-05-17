@props(['estado', 'final' => false])

@php
    $id = $estado->id ?? 0;
    $nombre = strtolower($estado->nombre ?? 'pendiente');
    
    // Premium Color Mapping
    $style = match(true) {
        $id == 9 => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'dot' => 'bg-emerald-500'],
        $id == 11 => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'dot' => 'bg-blue-500'],
        in_array($id, [1, 7]) => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100', 'dot' => 'bg-slate-400'],
        in_array($id, [3, 2, 5]) => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'dot' => 'bg-amber-500'],
        default => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-100', 'dot' => 'bg-rose-500'],
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border {$style['bg']} {$style['text']} {$style['border']} shadow-sm"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
    {{ $estado->nombre ?? 'Pendiente' }}
</span>
