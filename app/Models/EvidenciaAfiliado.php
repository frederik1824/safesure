<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaAfiliado extends Model
{

    protected $fillable = [
        'afiliado_id', 'tipo_documento', 'status', 'file_path', 'user_id', 'validated_by', 'observaciones', 'is_physical'
    ];

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($evidencia) {
            if ($evidencia->afiliado && $evidencia->afiliado->estado_id == 9) {
                throw new \Exception("Protocolo CMD: No se pueden eliminar evidencias de un expediente COMPLETADO.");
            }
        });

        static::saving(function ($evidencia) {
            // Regla de validación documental: No validar sin archivo físico (excepto si es validación física presencial)
            if (in_array($evidencia->status, ['recibido', 'validado']) && empty($evidencia->file_path)) {
                if (!isset($evidencia->is_physical) || !$evidencia->is_physical) {
                    throw new \Exception("Protocolo CMD: No se puede marcar un documento como 'recibido' o 'validado' sin haber adjuntado el archivo físico digital.");
                }
            }
        });

        static::saved(function ($evidencia) {
            // Regla de Cierre Automático: Disparar evaluación en el expediente
            if ($evidencia->afiliado) {
                $evidencia->afiliado->autoEvaluarCierre();
            }
        });
    }
}
