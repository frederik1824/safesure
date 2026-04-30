<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\Estado;
use Exception;

class AfiliadoBusinessRulesService
{
    /**
     * Valida las reglas de negocio antes de guardar un afiliado.
     * Basado en el Protocolo CMD.
     */
    public function validateSaving(Afiliado $afiliado): void
    {
        // 1. Regla de Inmutabilidad (Completado ID 9)
        if ($afiliado->getOriginal('estado_id') == 9) {
            if ($afiliado->isDirty(['responsable_id', 'empresa_id', 'estado_id', 'cedula'])) {
                if (!isset($afiliado->bypassing_reopen) || !$afiliado->bypassing_reopen) {
                    throw new Exception("Protocolo CMD: El expediente está COMPLETADO y es inmutable. No se permiten cambios estructurales.");
                }
            }
        }

        // 2. Regla de Asignación Obligatoria para "En Ruta" (ID 3)
        if ($afiliado->isDirty('estado_id') && $afiliado->estado_id == 3 && empty($afiliado->responsable_id)) {
            throw new Exception("Protocolo CMD: No se puede pasar a 'En Ruta' sin un responsable asignado.");
        }

        // 3. Bloqueo de Saltos Inválidos hacia Completado
        if ($afiliado->isDirty('estado_id')) {
            $oldState = $afiliado->getOriginal('estado_id');
            $newState = $afiliado->estado_id;

            if (!is_null($oldState) && $newState == 9 && in_array($oldState, [1, 4, 11, 12, 13])) {
                throw new Exception("Protocolo CMD: Transición inválida hacia Completado (9) desde estado inicial ($oldState).");
            }
        }

        // 4. Asegurar Costo de Entrega en estados finales
        if (in_array($afiliado->estado_id, [6, 9])) {
            if (is_null($afiliado->costo_entrega) || $afiliado->costo_entrega == 0) {
                $this->assignDefaultCost($afiliado);
            }
        }
    }

    /**
     * Asigna el costo base según proveedor o responsable
     */
    protected function assignDefaultCost(Afiliado $afiliado): void
    {
        if ($afiliado->proveedor_id && $afiliado->proveedor?->precio_base > 0) {
            $afiliado->costo_entrega = $afiliado->proveedor->precio_base;
        } elseif ($afiliado->responsable_id && $afiliado->responsable?->precio_entrega > 0) {
            $afiliado->costo_entrega = $afiliado->responsable->precio_entrega;
        }
    }

    /**
     * Normaliza la dirección
     */
    public function normalizeAddress(Afiliado $afiliado): void
    {
        if (!$afiliado->direccion) return;

        $replacements = [
            '/\bC\/\b/i' => 'Calle ',
            '/\bNo\.\b/i' => '#',
            '/\bEsq\.\b/i' => 'Esquina ',
            '/\bApt\.\b/i' => 'Apartamento ',
            '/\bRes\.\b/i' => 'Residencial ',
            '/\bAut\.\b/i' => 'Autopista ',
        ];

        $afiliado->direccion = preg_replace(array_keys($replacements), array_values($replacements), $afiliado->direccion);
        $afiliado->direccion = trim(preg_replace('/\s+/', ' ', $afiliado->direccion));
    }
}
