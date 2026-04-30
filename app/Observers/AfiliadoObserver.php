<?php

namespace App\Observers;

use App\Models\Afiliado;
use App\Jobs\SyncToFirebaseJob;
use App\Jobs\DeleteFromFirebaseJob;

class AfiliadoObserver
{
    /**
     * Handle the Afiliado "saved" event (covers created and updated)
     */
    public function saved(Afiliado $afiliado): void
    {
        // Prevención de bucles: Si el modelo fue guardado desde un Webhook, no lo re-sincronizamos
        if (isset($afiliado->is_firebase_sync) && $afiliado->is_firebase_sync) {
            return;
        }

        if ($afiliado->cedula) {
            // Aseguramos que las relaciones críticas estén cargadas para la transparencia con CMD
            $afiliado->load(['estado', 'responsable', 'corte']);
            
            // Usamos la cédula con guiones para mantener compatibilidad
            $documentId = $afiliado->cedula;
            
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

            // Despachar el Job en segundo plano (Asíncrono)
            SyncToFirebaseJob::dispatch($data, 'afiliados', $documentId);
        }
    }

    /**
     * Handle the Afiliado "deleted" event.
     */
    public function deleted(Afiliado $afiliado): void
    {
        if (isset($afiliado->is_firebase_sync) && $afiliado->is_firebase_sync) {
            return;
        }

        if ($afiliado->cedula) {
            $documentId = $afiliado->cedula;
            // Despachar el Job asíncrono
            DeleteFromFirebaseJob::dispatch('afiliados', $documentId);
        }
    }
}
