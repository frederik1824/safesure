<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class FirebaseWebhookController extends Controller
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle incoming webhook from Firebase.
     */
    public function handle(Request $request)
    {
        // Security Check
        $secret = $request->header('X-Webhook-Secret');
        if ($secret !== config('services.firebase.webhook_secret', env('FIREBASE_WEBHOOK_SECRET'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $type = $request->input('type'); // 'afiliado' or 'empresa'
        $id = $request->input('id');     // Document ID (Cedula for Afiliado, UUID for Empresa)
        $payload = $request->input('data'); // Opcional: Si Firebase manda todo el JSON

        \App\Models\WebhookLog::create([
            'event_type' => $type,
            'document_id' => $id,
            'payload' => $payload,
            'status' => 'received',
            'message' => "Webhook received from Firebase for {$type} {$id}"
        ]);

        Log::info("Firebase Webhook Received: type={$type}, id={$id}");

        try {
            if ($type === 'afiliado') {
                return $this->syncAfiliado($id, $payload);
            } elseif ($type === 'empresa') {
                return $this->syncEmpresa($id, $payload);
            }

            return response()->json(['message' => 'Unknown type'], 400);
        } catch (\Exception $e) {
            Log::error("Firebase Webhook Error: " . $e->getMessage());
            return response()->json(['message' => 'Internal Error'], 500);
        }
    }

    protected function syncAfiliado($cedula, $data = null)
    {
        // Si no mandan la data, la descargamos de Firebase (Costo: 1 lectura)
        if (!$data) {
            $data = $this->syncService->pull('afiliados', $cedula);
        }

        if (!$data) {
            return response()->json(['message' => 'Data not found in Firebase'], 404);
        }

        $afiliado = Afiliado::where('cedula', $cedula)->first();

        if ($afiliado) {
            // REGLA BIDIRECCIONAL: Solo actualizamos si Firebase es más reciente
            // Usamos saveQuietly() o manual flag para evitar que el Observer re-suba el cambio
            $afiliado->is_firebase_sync = true; 
            
            // Usamos el servicio para comparar y actualizar localmente
            $updated = $this->syncService->syncLocalModel($afiliado, $data);
            
            \App\Models\WebhookLog::where('document_id', $cedula)->where('event_type', 'afiliado')->latest()->first()?->update([
                'status' => 'processed',
                'message' => $updated ? 'Data updated from Firebase' : 'Already up to date'
            ]);

            return response()->json(['message' => $updated ? 'Updated' : 'Already up to date']);
        }

        \App\Models\WebhookLog::where('document_id', $cedula)->where('event_type', 'afiliado')->latest()->first()?->update([
            'status' => 'failed',
            'message' => 'Local record not found'
        ]);

        return response()->json(['message' => 'Local record not found'], 404);
    }

    protected function syncEmpresa($uuid, $data = null)
    {
        if (!$data) {
            $data = $this->syncService->pull('empresas', $uuid);
        }

        if (!$data) {
            return response()->json(['message' => 'Data not found in Firebase'], 404);
        }

        $empresa = Empresa::where('uuid', $uuid)->first();

        if ($empresa) {
            $empresa->is_firebase_sync = true;
            $updated = $this->syncService->syncLocalModel($empresa, $data);
            
            \App\Models\WebhookLog::where('document_id', $uuid)->where('event_type', 'empresa')->latest()->first()?->update([
                'status' => 'processed',
                'message' => $updated ? 'Data updated from Firebase' : 'Already up to date'
            ]);

            return response()->json(['message' => $updated ? 'Updated' : 'Already up to date']);
        }

        \App\Models\WebhookLog::where('document_id', $uuid)->where('event_type', 'empresa')->latest()->first()?->update([
            'status' => 'failed',
            'message' => 'Local record not found'
        ]);

        return response()->json(['message' => 'Local record not found'], 404);
    }
}
