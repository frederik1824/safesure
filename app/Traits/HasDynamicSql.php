<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasDynamicSql
{
    /**
     * Devuelve la expresión SQL para formatear una fecha según el driver activo.
     */
    public function scopeSelectMonthName($query, $column, $alias = 'mes')
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            return $query->selectRaw("TO_CHAR({$column}, 'Month') as {$alias}");
        }
        
        return $query->selectRaw("DATE_FORMAT({$column}, '%M') as {$alias}");
    }

    /**
     * Devuelve la expresión SQL para agrupar por mes según el driver.
     */
    public function scopeGroupByMonth($query, $column)
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            return $query->groupByRaw("TO_CHAR({$column}, 'Month')")
                         ->orderByRaw("MIN({$column}) ASC");
        }
        
        return $query->groupByRaw("mes")
                     ->orderByRaw("MIN({$column}) ASC");
    }
}
