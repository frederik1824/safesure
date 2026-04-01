<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Municipio;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Responsable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FirebaseSyncPull extends Command
{
    protected $signature = 'firebase:pull-all {--full : Descargar todo ignorando fechas de cambio}';
    protected $description = 'Consumir solo registros nuevos o modificados desde Firebase Cloud (Diferencial)';

    private $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle()
    {
        $tipo = $this->option('full') ? 'TOTAL' : 'DIFERENCIAL';
        $this->info("🚀 Iniciando consumo {$tipo} desde Firebase Cloud...");

        // Desactivar llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Catálogos (Estos siempre se descargan completo por ser pequeños y críticos)
        $this->syncCollection('provincias', Provincia::class, true);
        $this->syncCollection('municipios', Municipio::class, true);
        $this->syncCollection('cortes', Corte::class, true);
        $this->syncCollection('estados', Estado::class, true);
        $this->syncCollection('responsables', Responsable::class, true);

        // 2. Data Operativa (Diferencial por defecto)
        $this->syncCollection('empresas', Empresa::class, $this->option('full'));
        $this->syncCollection('afiliados', Afiliado::class, $this->option('full'));

        // Reactivar llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('✅ Sincronización diferencial finalizada. Safesure está sincronizado.');
    }

    private function syncCollection($collectionName, $modelClass, $forceFull = false)
    {
        $this->info("📥 Sincronizando: {$collectionName}...");
        
        try {
            $lastUpdate = $modelClass::max('updated_at');
            
            if ($lastUpdate && !$forceFull) {
                // Modo Diferencial: Solo lo que se actualizó después de nuestro último registro local
                $this->comment("   -> Buscando cambios posteriores a: {$lastUpdate}");
                $documents = $this->syncService->queryDocuments($collectionName, 'updated_at', 'GREATER_THAN', (string)$lastUpdate);
            } else {
                // Modo Full
                $this->comment("   -> Descarga total del catálogo.");
                $documents = $this->syncService->listDocuments($collectionName);
            }
            
            if (empty($documents)) {
                $this->warn("   - Sin registros nuevos en {$collectionName}.");
                return;
            }

            $bar = $this->output->createProgressBar(count($documents));
            $bar->start();

            foreach ($documents as $doc) {
                try {
                    if (!isset($doc['fields'])) continue;
                    
                    $fields = $doc['fields'];
                    $id = basename($doc['name']);
                    
                    $data = $this->mapFirestoreToEloquent($fields);
                    
                    if ($modelClass === Afiliado::class) {
                        $modelClass::updateOrCreate(['cedula' => $id], $data);
                    } else {
                        // Usamos DB directa para evitar validaciones de Eloquent si es necesario
                        // pero updateOrCreate suele ser suficiente si quitamos el unique
                        $modelClass::updateOrCreate(['id' => (int)$id], $data);
                    }
                } catch (\Exception $e) {
                    // Loguear error individual pero seguir con el resto
                    Log::warning("Falla en documento {$id} de {$collectionName}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

        } catch (\Exception $e) {
            $this->error("\n❌ Error fatal en {$collectionName}: " . $e->getMessage());
        }
    }

    private function mapFirestoreToEloquent($fields)
    {
        $data = [];
        foreach ($fields as $key => $value) {
            $type = array_key_first($value);
            $val = $value[$type];
            
            if ($type === 'integerValue') $val = (int)$val;
            if ($type === 'doubleValue') $val = (float)$val;
            if ($type === 'booleanValue') $val = (bool)$val;
            if ($type === 'timestampValue') $val = \Carbon\Carbon::parse($val);

            $data[$key] = $val;
        }
        return $data;
    }
}
