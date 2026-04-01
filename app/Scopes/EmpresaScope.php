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
            
            // Filtro Estricto: Solo empresas que tienen afiliados que pertenecen a esta gestora
            $builder->whereHas('afiliados', function($query) use ($responsableId) {
                $query->where('responsable_id', $responsableId);
            });
        }
    }
}
