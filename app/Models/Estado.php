<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = ['nombre', 'es_final'];

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'estado_nuevo_id');
    }
}
