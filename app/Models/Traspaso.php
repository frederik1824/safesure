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
        'agente',
        'estado',
        'cantidad_dependientes',
        'fecha_solicitud',
        'fecha_efectivo',
        'periodo',
        'status_unipago',
        'sync_status',
        'firebase_updated_at',
        'synced_at',
        'source_system',
        'local_updated_at'
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_efectivo' => 'date',
        'firebase_updated_at' => 'datetime',
        'synced_at' => 'datetime',
        'local_updated_at' => 'datetime',
        'cantidad_dependientes' => 'integer'
    ];
}
