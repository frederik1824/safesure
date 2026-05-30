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

        // Dynamic logistics metrics
        $diffSql = \App\Models\Afiliado::getDaysDifferenceSql('fecha_entrega_safesure', 'created_at');
        
        $avgCycleTime = (clone $query)->finished()
            ->whereNotNull('fecha_entrega_safesure')
            ->selectRaw("AVG({$diffSql}) as avg_days")
            ->first()->avg_days ?? 0;

        $completedCount = (clone $query)->finished()->whereNotNull('fecha_entrega_safesure')->count();
        $onTimeCount = (clone $query)->finished()->whereNotNull('fecha_entrega_safesure')
            ->whereRaw("{$diffSql} < 20")
            ->count();
        $otdRate = $completedCount > 0 ? ($onTimeCount / $completedCount) * 100 : 100;

        $stats = [
            'ingresos' => $ingresos_count,
            'salidas' => $salidas_count,
            'avg_cycle_time' => round($avgCycleTime, 1),
            'otd_rate' => round($otdRate, 1),
            'critico_sla' => (clone $query)->with('estado')->get()
                            ->filter(fn($a) => $a->sla_status === 'critico')
                            ->count(),
            'por_liquidar' => (clone $query)->finished()
                            ->where('liquidado', false)
                            ->sum('costo_entrega'),
        ];
        
        $stats['tasa_entrega'] = $stats['ingresos'] > 0 ? ($stats['salidas'] / $stats['ingresos']) * 100 : 0;

        // Funnel analysis pipeline stats
        $funnel_stats = [
            'ingreso' => (clone $query)->where('estado_id', 1)->count(), // Pendiente
            'proceso' => (clone $query)->whereIn('estado_id', [2, 3, 4, 5])->count(), // Impresión/Proceso
            'transito' => (clone $query)->where('estado_id', 6)->count(), // Entregado (En transito para acuse/cierre)
            'completado' => (clone $query)->where('estado_id', 9)->count(), // Completado
        ];

        // Geographic density for widget
        $densidad_provincias = (clone $query)->select('provincia_id')
            ->selectRaw('count(*) as total')
            ->whereNotNull('provincia_id')
            ->groupBy('provincia_id')
            ->with('provinciaRel')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

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
            'fecha_desde', 'fecha_hasta', 'corte_id', 'responsable_id', 'empresa_id',
            'funnel_stats', 'densidad_provincias'
        ));
    }

    public function export(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SupervisionExport($request->all()), 
            'reporte_supervision_' . now()->format('Ymd_His') . '.xlsx'
        );
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
