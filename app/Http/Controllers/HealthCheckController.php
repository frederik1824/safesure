<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Cache;

class HealthCheckController extends Controller
{
    /**
     * Endpoint para Dokploy / Monitoreo externo
     */
    public function __invoke()
    {
        $status = [
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'services' => []
        ];

        // 1. Verificar Base de Datos
        try {
            DB::connection()->getPdo();
            $status['services']['database'] = 'ok';
        } catch (\Exception $e) {
            $status['status'] = 'degraded';
            $status['services']['database'] = 'fail: ' . $e->getMessage();
        }

        // 2. Verificar Firebase (Health Check rápido)
        try {
            $sync = app(FirebaseSyncService::class);
            $token = Cache::get('firebase_access_token'); // Intentamos ver si hay token cacheado
            $status['services']['firebase'] = $token ? 'ok' : 'pending_auth';
        } catch (\Exception $e) {
            $status['services']['firebase'] = 'fail';
        }

        // 3. Verificar Espacio en Disco (Crítico para logs)
        $freeSpace = disk_free_space(base_path());
        $status['services']['storage'] = [
            'free_gb' => round($freeSpace / (1024 * 1024 * 1024), 2),
            'status' => ($freeSpace < 500 * 1024 * 1024) ? 'low' : 'ok' // Alerta si menos de 500MB
        ];

        $httpCode = ($status['status'] === 'healthy') ? 200 : 503;

        return response()->json($status, $httpCode);
    }
}
