<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Exception;

class SystemController extends Controller
{
    /**
     * Muestra el panel de control del sistema (DevOps Dashboard)
     */
    public function index()
    {
        // Obtener info básica del sistema
        $stats = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Docker/Dokploy',
            'database_connection' => config('database.default'),
            'queue_connection' => config('queue.default'),
            // Buscamos el último log de cualquier evento relevante
            'last_sync' => \App\Models\AuditLog::latest()->first()?->created_at?->diffForHumans() ?? 'Sin actividad reciente',
            // Añadimos conteos reales para que las tarjetas sean útiles
            'total_afiliados' => \App\Models\Afiliado::count(),
            'pending_jobs' => \Illuminate\Support\Facades\DB::table('jobs')->count(),
        ];

        return view('admin.system.index', compact('stats'));
    }

    /**
     * Ejecuta comandos de Artisan de forma controlada desde la web
     */
    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string|in:migrate,optimize,clear-cache,queue-restart,view-clear'
        ]);

        $command = $request->input('command');
        $output = '';

        try {
            switch ($command) {
                case 'migrate':
                    Artisan::call('migrate', ['--force' => true]);
                    $output = Artisan::output();
                    break;

                case 'optimize':
                    Artisan::call('optimize');
                    $output = Artisan::output();
                    if (empty($output)) $output = "Sistema optimizado: Configuración, Rutas y Vistas han sido cacheadas para producción.";
                    break;

                case 'clear-cache':
                    Artisan::call('cache:clear');
                    $output = Artisan::output();
                    break;

                case 'view-clear':
                    Artisan::call('view:clear');
                    $output = Artisan::output();
                    break;

                case 'queue-restart':
                    Artisan::call('queue:restart');
                    $output = "Queue workers restarted. New workers will use updated code.";
                    break;
            }

            Log::info("System Command Executed: $command", ['user_id' => auth()->id()]);
            
            return back()->with('success', "Comando '$command' ejecutado con éxito. Salida: " . $output);

        } catch (Exception $e) {
            Log::error("System Command Failed: $command", ['error' => $e->getMessage()]);
            return back()->with('error', "Error al ejecutar '$command': " . $e->getMessage());
        }
    }
}
