<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Traspaso;
use Illuminate\Support\Facades\DB;

class CrossUpdateDobAndSex extends Command
{
    protected $signature = 'db:cross-update-dob';
    protected $description = 'Cross-updates missing DOB and sex in afiliados table from traspasos table.';

    public function handle()
    {
        $this->info("Iniciando cruce de datos de Traspasos a Afiliados...");
        
        $this->info("Ejecutando cruce de datos masivo ultra-rápido a nivel de base de datos...");
        
        $result = Afiliado::crossUpdateFromTraspasos();
        
        if (is_int($result)) {
            $this->info("¡Completado! Se cruzaron y actualizaron {$result} afiliados.");
        } else {
            $this->info("¡Completado! Cruce de datos finalizado exitosamente.");
        }
        return 0;
    }
}
