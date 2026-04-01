<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Responsable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rid = $user->responsable_id ?? 'admin';
        $ttl = 300; // 5 minutos de Caché Térmica

        // Métricas Generales
        $totalAfiliados = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalAfiliados", $ttl, fn() => Afiliado::count());
        $totalAsignados = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalAsignados", $ttl, fn() => Afiliado::whereNotNull('responsable_id')->count());
        
        $totalEntregados = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalEntregados", $ttl, function() {
            return Afiliado::whereHas('estado', function($q) {
                $q->whereIn('nombre', ['Carnet entregado', 'Cierre parcial', 'Completado', 'Pendiente de recepción']);
            })->count();
        });
        
        $totalCompletados = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalCompletados", $ttl, function() {
            return Afiliado::whereHas('estado', function($q) {
                $q->where('nombre', 'Completado');
            })->count();
        });

        // Conteo por Empresas FILIAL
        $totalFilial = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalFilial", $ttl, fn() => Afiliado::enEmpresaFilial()->count());
        
        $confirmadosFilial = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_confirmadosFilial", $ttl, function() {
            return Afiliado::enEmpresaFilial()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        $totalOtras = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalOtras", $ttl, function() {
            return Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            })->count();
        });

        $terminadosOtras = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_terminadosOtras", $ttl, function() {
            return Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            })->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        // Métricas SAFESURE / SLA
        $fueraSlaCount = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_fueraSlaCount", $ttl, function() {
            return Afiliado::whereNotNull('fecha_entrega_proveedor')
                ->where('liquidado', false)
                ->with('estado')
                ->get() 
                ->filter(fn($a) => $a->sla_status === 'critico')
                ->count();
        });

        $montoArs = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_montoArs", $ttl, function() {
            return Afiliado::ars()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');
        });

        $montoNoArs = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_montoNoArs", $ttl, function() {
            return Afiliado::noArs()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');
        });

        // Métricas de EMPRESAS VERIFICADAS (Ex-SAFE)
        $totalVerificadas = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_totalVerificadas", $ttl, fn() => Afiliado::enEmpresaReal()->count());
        
        $confirmadosVerificadas = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_confirmadosVerificadas", $ttl, function() {
            return Afiliado::enEmpresaReal()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        // Calcular porcentaje global
        $porcentajeCompletado = $totalAfiliados > 0 ? round(($totalCompletados / $totalAfiliados) * 100) : 0;

        // Breakdown por Estado (para gráficos)
        $afiliadosPorEstado = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_afiliadosPorEstado", $ttl, function() {
            return Afiliado::select('estado_id', DB::raw('count(*) as total'))
                ->groupBy('estado_id')
                ->with('estado')
                ->get();
        });

        // Breakdown por Corte
        $afiliadosPorCorte = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_afiliadosPorCorte", $ttl, function() {
            return Afiliado::select('corte_id', DB::raw('count(*) as total'))
                ->groupBy('corte_id')
                ->with('corte')
                ->orderBy('corte_id', 'desc')
                ->take(5)
                ->get();
        });

        // Breakdown por Responsable
        $productividadResponsables = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_productividadResponsables", $ttl, function() {
            return Afiliado::select('responsable_id', DB::raw('count(*) as total_asignados'))
                ->whereNotNull('responsable_id')
                ->groupBy('responsable_id')
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

        // Estadísticas mensuales (Tendencia últimos 6 meses)
        $statsPorMes = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_statsPorMes", $ttl, function() {
            return DB::table('afiliados')
                ->select(DB::raw("DATE_FORMAT(created_at, '%M') as mes"), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('mes')
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();
        });

        // Actividad Reciente (Filtramos por afiliados visibles al usuario)
        $actividadReciente = \Illuminate\Support\Facades\Cache::remember("dashboard_{$rid}_actividadReciente", 60, function() {
            return \App\Models\HistorialEstado::whereHas('afiliado')
                ->with(['afiliado', 'estadoNuevo', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        });

        return view('dashboard', compact(
            'totalAfiliados', 
            'totalAsignados', 
            'totalEntregados',
            'totalCompletados',
            'totalFilial',
            'confirmadosFilial',
            'totalOtras',
            'terminadosOtras',
            'porcentajeCompletado',
            'afiliadosPorEstado',
            'afiliadosPorCorte',
            'productividadResponsables',
            'actividadReciente',
            'fueraSlaCount',
            'montoArs',
            'montoNoArs',
            'totalVerificadas',
            'confirmadosVerificadas',
            'statsPorMes'
        ));
    }
}
