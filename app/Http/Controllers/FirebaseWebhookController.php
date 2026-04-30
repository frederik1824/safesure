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

        if (!$afiliado) {
            // CREACIÓN: Si el registro no existe localmente, lo creamos
            $afiliado = new Afiliado(['cedula' => $cedula]);
            \App\Models\WebhookLog::where('document_id', $cedula)->where('event_type', 'afiliado')->latest()->first()?->update([
                'message' => "Creating new affiliate from Firebase"
            ]);
        }

        // REGLA BIDIRECCIONAL: Solo actualizamos si Firebase es más reciente
        $afiliado->is_firebase_sync = true; 
        
        // Usamos el servicio para comparar y actualizar localmente
        $updated = $this->syncService->syncLocalModel($afiliado, $data);
        
        \App\Models\WebhookLog::where('document_id', $cedula)->where('event_type', 'afiliado')->latest()->first()?->update([
            'status' => 'processed',
            'message' => $updated ? 'Data processed from Firebase' : 'Already up to date'
        ]);

        return response()->json(['message' => $updated ? 'Processed' : 'Already up to date']);
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

        if (!$empresa) {
            $empresa = new Empresa(['uuid' => $uuid]);
        }

        $empresa->is_firebase_sync = true;
        $updated = $this->syncService->syncLocalModel($empresa, $data);
        
        \App\Models\WebhookLog::where('document_id', $uuid)->where('event_type', 'empresa')->latest()->first()?->update([
            'status' => 'processed',
            'message' => $updated ? 'Data processed from Firebase' : 'Already up to date'
        ]);

        return response()->json(['message' => $updated ? 'Processed' : 'Already up to date']);
    }
}
