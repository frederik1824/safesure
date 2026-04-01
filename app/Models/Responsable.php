<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    use \App\Traits\Auditable;
    protected $fillable = ['nombre', 'descripcion', 'precio_entrega', 'activo', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function afiliados()
    {
        return $this->hasMany(Afiliado::class);
    }
}
