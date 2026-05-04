<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Visibilidad de Empresas: Marcar como reales si tienen afiliados
        \App\Models\Empresa::whereHas('afiliados')->update(['es_real' => true]);

        // 2. Normalización de Ubicaciones: Mapear nombres a IDs si están vacíos
        $afiliadosFix = \App\Models\Afiliado::whereNull('provincia_id')
            ->whereNotNull('provincia')
            ->where('provincia', '!=', '')
            ->get();

        foreach($afiliadosFix as $af) {
            $prov = \App\Models\Provincia::where('nombre', 'like', trim($af->provincia))->first();
            if($prov) {
                $af->provincia_id = $prov->id;
                $mun = \App\Models\Municipio::where('provincia_id', $prov->id)
                    ->where('nombre', 'like', trim($af->municipio))
                    ->first();
                if($mun) {
                    $af->municipio_id = $mun->id;
                }
                $af->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
