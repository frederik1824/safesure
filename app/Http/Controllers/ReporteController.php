<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Responsable;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Original General Dashboard (Restored)
     */
    public function index(Request $request)
    {
        // Estadísticas Críticas
        $stats = [
            'total_afiliados' => Afiliado::count(),
            'completados' => Afiliado::finished()->count(),
            'critico_sla' => Afiliado::with('estado')->get()
                            ->filter(fn($a) => $a->sla_status === 'critico')
                            ->count(),
            'por_liquidar' => Afiliado::finished()
                            ->where('liquidado', false)
                            ->sum('costo_entrega'),
        ];

        // Progreso por Corte
        $cortes_progreso = Corte::withCount(['afiliados', 'afiliados as completados_count' => function($q) {
            $q->whereIn('estado_id', [6, 9]);
        }])->get();

        // Distribución por Estado
        $estados_labels = Estado::pluck('nombre');
        $estados_counts = Estado::withCount('afiliados')->pluck('afiliados_count');

        return view('reportes.index', compact('stats', 'cortes_progreso', 'estados_labels', 'estados_counts'));
    }

    /**
     * New Executive Supervision Dashboard
     */
    public function supervision(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', now()->format('Y-m-d'));
        $corte_id = $request->input('corte_id');
        $responsable_id = $request->input('responsable_id');
        $empresa_id = $request->input('empresa_id');

        // Query base con filtros aplicados
        $query = Afiliado::query()
            ->when($fecha_desde, fn($q) => $q->whereDate('created_at', '>=', $fecha_desde))
            ->when($fecha_hasta, fn($q) => $q->whereDate('created_at', '<=', $fecha_hasta))
            ->when($corte_id, fn($q) => $q->where('corte_id', $corte_id))
            ->when($responsable_id, fn($q) => $q->where('responsable_id', $responsable_id))
            ->when($empresa_id, fn($q) => $q->where('empresa_id', $empresa_id));

        // 1. Estadísticas KPI
        $ingresos_count = (clone $query)->count();
        
        $salidas_query = \App\Models\HistorialEstado::whereHas('estadoNuevo', function($q) {
                $q->whereIn('id', [6, 9]);
            })
            ->whereDate('created_at', '>=', $fecha_desde)
            ->whereDate('created_at', '<=', $fecha_hasta)
            ->whereHas('afiliado', function($q) use ($corte_id, $responsable_id, $empresa_id) {
                $q->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
                  ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
                  ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
            });

        $salidas_count = $salidas_query->count();

        $stats = [
            'ingresos' => $ingresos_count,
            'salidas' => $salidas_count,
            'critico_sla' => (clone $query)->with('estado')->get()
                            ->filter(fn($a) => $a->sla_status === 'critico')
                            ->count(),
            'por_liquidar' => (clone $query)->finished()
                            ->where('liquidado', false)
                            ->sum('costo_entrega'),
        ];
        
        $stats['tasa_entrega'] = $stats['ingresos'] > 0 ? ($stats['salidas'] / $stats['ingresos']) * 100 : 0;

        // 2. Datos para Gráfico de Tendencia
        $tendencia = Afiliado::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as total_ingreso')
            )
            ->whereDate('created_at', '>=', $fecha_desde)
            ->whereDate('created_at', '<=', $fecha_hasta)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // 3. Distribución por Estado
        $estados = Estado::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $corte_id, $responsable_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
              ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])->get();

        // 4. Datos por Corte
        $cortes_data = Corte::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $responsable_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])->get();

        // 5. Productividad por Responsable
        $responsables_data = Responsable::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $corte_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])
        ->orderBy('afiliados_count', 'desc')
        ->take(10)
        ->get();

        // Data for view
        $cortes = Corte::all();
        $responsables = Responsable::all();
        $empresas = Empresa::where('es_real', true)->get();

        return view('reportes.supervision', compact(
            'stats', 'estados', 'cortes_data', 'responsables_data', 
            'tendencia', 'cortes', 'responsables', 'empresas',
            'fecha_desde', 'fecha_hasta', 'corte_id', 'responsable_id', 'empresa_id'
        ));
    }

    public function export(Request $request)
    {
        $query = Afiliado::with(['corte', 'estado', 'responsable', 'empresaModel'])
            ->when($request->fecha_desde, fn($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta))
            ->when($request->corte_id, fn($q) => $q->where('corte_id', $request->corte_id))
            ->when($request->responsable_id, fn($q) => $q->where('responsable_id', $request->responsable_id))
            ->when($request->empresa_id, fn($q) => $q->where('empresa_id', $request->empresa_id));

        $afiliados = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_supervision_'.date('Y-m-d').'.csv"',
        ];

        $callback = function() use ($afiliados) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Nombre Completo', 'Cedula', 'Contrato', 'Empresa', 'RNC Empresa', 
                'Corte', 'Estado', 'Responsable', 'Fecha Ingreso', 'Fecha Entrega Prov', 
                'SLA Status', 'Costo Entrega', 'Liquidado'
            ]);

            foreach ($afiliados as $a) {
                fputcsv($file, [
                    $a->id,
                    $a->nombre_completo,
                    $a->cedula,
                    $a->contrato,
                    $a->empresaModel?->nombre ?? $a->empresa,
                    $a->rnc_empresa,
                    $a->corte?->nombre ?? 'N/A',
                    $a->estado?->nombre ?? 'N/A',
                    $a->responsable?->nombre ?? 'N/A',
                    $a->created_at->format('Y-m-d'),
                    $a->fecha_entrega_proveedor?->format('Y-m-d') ?? '',
                    $a->sla_status,
                    $a->costo_entrega,
                    $a->liquidado ? 'SI' : 'NO'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function heatmap(Request $request)
    {
        $provincia_id = $request->provincia_id;
        $municipio_id = $request->municipio_id;

        $queryAfiliados = Afiliado::query();
        if ($provincia_id) $queryAfiliados->where('provincia_id', $provincia_id);
        if ($municipio_id) $queryAfiliados->where('municipio_id', $municipio_id);

        $densidadProvincia = (clone $queryAfiliados)->select('provincia_id')
            ->selectRaw('count(*) as total')
            ->whereNotNull('provincia_id')
            ->groupBy('provincia_id')
            ->with('provinciaRel')
            ->orderBy('total', 'desc')
            ->get();

        $densidadMunicipio = (clone $queryAfiliados)->select('provincia_id', 'municipio_id')
            ->selectRaw('count(*) as total')
            ->whereNotNull('municipio_id')
            ->groupBy('provincia_id', 'municipio_id')
            ->with(['provinciaRel', 'municipioRel'])
            ->orderBy('total', 'desc')
            ->take(20)
            ->get();

        $queryEmpresas = Empresa::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('afiliados');
            
        if ($provincia_id) $queryEmpresas->where('provincia_id', $provincia_id);
        if ($municipio_id) $queryEmpresas->where('municipio_id', $municipio_id);

        $puntosMapa = $queryEmpresas->get();

        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = $provincia_id ? \App\Models\Municipio::where('provincia_id', $provincia_id)->orderBy('nombre')->get() : collect();

        return view('reportes.heatmap', compact(
            'densidadProvincia', 'densidadMunicipio', 'puntosMapa', 
            'provincias', 'municipios', 'provincia_id', 'municipio_id'
        ));
    }

    public function comparison()
    {
        $responsables = Responsable::whereIn('nombre', ['ARS CMD', 'SAFESURE'])->get();
        
        $comparisonData = [];
        
        foreach ($responsables as $resp) {
            $query = Afiliado::where('responsable_id', $resp->id);
            
            $total = (clone $query)->count();
            $completados = (clone $query)->whereIn('estado_id', [6, 9])->count();
            
            $criticos = (clone $query)->with('estado')->get()->filter(fn($a) => $a->sla_status === 'critico')->count();
            $alertas = (clone $query)->with('estado')->get()->filter(fn($a) => $a->sla_status === 'alerta')->count();
            
            $comparisonData[$resp->nombre] = [
                'id' => $resp->id,
                'total' => $total,
                'completados' => $completados,
                'porcentaje' => $total > 0 ? round(($completados / $total) * 100, 1) : 0,
                'criticos' => $criticos,
                'alertas' => $alertas,
                'por_liquidar' => (clone $query)->finished()->where('liquidado', false)->sum('costo_entrega')
            ];
        }
        
        return view('reportes.comparison', compact('comparisonData'));
    }

    public function slaAlerts()
    {
        return view('reportes.sla_alerts');
    }
}
