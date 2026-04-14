<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Afiliado extends Model
{
    use Auditable, HasUuids, SoftDeletes;

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
        'provincia_id', 'municipio_id', 'reasignado', 'firebase_synced_at'
    ];

    protected $casts = [
        'fecha_entrega_safesure' => 'datetime',
        'fecha_liquidacion' => 'datetime',
        'fecha_entrega_proveedor' => 'datetime',
        'liquidado' => 'boolean',
        'reasignado' => 'boolean',
        'firebase_synced_at' => 'datetime'
    ];
    
    public function getStatusColorClassAttribute()
    {
        $estadoId = $this->estado_id;
        
        // IDs 6 y 9 definidos por CMD como estados de terminacion
        if (in_array($estadoId, [6, 9]) || $this->estado?->es_final) {
            return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        }
        
        $estadoNombre = strtolower($this->estado?->nombre ?? 'pendiente');
        return match($estadoNombre) {
            'pendiente'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'carnet entregado' => 'bg-blue-100 text-blue-700 border-blue-200',
            'entregado'  => 'bg-blue-100 text-blue-700 border-blue-200',
            'cancelado'  => 'bg-rose-100 text-rose-700 border-rose-200',
            'en proceso' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            default      => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Regla Estricta: Asegurar costo base al guardar si está completado
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new \App\Scopes\ResponsableScope);
        
        static::saving(function ($afiliado) {
            // Regla Inmutable (Protocolo CMD): Si el registro YA está en estado 9 (Completado)
            if ($afiliado->getOriginal('estado_id') == 9) {
                if ($afiliado->isDirty(['responsable_id', 'empresa_id', 'estado_id', 'cedula'])) {
                    throw new \Exception("Protocolo CMD: El expediente está COMPLETADO (ID 9) y es inmutable. No se permiten cambios de responsable, empresa, cédula o estado.");
                }
            }

            // Si el estado es uno de los terminados (6 o 9)
            if (in_array($afiliado->estado_id, [6, 9])) {
                // Si el costo es nulo o cero, forzamos la asignación del precio base
                if (is_null($afiliado->costo_entrega) || $afiliado->costo_entrega == 0) {
                    if ($afiliado->proveedor_id && $afiliado->proveedor?->precio_base > 0) {
                        $afiliado->costo_entrega = $afiliado->proveedor->precio_base;
                    } elseif ($afiliado->responsable_id && $afiliado->responsable?->precio_entrega > 0) {
                        $afiliado->costo_entrega = $afiliado->responsable->precio_entrega;
                    }
                }
            }
        });
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
     * Normaliza los campos de dirección eliminando abreviaturas comunes
     */
    public function normalizeAddress()
    {
        if (!$this->direccion) return;

        $replacements = [
            '/\bC\/\b/i' => 'Calle ',
            '/\bNo\.\b/i' => '#',
            '/\bEsq\.\b/i' => 'Esquina ',
            '/\bApt\.\b/i' => 'Apartamento ',
            '/\bRes\.\b/i' => 'Residencial ',
            '/\bAut\.\b/i' => 'Autopista ',
        ];

        $this->direccion = preg_replace(array_keys($replacements), array_values($replacements), $this->direccion);
        $this->direccion = trim(preg_replace('/\s+/', ' ', $this->direccion));
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
}
