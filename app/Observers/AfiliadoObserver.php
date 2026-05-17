<?php

namespace App\Observers;

use App\Models\Afiliado;
use App\Jobs\SyncToFirebaseJob;
use App\Jobs\DeleteFromFirebaseJob;

class AfiliadoObserver
{
    protected $rules;

    public function __construct(\App\Services\AfiliadoBusinessRulesService $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Handle the Afiliado "saving" event
     */
    public function saving(Afiliado $afiliado): void
    {
        // 1. Identificar el origen y responsable si es un cambio local (no viene de Firebase)
        if (!isset($afiliado->is_firebase_sync) || !$afiliado->is_firebase_sync) {
            $afiliado->updated_from = 'local';
            $afiliado->last_updated_by = auth()->id() ?? 1; // 1 = Admin/System
            $afiliado->firebase_sync_version = ($afiliado->firebase_sync_version ?? 0) + 1;
            $afiliado->firebase_sync_status = 'pending';
        }

        // 2. Aplicar reglas de negocio
        $this->rules->normalizeAddress($afiliado);
        $this->rules->validateSaving($afiliado);
    }

    /**
     * Handle the Afiliado "saved" event (covers created and updated)
     */
    public function saved(Afiliado $afiliado): void
    {
        // 1. Auditoría Atómica para cambios locales
        if (!isset($afiliado->is_firebase_sync) || !$afiliado->is_firebase_sync) {
            $changes = $afiliado->getChanges();
            // No auditamos si solo cambió la fecha de actualización o campos técnicos de sync
            unset($changes['updated_at'], $changes['firebase_synced_at'], $changes['firebase_sync_status']);
            
            if (count($changes) > 0) {
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id() ?? 1,
                    'model_type' => get_class($afiliado),
                    'model_id' => $afiliado->id,
                    'event' => 'local_update',
                    'old_values' => array_intersect_key($afiliado->getOriginal(), $changes),
                    'new_values' => $changes,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                    'user_agent' => request()->userAgent() ?? 'System'
                ]);
            }
        }

        // 2. Prevención de bucles: Si el modelo fue guardado desde un Webhook/Pull, no lo re-sincronizamos a la nube inmediatamente
        if (isset($afiliado->is_firebase_sync) && $afiliado->is_firebase_sync) {
            return;
        }

        if ($afiliado->cedula) {
            // Aseguramos que las relaciones críticas estén cargadas para la transparencia con CMD
            $afiliado->load(['estado', 'responsable', 'corte']);
            
            $documentId = $afiliado->cedula;
            
            // Payload enriquecido para Firebase
            $data = $afiliado->getAttributes();
            $data['estado_nombre'] = strtoupper($afiliado->estado?->nombre ?? 'PENDIENTE');
            $data['responsable_nombre'] = strtoupper($afiliado->responsable?->nombre ?? 'SIN ASIGNAR');
            $data['corte_nombre'] = strtoupper($afiliado->corte?->nombre ?? 'N/D');
            
            // Metadatos de auditoría para CMD
            $data['last_updated_by_name'] = auth()->user()?->name ?? 'System';
            $data['updated_from'] = 'SAFE-SYSTEM';
            
            // Formato ISO para fechas
            foreach ($afiliado->getCasts() as $field => $cast) {
                if (($cast === 'datetime' || $cast === 'date') && $afiliado->$field) {
                    $data[$field] = $afiliado->$field->toIso8601String();
                }
            }

            // Despachar el Job en segundo plano
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
