<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary/10 text-primary rounded-2xl">
                <span class="material-symbols-outlined">design_services</span>
            </div>
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tighter">Guía de Estilo Enterprise Core</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Línea Gráfica Estandarizada</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-12">
        {{-- Indicadores de KPI --}}
        <section>
            <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Métricas y KPIs (`x-ui.stats-card`)</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-ui.stats-card icon="groups" label="Total Afiliados" value="1,280" color="blue" trend="+12%" />
                <x-ui.stats-card icon="business" label="Empresas" value="45" color="indigo" />
                <x-ui.stats-card icon="cancel" label="Pendientes" value="18" color="rose" trend="-5%" trendType="down" />
                <x-ui.stats-card icon="done_all" label="Completados" value="95%" color="emerald" />
            </div>
        </section>

        {{-- Estructura de Contenedores --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-8">
                <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">Contenedores (`x-ui.card`)</h4>
                <x-ui.card title="Título del Card" description="Subtítulo Informativo">
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Este es un card estándar con el radio de borde `2.5rem` definido para la línea gráfica. 
                        Incluye un encabezado sombreado y padding generoso de `p-8`.
                    </p>
                    <x-slot name="footer">
                        <div class="flex justify-end gap-3">
                            <button class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Cancelar</button>
                            <button class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-lg shadow-primary/20 hover:scale-105 transition-all">Acción Principal</button>
                        </div>
                    </x-slot>
                </x-ui.card>
            </div>

            <div class="space-y-8">
                <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">Etiquetas y Badges (`x-ui.badge`)</h4>
                <x-ui.card title="Estados del Sistema">
                    <div class="flex flex-wrap gap-3">
                        <x-ui.badge color="primary" label="Activo" />
                        <x-ui.badge color="emerald" label="Completado" />
                        <x-ui.badge color="amber" label="En Proceso" />
                        <x-ui.badge color="rose" label="Cancelado" />
                        <x-ui.badge color="indigo" label="Revisión" />
                        <x-ui.badge color="slate" label="Borrador" />
                    </div>
                </x-ui.card>
            </div>
        </section>

        {{-- Tipografía y Guía Visual --}}
        <section>
            <x-ui.card>
                <x-slot name="header">
                    <div class="flex items-center justify-between w-full">
                        <h3 class="font-black text-xl text-slate-900 tracking-tighter">Especificaciones de Diseño</h3>
                        <x-ui.badge color="indigo" label="Enterprise Core v1.0" />
                    </div>
                </x-slot>
                <div class="prose prose-slate max-w-none">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <span class="block text-[0.6rem] font-black text-slate-400 uppercase mb-2">Tipografía Principal</span>
                            <h1 class="text-4xl font-black text-slate-900 tracking-tighter">AaBbCc 123</h1>
                            <p class="text-xs text-slate-500 mt-2 font-medium">Inter / Outfit - 900 Black</p>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-slate-400 uppercase mb-2">Sombras Dinámicas</span>
                            <div class="flex gap-4">
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-50"></div>
                                <div class="w-12 h-12 bg-white rounded-2xl shadow-xl shadow-slate-200/50"></div>
                            </div>
                            <p class="text-[0.65rem] text-slate-500 mt-2 font-medium">Subtle (Base) vs Elevated (Hover)</p>
                        </div>
                        <div>
                            <span class="block text-[0.6rem] font-black text-slate-400 uppercase mb-2">Radio de Curvatura</span>
                            <div class="w-full h-12 bg-slate-900 rounded-[2.5rem]"></div>
                            <p class="text-[0.65rem] text-slate-500 mt-2 font-medium">Standard Corner: 2.5rem / 40px</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </section>
    </div>
</x-app-layout>
