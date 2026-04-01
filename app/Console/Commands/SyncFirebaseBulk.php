<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Observers\AfiliadoObserver;
use App\Observers\EmpresaObserver;

class SyncFirebaseBulk extends Command
{
    /**
     * @var string
     */
    protected $signature = 'firebase:sync-bulk {--afiliados : Sincronizar todos los afiliados} {--empresas : Sincronizar todas las empresas} {--all : Sincronizar todo}';

    /**
     * @var string
     */
    protected $description = 'Sincronización masiva de datos con Firestore (REST)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $syncAll = $this->option('all');
        $syncAfiliados = $syncAll || $this->option('afiliados');
        $syncEmpresas = $syncAll || $this->option('empresas');

        if (!$syncAfiliados && !$syncEmpresas) {
            $this->error("Debes especificar qué sincronizar: --afiliados, --empresas o --all");
            return 1;
        }

        if ($syncEmpresas) {
            $this->info("Iniciando sincronización masiva de EMPRESAS...");
            $observer = app(EmpresaObserver::class);
            
            Empresa::whereNotNull('rnc')->chunk(100, function ($empresas) use ($observer) {
                foreach ($empresas as $empresa) {
                    $this->line(" - Empresa: {$empresa->nombre}");
                    $observer->saved($empresa);
                }
            });
            $this->info("✅ Empresas sincronizadas.");
        }

        if ($syncAfiliados) {
            $this->info("\nIniciando sincronización masiva de AFILIADOS...");
            $observer = app(AfiliadoObserver::class);
            
            Afiliado::whereNotNull('cedula')->chunk(100, function ($afiliados) use ($observer) {
                foreach ($afiliados as $afiliado) {
                    $this->line(" - Afiliado: {$afiliado->nombre_completo} [{$afiliado->cedula}]");
                    $observer->saved($afiliado);
                }
            });
            $this->info("✅ Afiliados sincronizados.");
        }

        $this->info("\n¡Sincronización masiva completada con éxito!");
        return 0;
    }
}
