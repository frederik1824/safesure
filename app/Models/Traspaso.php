<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Traspaso extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'traspasos';

    protected $fillable = [
        'firebase_document_id',
        'nombre_afiliado',
        'cedula_afiliado',
        'fecha_nacimiento',
        'sexo',
        'agente',
        'estado',
        'cantidad_dependientes',
        'fecha_solicitud',
        'fecha_efectivo',
        'periodo',
        'status_unipago',
        'motivo_rechazo',
        'fecha_rechazo',
        'sync_status',
        'firebase_updated_at',
        'synced_at',
        'source_system',
        'local_updated_at'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_efectivo' => 'date',
        'fecha_rechazo' => 'date',
        'fecha_nacimiento' => 'date',
        'firebase_updated_at' => 'datetime',
        'synced_at' => 'datetime',
        'local_updated_at' => 'datetime',
        'cantidad_dependientes' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        // Cuando se guarda un traspaso, buscar si existe el afiliado con la misma cédula
        // y actualizarle la fecha de nacimiento y el sexo si están vacíos!
        static::saved(function ($model) {
            $cleanCedula = preg_replace('/[^0-9]/', '', $model->cedula_afiliado);
            if ($cleanCedula) {
                $afiliado = \App\Models\Afiliado::withoutGlobalScopes()
                    ->whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanCedula])
                    ->first();
                if ($afiliado) {
                    $changed = false;
                    if (empty($afiliado->fecha_nacimiento) && $model->fecha_nacimiento) {
                        $afiliado->fecha_nacimiento = $model->fecha_nacimiento;
                        $changed = true;
                    }
                    if (empty($afiliado->sexo) && $model->sexo) {
                        $afiliado->sexo = $model->sexo;
                        $changed = true;
                    }
                    if ($changed) {
                        $afiliado->saveQuietly();
                    }
                }
            }
        });
    }
}
