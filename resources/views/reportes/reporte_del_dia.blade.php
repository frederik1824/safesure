@extends('layouts.app')

@section('content')
<style>
    @media print {
        header, aside, .no-print, button, form, .filters-container, nav {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            font-family: 'Inter', sans-serif !important;
        }
        .print-container {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            box-shadow: none !important;
            border: none !important;
        }
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 11px !important;
        }
        th, td {
            border: 1px solid #cbd5e1 !important;
            padding: 6px 8px !important;
            text-align: left !important;
        }
        th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: bold !important;
        }
        @page {
            size: letter portrait;
            margin: 1.2cm;
        }
    }
</style>

<div class="space-y-6 print-container animate-page-transition">
    <!-- Top toolbar / Date Selector (Hidden on print) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm no-print">
        <div>
            <h1 class="text-2xl font-display font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-indigo-600">print</span>
                Reporte del Día (Entregas a CMD)
            </h1>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Listado diario oficial de expedientes entregados y completados</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('reportes.del_dia') }}" class="flex items-center gap-2">
                <label for="fecha" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha de Entrega:</label>
                <input type="date" name="fecha" id="fecha" value="{{ $fecha }}" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
            </form>
            
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-lg shadow-indigo-600/20 transition-all">
                <span class="material-symbols-outlined text-lg">print</span> Imprimir Reporte
            </button>
            <a href="{{ route('reportes.index') }}" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all">
                Volver
            </a>
        </div>
    </div>

    <!-- Official Printable Document Card -->
    <div class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm space-y-8">
        
        <!-- Header for print/screen -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-6">
            <div class="space-y-1">
                <h2 class="text-xl font-extrabold text-slate-800 uppercase tracking-tight">SysSAFE logistics platform</h2>
                <p class="text-sm font-bold text-indigo-600 uppercase tracking-widest">Soporte físico de carnets entregados</p>
                <p class="text-xs text-slate-400">Generado el {{ now()->format('d/m/Y h:i A') }} por {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right space-y-1">
                <span class="inline-block px-3 py-1 bg-indigo-50 border border-indigo-100 rounded-xl text-[10px] font-black uppercase text-indigo-700 tracking-wider">
                    REPORTE OFICIAL DEL DÍA
                </span>
                <div class="text-lg font-black text-slate-700 mt-2">
                    FECHA: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Meta Summary Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Entidad Destino</span>
                <span class="text-xs font-bold text-slate-700">ARS CMD</span>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Proveedor Logístico</span>
                <span class="text-xs font-bold text-slate-700">SAFESURE logistics</span>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Expedientes</span>
                <span class="text-xs font-black text-indigo-600">{{ $afiliados->count() }} caso(s)</span>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Estado del Reporte</span>
                <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Listo para Remitir
                </span>
            </div>
        </div>

        <!-- Table List of Affiliates -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-slate-100">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider w-10 text-center">#</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider">Cédula</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider">Afiliado</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider text-center">Sexo</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider">F. Nacimiento</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider">Solicitud / Contrato</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider text-center">Dep.</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider text-center">Estado</th>
                        <th class="py-3 px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider text-right">F. Registro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($afiliados as $index => $a)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-4 text-xs font-bold text-slate-400 text-center">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 text-xs font-mono font-bold text-slate-700">{{ $a->cedula_formatted }}</td>
                        <td class="py-3 px-4 text-xs font-bold text-slate-800">{{ $a->nombre_completo }}</td>
                        <td class="py-3 px-4 text-xs text-slate-600 text-center">{{ $a->sexo ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            {{ $a->fecha_nacimiento ? $a->fecha_nacimiento->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600">
                            <span class="font-bold text-slate-700">{{ $a->numero_solicitud ?: ($a->contrato ?: 'N/A') }}</span>
                            @if($a->poliza) <span class="text-slate-400 block text-[10px]">Pol: {{ $a->poliza }}</span> @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-600 text-center">{{ $a->cantidad_dependientes ?? 0 }}</td>
                        <td class="py-3 px-4 text-xs text-center">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $a->estado_id == 9 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                {{ $a->estado?->nombre }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-slate-500 text-right">
                            {{ $a->fecha_entrega_safesure ? $a->fecha_entrega_safesure->format('d/m/Y h:i A') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 font-medium">
                            <span class="material-symbols-outlined block text-3xl mb-2 text-slate-300">ghost</span>
                            No hay afiliados registrados como Recibido o Completado en esta fecha.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Print Signatures Section -->
        <div class="pt-16 border-t border-slate-100 grid grid-cols-2 gap-12">
            <div class="text-center space-y-4">
                <div class="w-2/3 mx-auto border-b-2 border-slate-300 h-10"></div>
                <div class="space-y-1">
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wider block">Entregado / Remitido por:</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">SAFESURE LOGISTICS DEPT.</span>
                    <span class="text-xs text-slate-500 font-medium mt-1 block">Firma y Sello</span>
                </div>
            </div>
            <div class="text-center space-y-4">
                <div class="w-2/3 mx-auto border-b-2 border-slate-300 h-10"></div>
                <div class="space-y-1">
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wider block">Recibido Conforme por:</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block">ARS CMD AUDIT DEPT.</span>
                    <span class="text-xs text-slate-500 font-medium mt-1 block">Firma, Nombre y Cédula</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
