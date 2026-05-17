<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasDynamicSql;

class Afiliado extends Model
{
    use Auditable, HasUuids, SoftDeletes, HasDynamicSql;
    
    // Constantes de Estado Operativo
    const ESTADO_ENTREGADO = 6;
    const ESTADO_COMPLETADO = 9;
    const ESTADO_REVISION = 8;
    const ESTADO_PENDIENTE = 1;

    /**
     * Define which columns should be generated as UUIDs.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $fillable = [
        'uuid', 'corte_id', 'responsable_id', 'estado_id', 'empresa_id', 'nombre_completo', 'cedula',
        'sexo', 'telefono', 'direccion', 'provincia', 'municipio', 'empresa', 'rnc_empresa',
        'codigo', 'lote_id', 'proveedor_id', 'costo_entrega', 'poliza', 'contrato',
        'fecha_entrega_proveedor', 'liquidado', 'fecha_liquidacion', 'recibo_liquidacion',
        'fecha_entrega_safesure', 'lote_liquidacion_id',
        'provincia_id', 'municipio_id', 'reasignado', 'firebase_synced_at',
        'firebase_sync_status', 'firebase_sync_version', 'firebase_error_log',
        'updated_from', 'last_updated_by', 'conflict_status', 'remote_version',
        'corte_nombre', 'estado_nombre_remote', 'responsable_nombre_remote', 'last_sync_hash'
    ];

    protected $casts = [
        'fecha_entrega_safesure' => 'datetime',
        'fecha_liquidacion' => 'datetime',
        'fecha_entrega_proveedor' => 'datetime',
        'liquidado' => 'boolean',
        'reasignado' => 'boolean',
        'firebase_synced_at' => 'datetime',
        'conflict_status' => 'boolean'
    ];
    

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ResponsableScope);

        // Lógica de versionamiento para sincronización
        static::updating(function ($model) {
            // Si el cambio es local (no viene marcado como sync desde el servicio/webhook)
            if (!isset($model->is_firebase_sync) || !$model->is_firebase_sync) {
                // Si ha cambiado algo relevante
                if ($model->isDirty()) {
                    $model->firebase_sync_status = 'pending';
                    $model->firebase_sync_version++;
                    $model->updated_from = 'local';
                    $model->last_updated_by = auth()->id() ?? 1;
                }
            }
        });
    }

    /**
     * Retorna la cédula con formato 000-0000000-0
     */
    public function getCedulaFormattedAttribute()
    {
        $val = preg_replace('/[^0-9]/', '', $this->cedula);
        if (strlen($val) === 11) {
            return substr($val, 0, 3) . '-' . substr($val, 3, 7) . '-' . substr($val, 10, 1);
        }
        return $this->cedula;
    }

    /**
     * Calcula los días transcurridos desde que se entregó a un proveedor
     */
    public function getDiasTranscurridosAttribute()
    {
        // Si no se ha entregado a un proveedor o ya está liquidado, no contamos días
        if (!$this->fecha_entrega_proveedor) return 0;
        
        /** @var Carbon $fecha */
        $fecha = $this->fecha_entrega_proveedor;
        return $fecha->diffInDays(now());
    }

    /**
     * Determina el color del semáforo basado en el SLA (20 días según usuario)
     */
    public function getSlaStatusAttribute()
    {
        if (in_array($this->estado_id, [6, 9])) return 'completado';
        if (!$this->fecha_entrega_proveedor) return 'pendiente';

        $dias = $this->dias_transcurridos;
        if ($dias >= 20) return 'critico'; // Rojo
        if ($dias >= 15) return 'alerta';   // Amarillo
        return 'en_tiempo';                // Verde
    }


    public function corte()
    {
        return $this->belongsTo(Corte::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipioRel()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function provinciaRel()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    /**
     * @deprecated Usar provinciaRel
     */
    public function provincia()
    {
        return $this->provinciaRel();
    }

    /**
     * @deprecated Usar municipioRel
     */
    public function municipio()
    {
        return $this->municipioRel();
    }

    /**
     * RESOLUCIÓN DE UBICACIÓN FINAL (Regla de Negocio)
     * Prioridad: Afiliado Directo > Empresa > Legacy
     */
    public function getProvinciaFinalAttribute()
    {
        // Si el afiliado tiene provincia_id directa, retornamos esa relación (modelo)
        if ($this->provincia_id) {
            return $this->provinciaRel; 
        }
        // Si no, retornamos la de la empresa
        return $this->empresaModel?->provinciaRel;
    }

    public function getMunicipioFinalAttribute()
    {
        if ($this->municipio_id) {
            return $this->municipioRel;
        }
        return $this->empresaModel?->municipioRel;
    }

    public function getDireccionPersonalAttribute()
    {
        return $this->direccion ?: 'SIN DIRECCIÓN PERSONAL';
    }

    public function getDireccionEmpresaAttribute()
    {
        return $this->empresaModel?->direccion ?: 'SIN DIRECCIÓN DE EMPRESA';
    }

    public function getDireccionFinalAttribute()
    {
        // Prioridad: Si hay dirección personal cargada explícitamente, esa manda.
        // Si no, usamos la de la empresa.
        return $this->direccion ?: ($this->empresaModel?->direccion ?: 'DIRECCIÓN NO DISPONIBLE');
    }

    public function getProvinciaNombreAttribute()
    {
        return $this->provincia_final?->nombre ?? ($this->attributes['provincia'] ?? 'SIN PROVINCIA');
    }

    public function getMunicipioNombreAttribute()
    {
        return $this->municipio_final?->nombre ?? ($this->attributes['municipio'] ?? 'SIN MUNICIPIO');
    }


    /**
     * Verifica si existe un historial previo de entrega exitosa para esta cédula
     */
    public function scopeHistorialEntrega($query, $cedula)
    {
        return $query->where('cedula', $cedula)
            ->whereIn('estado_id', [6, 9, 10]); // Incluyendo Liquidado si existe (suponiendo ID 10)
    }

    /**
     * Scope for finished affiliates (Entregado ID:6 or Completado ID:9)
     * as suggested by CMD to discount from Pending inventory.
     */
    public function scopeFinished($query)
    {
        return $query->whereIn('estado_id', [6, 9]);
    }

    public function empresaModel()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class);
    }

    public function loteLiquidacion()
    {
        return $this->belongsTo(LoteLiquidacion::class, 'lote_liquidacion_id');
    }

    public function evidenciasAfiliado()
    {
        return $this->hasMany(EvidenciaAfiliado::class);
    }

    // Scopes para Reporte Dual (Basados en el Responsable)
    public function scopeArs($query)
    {
        return $query->whereHas('responsable', function($q) {
            $q->where('nombre', 'LIKE', '%CMD%');
        });
    }

    public function scopeNoArs($query)
    {
        return $query->whereHas('responsable', function($q) {
            $q->where('nombre', 'NOT LIKE', '%CMD%');
        });
    }

    public function scopeEnEmpresaReal($query)
    {
        return $query->whereHas('empresaModel', function($q) {
            $q->where('es_verificada', true);
        });
    }

    public function scopeEnEmpresaFilial($query)
    {
        return $query->whereHas('empresaModel', function($q) {
            $q->where('es_filial', true);
        });
    }

    // Scope para métricas de Proveedores de Entrega
    public function scopeEntregadoProveedor($query)
    {
        return $query->whereNotNull('fecha_entrega_proveedor');
    }

    public function notas()
    {
        return $this->hasMany(NotaAfiliado::class);
    }

    public function despachoItems()
    {
        return $this->hasMany(DespachoItem::class);
    }

    /**
     * Motor de Cierre Automático (Protocolo CMD)
     * Regla 5.1: Un afiliado solo pasa a COMPLETADO (9) si:
     * - Carnet Entregado (estado 6)
     * - Acuse recibido o validado
     * - Formulario recibido o validado
     * - Sin documentos rechazados
     */
    public function autoEvaluarCierre()
    {
        // Solo evaluamos si está Entregado (6) o Pendiente de Recepción (7)
        if (!in_array($this->estado_id, [6, 7])) {
            return false;
        }

        $evidencias = $this->evidenciasAfiliado()->get();

        $hasRechazados = $evidencias->where('status', 'rechazado')->isNotEmpty();
        if ($hasRechazados) {
            return false;
        }

        $hasAcuse = $evidencias->where('tipo_documento', 'acuse')->whereIn('status', ['recibido', 'validado'])->isNotEmpty();
        $hasFormulario = $evidencias->where('tipo_documento', 'formulario')->whereIn('status', ['recibido', 'validado'])->isNotEmpty();

        if ($hasAcuse && $hasFormulario) {
            $this->estado_id = 9; // Completado
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Normaliza la dirección del afiliado eliminando caracteres extraños,
     * dobles espacios y estandarizando el formato.
     */
    public function normalizeAddress()
    {
        if (!$this->direccion) return;

        $search  = ['  ', '  ', ' ,', ',,', '#', 'N°', 'No.', 'Nro'];
        $replace = [' ', ' ', ',', ',', '', '', '', ''];
        
        $clean = str_replace($search, $replace, $this->direccion);
        $clean = trim($clean);
        $clean = mb_strtoupper($clean);
        
        $this->direccion = $clean;
    }
}
