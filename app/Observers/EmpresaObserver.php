<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Services\FirebaseSyncService;

class EmpresaObserver
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the Empresa "saved" event (covers created and updated)
     */
    public function saved(Empresa $empresa): void
    {
        if ($empresa->rnc) {
            // Cargar el promotor para transparencia con CMD
            $empresa->load('promotor');
            
            // Limpiamos el RNC para usarlo como ID único en Firestore
            $documentId = preg_replace('/[^0-9]/', '', $empresa->rnc);
            
            // Creamos un payload enriquecido para Firebase (SOLO ATRIBUTOS PLANOS)
            $data = $empresa->getAttributes();
            
            // Enriquecemos con los nombres descriptivos que CMD espera
            $data['promotor_nombre'] = strtoupper($empresa->promotor?->name ?? 'SIN PROMOTOR');
            $data['estatus_verificacion'] = $empresa->es_verificada ? 'VERIFICADA' : 'PENDIENTE';
            
            // Forzamos formato ISO 8601 para todas las fechas detectadas
            foreach ($empresa->getCasts() as $field => $cast) {
                if (($cast === 'datetime' || $cast === 'date') && $empresa->$field) {
                    $data[$field] = $empresa->$field->toIso8601String();
                }
            }

            // Sincronizar toda la data enriquecida
            $this->syncService->syncData($data, 'empresas', $documentId);
            
            // Actualizar marca de tiempo sin disparar eventos
            $empresa->updateQuietly(['firebase_synced_at' => now()]);
        }
    }

    /**
     * Handle the Empresa "deleted" event.
     */
    public function deleted(Empresa $empresa): void
    {
        if ($empresa->rnc) {
            $documentId = preg_replace('/[^0-9]/', '', $empresa->rnc);
            $this->syncService->deleteDocument('empresas', $documentId);
        }
    }
}
