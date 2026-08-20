<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\Estado;
use App\Services\AfiliadoService;
use App\Services\EvidenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SafesureApiController extends Controller
{
    /**
     * Normalizes and formats the ID card (Cédula) into standard format: 000-0000000-0
     */
    protected function normalizeCedula(string $cedula): string
    {
        $clean = preg_replace('/[^0-9]/', '', $cedula);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
        }
        return $cedula; // Return as is if it doesn't match standard 11 digits
    }

    /**
     * Transforms an affiliate model for API output
     */
    protected function transformAfiliado(Afiliado $afiliado): array
    {
        return [
            'uuid' => $afiliado->uuid,
            'nombre_completo' => $afiliado->nombre_completo,
            'cedula' => $afiliado->cedula,
            'sexo' => $afiliado->sexo,
            'telefono' => $afiliado->telefono,
            'direccion' => $afiliado->direccion,
            'provincia' => $afiliado->provincia,
            'municipio' => $afiliado->municipio,
            'empresa' => $afiliado->empresaModel?->nombre ?? $afiliado->empresa,
            'rnc_empresa' => $afiliado->empresaModel?->rnc ?? $afiliado->rnc_empresa,
            'poliza' => $afiliado->poliza,
            'contrato' => $afiliado->contrato,
            'estado' => [
                'id' => $afiliado->estado_id,
                'nombre' => $afiliado->estado?->nombre ?? 'Desconocido',
                'es_final' => (bool) ($afiliado->estado?->es_final ?? false),
            ],
            'fecha_entrega_proveedor' => $afiliado->fecha_entrega_proveedor ? Carbon::parse($afiliado->fecha_entrega_proveedor)->toIso8601String() : null,
            'fecha_entrega_safesure' => $afiliado->fecha_entrega_safesure ? Carbon::parse($afiliado->fecha_entrega_safesure)->toIso8601String() : null,
            'updated_at' => $afiliado->updated_at->toIso8601String(),
            'evidencias' => $afiliado->evidenciasAfiliado->map(function ($ev) {
                return [
                    'id' => $ev->id,
                    'tipo_documento' => $ev->tipo_documento,
                    'status' => $ev->status,
                    'file_path' => $ev->file_path ? asset('storage/' . $ev->file_path) : null,
                    'observaciones' => $ev->observaciones,
                    'uploaded_at' => $ev->created_at->toIso8601String(),
                ];
            }),
        ];
    }

    /**
     * GET /api/v1/safesure/estados
     * Retrieve the list of available statuses
     */
    public function getEstados()
    {
        $estados = Estado::orderBy('id', 'asc')->get(['id', 'nombre', 'es_final']);
        return response()->json([
            'success' => true,
            'data' => $estados
        ]);
    }

    /**
     * GET /api/v1/safesure/afiliados
     * Retrieve paginated affiliates assigned to SAFESURE (responsable_id = 2)
     */
    public function getAfiliados(Request $request)
    {
        $request->validate([
            'estado_id' => 'nullable|integer|exists:estados,id',
            'cedula' => 'nullable|string|max:20',
            'updated_after' => 'nullable|date_format:Y-m-d\TH:i:sP,Y-m-d H:i:s,Y-m-d',
        ]);

        // Query without global scopes, restricted strictly to SAFESURE
        $query = Afiliado::withoutGlobalScopes()
            ->where('responsable_id', 2)
            ->with(['estado', 'empresaModel', 'evidenciasAfiliado']);

        if ($request->has('estado_id')) {
            $query->where('estado_id', $request->input('estado_id'));
        }

        if ($request->has('cedula')) {
            $cedula = $this->normalizeCedula($request->input('cedula'));
            $query->where('cedula', $cedula);
        }

        if ($request->has('updated_after')) {
            try {
                $date = Carbon::parse($request->input('updated_after'));
                $query->where('updated_at', '>=', $date);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formato de fecha inválido para el campo updated_after.'
                ], 400);
            }
        }

        $perPage = (int) $request->input('per_page', 50);
        $perPage = min(max($perPage, 1), 250); // Bound limit

        $afiliados = $query->paginate($perPage);

        $transformed = collect($afiliados->items())->map(function ($afiliado) {
            return $this->transformAfiliado($afiliado);
        });

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'meta' => [
                'current_page' => $afiliados->currentPage(),
                'last_page' => $afiliados->lastPage(),
                'per_page' => $afiliados->perPage(),
                'total' => $afiliados->total(),
            ]
        ]);
    }

    /**
     * GET /api/v1/safesure/afiliados/{cedula}
     * Retrieve single affiliate details by Cedula
     */
    public function getAfiliadoByCedula($cedula)
    {
        $normalized = $this->normalizeCedula($cedula);

        $afiliado = Afiliado::withoutGlobalScopes()
            ->where('responsable_id', 2)
            ->where('cedula', $normalized)
            ->with(['estado', 'empresaModel', 'evidenciasAfiliado'])
            ->first();

        if (!$afiliado) {
            return response()->json([
                'success' => false,
                'error' => 'Afiliado no encontrado o no asignado a SAFESURE.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformAfiliado($afiliado)
        ]);
    }

    /**
     * POST /api/v1/safesure/afiliados/{cedula}/estado
     * Update state of a single affiliate
     */
    public function updateEstado(Request $request, $cedula, AfiliadoService $afiliadoService)
    {
        $normalized = $this->normalizeCedula($cedula);

        $afiliado = Afiliado::withoutGlobalScopes()
            ->where('responsable_id', 2)
            ->where('cedula', $normalized)
            ->first();

        if (!$afiliado) {
            return response()->json([
                'success' => false,
                'error' => 'Afiliado no encontrado o no asignado a SAFESURE.'
            ], 404);
        }

        $request->validate([
            'estado_id' => 'required|integer|exists:estados,id',
            'observacion' => 'nullable|string|max:500',
            'fecha_entrega' => 'nullable|date_format:Y-m-d H:i:s,Y-m-d\TH:i:sP,Y-m-d',
        ]);

        $newEstadoId = (int)$request->input('estado_id');
        $newEstado = Estado::find($newEstadoId);
        $newNombre = strtolower($newEstado->nombre);

        try {
            DB::beginTransaction();

            // Set delivery date if transition is to a completed/delivered/acuse state
            if (in_array($newNombre, ['completado', 'acuse recibido', 'carnet entregado'])) {
                $deliveryDate = $request->input('fecha_entrega') 
                    ? Carbon::parse($request->input('fecha_entrega')) 
                    : now();
                
                $afiliado->fecha_entrega_safesure = $deliveryDate;
            }

            // Record update source
            $afiliado->updated_from = 'SAFESURE';
            $afiliado->save();

            // Update state with audit logs via standard service
            $afiliadoService->updateStatus(
                $afiliado,
                $newEstadoId,
                $request->input('observacion') ?? "Actualizado vía API de SAFESURE",
                1 // System User ID reference
            );

            DB::commit();

            $afiliado->load(['estado', 'empresaModel', 'evidenciasAfiliado']);

            return response()->json([
                'success' => true,
                'message' => 'Estado del afiliado actualizado correctamente.',
                'data' => $this->transformAfiliado($afiliado)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar estado mediante API SAFESURE para afiliado {$cedula}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error interno al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/v1/safesure/afiliados/{cedula}/evidencia
     * Upload delivery evidence and let the system automatically recalculate the state
     */
    public function uploadEvidencia(Request $request, $cedula, EvidenciaService $evidenciaService)
    {
        $normalized = $this->normalizeCedula($cedula);

        $afiliado = Afiliado::withoutGlobalScopes()
            ->where('responsable_id', 2)
            ->where('cedula', $normalized)
            ->first();

        if (!$afiliado) {
            return response()->json([
                'success' => false,
                'error' => 'Afiliado no encontrado o no asignado a SAFESURE.'
            ], 404);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240', // 10MB limit
            'tipo_documento' => 'required|in:acuse_recibo,formulario_firmado',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $tipo = $request->input('tipo_documento');
            $obs = $request->input('observaciones') ?? 'Evidencia cargada vía API de SAFESURE';

            // Upload via service (saves file, updates DB record, recalculates state, updates status)
            $evidencia = $evidenciaService->upload(
                $afiliado,
                $tipo,
                $file,
                1, // System User ID
                $obs
            );

            // Record update source
            $afiliado->updated_from = 'SAFESURE';
            $afiliado->save();

            DB::commit();

            $afiliado->load(['estado', 'empresaModel', 'evidenciasAfiliado']);

            return response()->json([
                'success' => true,
                'message' => 'Evidencia digital subida y registrada exitosamente.',
                'evidencia' => [
                    'id' => $evidencia->id,
                    'tipo_documento' => $evidencia->tipo_documento,
                    'status' => $evidencia->status,
                    'file_path' => $evidencia->file_path ? asset('storage/' . $evidencia->file_path) : null,
                    'observaciones' => $evidencia->observaciones,
                ],
                'data' => $this->transformAfiliado($afiliado)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al subir evidencia mediante API SAFESURE para afiliado {$cedula}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error interno al subir la evidencia: ' . $e->getMessage()
            ], 500);
        }
    }
}
