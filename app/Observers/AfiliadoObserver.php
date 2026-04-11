<?php

namespace App\Observers;

use App\Models\Afiliado;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class AfiliadoObserver
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the Afiliado "saved" event (covers created and updated)
     */
    public function saved(Afiliado $afiliado): void
    {
        if ($afiliado->cedula) {
            // Aseguramos que las relaciones críticas estén cargadas para la transparencia con CMD
            $afiliado->load(['estado', 'responsable', 'corte']);
            
            // Limpiamos la cédula para usarla como ID único en Firestore
            $documentId = preg_replace('/[^0-9]/', '', $afiliado->cedula);
            
            // Creamos un payload enriquecido para Firebase (SOLO ATRIBUTOS PLANOS)
            $data = $afiliado->getAttributes();
            
            // Enriquecemos con los nombres descriptivos que CMD espera
            $data['estado_nombre'] = strtoupper($afiliado->estado?->nombre ?? 'PENDIENTE');
            $data['responsable_nombre'] = strtoupper($afiliado->responsable?->nombre ?? 'SIN ASIGNAR');
            $data['corte_nombre'] = strtoupper($afiliado->corte?->nombre ?? 'N/D');
            
            // Forzamos formato ISO 8601 para todas las fechas detectadas
            foreach ($afiliado->getCasts() as $field => $cast) {
                if (($cast === 'datetime' || $cast === 'date') && $afiliado->$field) {
                    $data[$field] = $afiliado->$field->toIso8601String();
                }
            }

            // Sincronizar con Firebase pasando el array enriquecido
            $success = $this->syncService->syncData($data, 'afiliados', $documentId);
            
            if ($success) {
                // Actualizar el campo local de sincronización solo si tuvo éxito
                $afiliado->updateQuietly(['firebase_synced_at' => now()]);
            } else {
                Log::error("Firebase Sync: No se pudo sincronizar el afiliado {$afiliado->cedula}. Revisa los logs de Firebase.");
            }
        }
    }

    /**
     * Handle the Afiliado "deleted" event.
     */
    public function deleted(Afiliado $afiliado): void
    {
        if ($afiliado->cedula) {
            $documentId = preg_replace('/[^0-9]/', '', $afiliado->cedula);
            $this->syncService->deleteDocument('afiliados', $documentId);
        }
    }
}
