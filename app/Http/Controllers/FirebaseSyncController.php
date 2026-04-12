<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FirebaseSyncLog;
use Illuminate\Support\Facades\Artisan;

class FirebaseSyncController extends Controller
{
    /**
     * Display the sync dashboard.
     */
    public function index()
    {
        $logs = FirebaseSyncLog::with('user')
            ->latest()
            ->paginate(20);

        $lastSync = FirebaseSyncLog::where('status', 'completed')->latest()->first();

        return view('admin.sync.index', compact('logs', 'lastSync'));
    }

    /**
     * Trigger a specific sync type.
     */
    public function trigger(Request $request)
    {
        $type = $request->input('type', 'incremental');
        
        // Log the start
        $log = FirebaseSyncLog::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'status' => 'started',
            'started_at' => now(),
        ]);

        try {
            switch ($type) {
                case 'verified':
                    Artisan::queue('firebase:pull-all', ['--verificadas' => true, '--log-id' => $log->id]);
                    $message = "Sincronización de empresas verificadas iniciada en segundo plano.";
                    break;
                case 'reales':
                    Artisan::queue('firebase:pull-all', ['--reales' => true, '--log-id' => $log->id]);
                    $message = "Sincronización de empresas reales iniciada en segundo plano.";
                    break;
                case 'push':
                    Artisan::queue('firebase:push', ['--all' => true, '--log-id' => $log->id]);
                    $message = "Sincronización de SUBIDA (Local -> Firebase) iniciada.";
                    break;
                case 'full':
                    Artisan::queue('firebase:pull-all', ['--full' => true, '--log-id' => $log->id]);
                    $message = "Sincronización TOTAL iniciada en segundo plano. Esto puede tardar varios minutos.";
                    break;
                default:
                    Artisan::queue('firebase:pull-all', ['--hours' => 24, '--log-id' => $log->id]);
                    $message = "Sincronización incremental (24h) iniciada en segundo plano.";
                    break;
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
            return back()->with('error', 'Error al iniciar la sincronización: ' . $e->getMessage());
        }
    }
}
