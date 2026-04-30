<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\HistorialEstado;
use App\Models\Estado;
use Illuminate\Support\Facades\Auth;
use Exception;

class AfiliadoService
{
    /**
     * Define valid state transitions based on Rule 5.5
     */
    protected $validTransitions = [
        'pendiente' => ['asignado', 'cancelado'],
        'asignado' => ['contactado', 'pendiente', 'cancelado'],
        'contactado' => ['en ruta', 'reprogramado', 'no localizado', 'incidencia'],
        'en ruta' => ['carnet entregado', 'no localizado', 'incidencia', 'reprogramado'],
        'carnet entregado' => ['acuse recibido', 'formulario recibido', 'completado'],
        'no localizado' => ['asignado', 'contactado', 'cancelado'],
        'reprogramado' => ['en ruta', 'contactado'],
        'incidencia' => ['asignado', 'contactado', 'cancelado'],
        'acuse recibido' => ['formulario recibido', 'completado', 'cierre parcial'],
        'formulario recibido' => ['acuse recibido', 'completado', 'cierre parcial'],
        'cierre parcial' => ['completado'],
        'completado' => [], // Locked by Rule 5.3
    ];

    /**
     * Update Affiliate status with business rules validation
     */
    public function updateStatus(Afiliado $afiliado, int $newEstadoId, ?string $observacion = null, $userId = null)
    {
        // 1. Antes de proceder, verificar si necesitamos un recálculo automático basado en evidencias
        // Esto permite que el sistema "sepa" si debe ir a Completado o Cierre Parcial
        $finalEstadoId = $this->calculateAutomaticState($afiliado, $newEstadoId);
        
        $newEstado = Estado::findOrFail($finalEstadoId);
        $oldEstado = $afiliado->estado;
        
        // IDs críticos (Cache o búsqueda eficiente)
        $idCompletado = Estado::where('nombre', 'Completado')->first()?->id;

        // Rule 5.3: Immutability of closure
        if ($oldEstado && $oldEstado->id == $idCompletado && !isset($afiliado->bypassing_reopen)) {
            throw new Exception("Regla 5.3: El registro está Completado e inmutable. Use el proceso de reapertura oficial.");
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($afiliado, $finalEstadoId, $oldEstado, $newEstado, $userId, $observacion) {
            $afiliado->estado_id = $finalEstadoId;
            $afiliado->save();

            // Rule 6: Audit/Traceability
            HistorialEstado::create([
                'afiliado_id' => $afiliado->id,
                'estado_anterior_id' => $oldEstado->id ?? null,
                'estado_nuevo_id' => $finalEstadoId,
                'user_id' => $userId ?? Auth::id() ?? 1,
                'observacion' => $observacion ?? "Cambio de estado a " . ($newEstado->nombre ?? 'N/A')
            ]);

            return $afiliado;
        });
    }

    /**
     * Logic for automatic state based on documents (Rule 5.1, 5.2)
     * Note: This is now integrated or called when needed to keep updateStatus simple
     */
    public function calculateAutomaticState(Afiliado $afiliado, int $currentEstadoId)
    {
        $estadoCompletado = Estado::where('nombre', 'Completado')->first();
        if ($currentEstadoId === $estadoCompletado?->id) return $currentEstadoId;

        // Revisamos evidencias (digitales o físicas sin archivo)
        $acuse = $afiliado->evidenciasAfiliado()->where('tipo_documento', 'acuse_recibo')->where('status', 'validado')->exists();
        $formulario = $afiliado->evidenciasAfiliado()->where('tipo_documento', 'formulario_firmado')->where('status', 'validado')->exists();

        if ($acuse && $formulario) {
            return $estadoCompletado->id ?? $currentEstadoId;
        }

        if ($acuse || $formulario) {
            return Estado::where('nombre', 'Cierre parcial')->first()->id ?? $currentEstadoId;
        }

        return $currentEstadoId;
    }

    protected function isValidTransition($from, $to)
    {
        if ($from === $to) return true;
        
        // Regla de Oro: El cierre (completado) puede forzarse administrativamente si hay una razón válida
        if ($to === 'completado') {
            return true;
        }

        if (!isset($this->validTransitions[$from])) return true; 
        return in_array($to, $this->validTransitions[$from]);
    }

    /**
     * Rule 5.4: Reopening process
     */
    public function reopen(Afiliado $afiliado, string $motivo, $userId)
    {
        $oldEstadoId = $afiliado->estado_id;
        $estadoPendiente = Estado::where('nombre', 'Pendiente')->first();
        
        $afiliado->estado_id = $estadoPendiente->id;
        $afiliado->save();

        HistorialEstado::create([
            'afiliado_id' => $afiliado->id,
            'estado_anterior_id' => $oldEstadoId,
            'estado_nuevo_id' => $afiliado->estado_id,
            'user_id' => $userId,
            'observacion' => "REAPERTURA: " . $motivo
        ]);

        return $afiliado;
    }
}
