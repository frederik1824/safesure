<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Jobs\SyncToFirebaseJob;
use App\Jobs\DeleteFromFirebaseJob;

class EmpresaObserver
{
    /**
     * Handle the Empresa "saved" event (covers created and updated)
     */
    public function saved(Empresa $empresa): void
    {
        // Prevención de bucles: Si el modelo fue guardado desde un Webhook, no lo re-sincronizamos
        if (isset($empresa->is_firebase_sync) && $empresa->is_firebase_sync) {
            return;
        }

        if ($empresa->rnc) {
            // Cargar el promotor para transparencia con CMD
            $empresa->load('promotor');
            
            // Usamos el UUID como identificador único en Firebase para máxima compatibilidad con CMD
            $documentId = $empresa->uuid;
            
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

            // Despachar el Job asíncrono
            SyncToFirebaseJob::dispatch($data, 'empresas', $documentId);
        }
    }

    /**
     * Handle the Empresa "deleted" event.
     */
    public function deleted(Empresa $empresa): void
    {
        if (isset($empresa->is_firebase_sync) && $empresa->is_firebase_sync) {
            return;
        }

        if ($empresa->rnc) {
            $documentId = $empresa->uuid;
            // Despachar el Job asíncrono
            DeleteFromFirebaseJob::dispatch('empresas', $documentId);
        }
    }
}
