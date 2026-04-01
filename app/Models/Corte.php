<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corte extends Model
{
    use \App\Traits\Auditable;
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }
}
