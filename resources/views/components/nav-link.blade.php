@props(['route', 'icon', 'label', 'query' => null])

@php
    $fullRoute = $query ? route($route) . '?' . $query : route($route);
    $isActive = request()->routeIs($route) || (request()->fullUrl() == $fullRoute);
@endphp

<a href="{{ $fullRoute }}" 
   class="{{ $isActive ? 'text-white font-black bg-white/10 border-l-[3px] border-primary shadow-inner' : 'text-slate-500 hover:text-white hover:bg-white/5' }} flex items-center gap-3 px-4 py-3 rounded-r-2xl transition-all group/link mt-0.5 relative overflow-hidden">
    @if($isActive)
        <div class="absolute inset-0 bg-gradient-to-r from-primary/20 via-transparent to-transparent"></div>
    @endif
    <i class="{{ $icon }} text-lg {{ $isActive ? 'text-primary' : 'text-slate-600 group-hover/link:text-slate-300' }} transition-colors relative z-10"></i>
    <span class="text-[0.65rem] tracking-[0.1em] uppercase font-black relative z-10">{{ $label }}</span>
</a>
