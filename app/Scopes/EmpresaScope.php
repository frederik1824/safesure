<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EmpresaScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check() && Auth::user()->isGestora()) {
            $responsableId = Auth::user()->responsable_id;
            
            // Permitir ver si es una empresa verificada/real O si tiene afiliados de la gestora
            $builder->where(function($q) use ($responsableId) {
                $q->where('es_verificada', true)
                  ->orWhere('es_real', true)
                  ->orWhereHas('afiliados', function($query) use ($responsableId) {
                      $query->where('responsable_id', $responsableId);
                  });
            });
        }
    }
}
