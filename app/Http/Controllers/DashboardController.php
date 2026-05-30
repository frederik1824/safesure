<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Responsable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rid = $user->responsable_id ?? 'admin';
        $ttl = 300; // 5 minutos de Caché Térmica

        $mes = $request->input('mes');
        $cachePrefix = "dashboard_{$rid}" . ($mes ? "_" . str_replace('-', '_', $mes) : "");

        // Generar lista de los últimos 12 meses en español
        $monthsList = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthsList[$date->format('Y-m')] = ucfirst($date->translatedFormat('F Y'));
        }

        // Helper para aplicar filtro de mes en consultas de Afiliados
        $applyDateFilter = function ($query) use ($mes) {
            if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $parts = explode('-', $mes);
                $query->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
            }
            return $query;
        };

        // Helper para aplicar filtro de mes en consultas de Empresas
        $applyDateFilterEmpresas = function ($query) use ($mes) {
            if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
                $parts = explode('-', $mes);
                $query->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
            }
            return $query;
        };

        // Métricas Generales
        $totalAfiliados = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalAfiliados", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::query())->count();
        });
        
        $totalEmpresas = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalEmpresas", $ttl, function() use ($applyDateFilterEmpresas) {
            return $applyDateFilterEmpresas(\App\Models\Empresa::query())->count();
        });
        
        $totalAsignados = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalAsignados", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereNotNull('responsable_id'))->count();
        });
        
        $totalEntregados = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalEntregados", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereHas('estado', function($q) {
                $q->whereIn('nombre', ['Carnet entregado', 'Cierre parcial', 'Completado', 'Pendiente de recepción']);
            }))->count();
        });
        
        $totalAcusesRecibidos = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalAcusesRecibidos", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::where('estado_id', 10))->count();
        });

        $totalCompletados = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalCompletados", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereHas('estado', function($q) {
                $q->where('nombre', 'Completado');
            }))->count();
        });

        // Conteo por Empresas FILIAL
        $totalFilial = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalFilial", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::enEmpresaFilial())->count();
        });
        
        $confirmadosFilial = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_confirmadosFilial", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::enEmpresaFilial()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            }))->count();
        });

        $totalOtras = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalOtras", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            }))->count();
        });

        $terminadosOtras = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_terminadosOtras", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            })->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            }))->count();
        });

        // Métricas SAFESURE / SLA
        $fueraSlaCount = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_fueraSlaCount", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::whereNotNull('fecha_entrega_proveedor')
                ->where('liquidado', false))
                ->with('estado')
                ->get() 
                ->filter(fn($a) => $a->sla_status === 'critico')
                ->count();
        });

        $montoArs = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_montoArs", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::ars()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false))
                ->sum('costo_entrega');
        });

        $montoNoArs = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_montoNoArs", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::noArs()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false))
                ->sum('costo_entrega');
        });

        // Métricas de EMPRESAS VERIFICADAS (Ex-SAFE)
        $totalVerificadas = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_totalVerificadas", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::enEmpresaReal())->count();
        });
        
        $confirmadosVerificadas = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_confirmadosVerificadas", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::enEmpresaReal()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            }))->count();
        });

        // Calcular porcentaje global
        $porcentajeCompletado = $totalAfiliados > 0 ? round(($totalCompletados / $totalAfiliados) * 100) : 0;
        $porcentajeAcuses = $totalAfiliados > 0 ? round(($totalAcusesRecibidos / $totalAfiliados) * 100) : 0;

        // Breakdown por Estado (para gráficos)
        $afiliadosPorEstado = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_afiliadosPorEstado", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::select('estado_id', DB::raw('count(*) as total'))
                ->groupBy('estado_id'))
                ->with('estado')
                ->get();
        });

        // Breakdown por Corte
        $afiliadosPorCorte = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_afiliadosPorCorte", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::select('corte_id', DB::raw('count(*) as total'))
                ->groupBy('corte_id'))
                ->with('corte')
                ->orderBy('corte_id', 'desc')
                ->take(5)
                ->get();
        });

        // Breakdown por Responsable
        $productividadResponsables = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_productividadResponsables", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::select('responsable_id', DB::raw('count(*) as total_asignados'))
                ->whereNotNull('responsable_id')
                ->groupBy('responsable_id'))
                ->with('responsable')
                ->get()->map(function($item) {
                    $entregados = Afiliado::where('responsable_id', $item->responsable_id)
                        ->whereHas('estado', function($q) {
                            $q->whereIn('nombre', ['Carnet entregado', 'Cierre parcial', 'Completado', 'Pendiente de recepción']);
                        })->count();
                    $item->entregados = $entregados;
                    $item->porcentaje = $item->total_asignados > 0 ? round(($entregados / $item->total_asignados) * 100) : 0;
                    return $item;
                });
        });

        // Estadísticas mensuales usando el Trait dinámico (Cacheado por performance)
        $statsPorMes = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_statsPorMes", $ttl, function() use ($applyDateFilter) {
            return $applyDateFilter(Afiliado::query())
                ->selectMonthName('created_at')
                ->selectRaw('count(*) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupByMonth('created_at')
                ->get();
        });

        // Actividad Reciente (Filtramos por afiliados visibles al usuario)
        $actividadReciente = \Illuminate\Support\Facades\Cache::remember("{$cachePrefix}_actividadReciente", 60, function() use ($applyDateFilter) {
            return \App\Models\HistorialEstado::whereHas('afiliado', function($q) use ($applyDateFilter) {
                    $applyDateFilter($q);
                })
                ->with(['afiliado', 'estadoNuevo', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        });

        return view('dashboard', compact(
            'totalAfiliados', 
            'totalEmpresas',
            'totalAsignados', 
            'totalEntregados',
            'totalAcusesRecibidos',
            'totalCompletados',
            'totalFilial',
            'confirmadosFilial',
            'totalOtras',
            'terminadosOtras',
            'porcentajeCompletado',
            'porcentajeAcuses',
            'afiliadosPorEstado',
            'afiliadosPorCorte',
            'productividadResponsables',
            'actividadReciente',
            'fueraSlaCount',
            'montoArs',
            'montoNoArs',
            'totalVerificadas',
            'confirmadosVerificadas',
            'statsPorMes',
            'monthsList'
        ));
    }
}
