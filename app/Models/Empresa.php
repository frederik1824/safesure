<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use Auditable, HasUuids, SoftDeletes;
    
    /**
     * Define which columns should be generated as UUIDs.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new \App\Scopes\EmpresaScope);
    }

    /**
     * Get the route key name for the model.
     * Reversed to UUID as per user instruction.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }



    protected $fillable = [
        'uuid', 'nombre', 'rnc', 'direccion', 'telefono', 'es_real', 'es_filial',
        'provincia_id', 'municipio_id',
        'contacto_nombre', 'contacto_puesto', 'contacto_telefono', 'contacto_email',
        'comision_tipo', 'comision_valor',
        'promotor_id', 'estado_contacto',
        'latitude', 'longitude', 'google_maps_url',
        // Legacy fields marked for future removal
        'provincia', 'municipio', 'firebase_synced_at', 'es_verificada' 
    ];

    protected $casts = [
        'es_real' => 'boolean',
        'es_filial' => 'boolean',
        'es_verificada' => 'boolean',
        'comision_valor' => 'decimal:2',
        'firebase_synced_at' => 'datetime',
    ];

    /**
     * Determina si la empresa requiere atención basada en la última interacción
     */
    public function getSlaStatusAttribute()
    {
        $ultimaInteraccion = $this->interacciones()->latest()->first();
        
        if (!$ultimaInteraccion) {
            $clase = 'critical';
            $mensaje = 'Sin actividad registrada';
            $dias = null;
        } else {
            $dias = $ultimaInteraccion->created_at->diffInDays(now());
            if ($dias >= 15) {
                $clase = 'critical';
                $mensaje = "Inactiva por $dias días";
            } elseif ($dias >= 7) {
                $clase = 'warning';
                $mensaje = "Revisión pendiente ($dias días)";
            } else {
                $clase = 'good';
                $mensaje = 'Al día';
            }
        }

        return (object) [
            'level' => $clase,
            'message' => $mensaje,
            'days' => $dias,
            'color' => match($clase) {
                'critical' => 'rose',
                'warning' => 'amber',
                'good' => 'emerald',
            }
        ];
    }

    public function promotor()
    {
        return $this->belongsTo(User::class, 'promotor_id');
    }

    public function interacciones()
    {
        return $this->hasMany(InteraccionEmpresa::class)->orderBy('fecha_contacto', 'desc');
    }

    /**
     * Relación optimizada para obtener solo la última interacción
     */
    public function latestInteraccion()
    {
        return $this->hasOne(InteraccionEmpresa::class)->latestOfMany('fecha_contacto');
    }

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }

    public function provinciaRel()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function municipioRel()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    /**
     * @deprecated Usar provinciaRel()
     */
    public function provincia()
    {
        return $this->provinciaRel();
    }

    /**
     * @deprecated Usar municipioRel()
     */
    public function municipio()
    {
        return $this->municipioRel();
    }

    /**
     * Resolución de Ruta Inteligente (CMD Protocol)
     * Permite buscar por RNC (prioridad), UUID o ID interno.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('rnc', $value)
            ->orWhere('uuid', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    /**
     * Mutador para extraer coordenadas automáticamente de la URL de Google Maps
     * Soporta enlaces largos y cortos (goo.gl / maps.app.goo.gl)
     */
    public function setGoogleMapsUrlAttribute($value)
    {
        $this->attributes['google_maps_url'] = $value;

        if (empty($value)) return;

        try {
            // Resolver URL corta si aplica
            $finalUrl = $value;
            if (str_contains($value, 'goo.gl') || str_contains($value, 'maps.app.goo.gl')) {
                // Usamos una petición HEAD rápida para seguir redirecciones
                $response = \Illuminate\Support\Facades\Http::withOptions([
                    'allow_redirects' => true,
                    'connect_timeout' => 5
                ])->get($value);
                $finalUrl = $response->effectiveUri()->__toString();
            }

            // Patrón 1: @lat,lng (Formato estándar de Maps)
            if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $this->attributes['latitude'] = $matches[1];
                $this->attributes['longitude'] = $matches[2];
            }
            // Patrón 2: ll=lat,lng (Formato de consulta legacy)
            elseif (preg_match('/ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $this->attributes['latitude'] = $matches[1];
                $this->attributes['longitude'] = $matches[2];
            }
            // Patrón 3: q=lat,lng (Búsqueda por coordenadas)
            elseif (preg_match('/q=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
                $this->attributes['latitude'] = $matches[1];
                $this->attributes['longitude'] = $matches[2];
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("No se pudo extraer geodatos de la URL: $value. Error: " . $e->getMessage());
        }
    }
}
