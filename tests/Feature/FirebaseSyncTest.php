<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\CloudSyncCheckpoint;
use App\Models\FirebaseSyncLog;
use App\Services\FirebaseSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;

class FirebaseSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic dependencies directly via query builder to avoid Eloquent events/Firebase sync triggers
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'id' => 1,
            'name' => 'Administrador',
            'email' => 'admin@arscmd.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('responsables')->insert([
            'id' => 1,
            'nombre' => 'SAFESURE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('cortes')->insert([
            'id' => 1,
            'nombre' => 'Corte Inicial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $estados = [
            ['id' => 1, 'nombre' => 'Pendiente', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Contactado', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'En ruta', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'No localizado', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'Reprogramado', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nombre' => 'Carnet entregado', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nombre' => 'Pendiente de recepción', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'nombre' => 'Cierre parcial', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'nombre' => 'Completado', 'es_final' => true, 'created_at' => now(), 'updated_at' => now()],
        ];
        \Illuminate\Support\Facades\DB::table('estados')->insert($estados);
    }

    /**
     * Test resilient cedula lookup (matching with or without hyphens).
     */
    public function test_resilient_cedula_lookup(): void
    {
        // 1. Setup Firebase mock to handle creation job and subsequent batch retrieval
        $this->mock(FirebaseSyncService::class, function (MockInterface $mock) {
            $mock->shouldReceive('isCircuitOpen')->andReturn(false);
            $mock->shouldReceive('ping')->andReturn(true);
            $mock->shouldReceive('setSyncLog')->andReturn(null);
            
            // Mock syncData called by the model observer
            $mock->shouldReceive('syncData')->andReturn(true);

            // Mock getCollection for static catalogs
            $mock->shouldReceive('getCollection')->andReturn([]);
            
            // Mock getQueryCount to return 1 record to sync
            $mock->shouldReceive('getQueryCount')->andReturn(1);
            
            // Mock getCollectionBatched to return the affiliate with clean cedula
            $mock->shouldReceive('getCollectionBatched')->once()->andReturn([
                'data' => [
                    [
                        'firebase_id' => '00112345678',
                        'cedula' => '00112345678',
                        'nombre_completo' => 'Juan Perez Modificado',
                        'firebase_sync_version' => 2,
                        'firebase_updated_at_meta' => now()->toIso8601String(),
                    ]
                ],
                'nextPageToken' => null
            ]);
            
            // Mock syncLocalModel which will be called during pulling
            $mock->shouldReceive('syncLocalModel')->once()->andReturnUsing(function($model, $mapped, $force) {
                $model->nombre_completo = $mapped['nombre_completo'];
                $model->firebase_sync_status = 'synced';
                $model->saveQuietly();
                return true;
            });

            // Mock deleteDocument
            $mock->shouldReceive('deleteDocument')->andReturn(true);
        });

        // 2. Create a local affiliate with hyphens in the database
        $afiliado = Afiliado::create([
            'nombre_completo' => 'Juan Perez',
            'cedula' => '001-1234567-8',
            'corte_id' => 1,
            'estado_id' => 1,
            'firebase_sync_status' => 'synced',
            'firebase_sync_version' => 1,
        ]);

        // 3. Create log in database
        $log = FirebaseSyncLog::create([
            'user_id' => 1,
            'type' => 'full',
            'status' => 'started',
            'started_at' => now(),
        ]);

        // 4. Run sync
        Artisan::call('firebase:pull-all', ['--full' => true, '--collection' => 'afiliados', '--log-id' => $log->id]);

        // 5. Assert database state: should NOT create a new record, should update the existing one!
        $this->assertEquals(1, Afiliado::withoutGlobalScopes()->count());
        $this->assertEquals('Juan Perez Modificado', Afiliado::withoutGlobalScopes()->first()->nombre_completo);
    }

    /**
     * Test soft deletion of locally orphaned records in full sync.
     */
    public function test_soft_delete_orphans_in_full_sync(): void
    {
        // 1. Setup Firebase mock to handle creation job and return empty batch
        $this->mock(FirebaseSyncService::class, function (MockInterface $mock) {
            $mock->shouldReceive('isCircuitOpen')->andReturn(false);
            $mock->shouldReceive('ping')->andReturn(true);
            $mock->shouldReceive('setSyncLog')->andReturn(null);
            
            // Mock syncData called by the model observer
            $mock->shouldReceive('syncData')->andReturn(true);

            // Mock getCollection for static catalogs
            $mock->shouldReceive('getCollection')->andReturn([]);
            
            $mock->shouldReceive('getQueryCount')->andReturn(0);
            
            // Return empty data
            $mock->shouldReceive('getCollectionBatched')->once()->andReturn([
                'data' => [],
                'nextPageToken' => null
            ]);

            // Mock deleteDocument to be called when the local orphan is soft-deleted
            $mock->shouldReceive('deleteDocument')->once()->with('afiliados', '001-0000000-0')->andReturn(true);
        });

        // 2. Create a local affiliate that doesn't exist in Firebase anymore
        $afiliado = Afiliado::create([
            'nombre_completo' => 'Juan Perez Borrado',
            'cedula' => '001-0000000-0',
            'corte_id' => 1,
            'estado_id' => 1,
            'firebase_sync_status' => 'synced',
            'firebase_synced_at' => now()->subDay(), // Old sync date
        ]);

        $log = FirebaseSyncLog::create([
            'user_id' => 1,
            'type' => 'full',
            'status' => 'started',
            'started_at' => now(),
        ]);

        // 3. Run sync
        Artisan::call('firebase:pull-all', ['--full' => true, '--collection' => 'afiliados', '--log-id' => $log->id]);

        // 4. Assert local affiliate is soft-deleted!
        $this->assertSoftDeleted('afiliados', ['id' => $afiliado->id]);
    }
}
