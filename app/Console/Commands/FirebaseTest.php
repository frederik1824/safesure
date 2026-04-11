<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;

class FirebaseTest extends Command
{
    protected $signature = 'firebase:test';
    protected $description = 'Verifica la conexión y autenticación con Firebase Firestore';

    public function handle(FirebaseSyncService $syncService)
    {
        $this->info('--- Probando Conexión con Firebase ---');
        
        $projectId = env('FIREBASE_PROJECT_ID');
        $credentials = env('FIREBASE_CREDENTIALS');
        
        $this->line("Proyecto: <comment>{$projectId}</comment>");
        $this->line("Ruta Credenciales: <comment>{$credentials}</comment>");

        $this->info('Intentando obtener Token de Acceso...');
        
        // El método getAccessToken es protegido, pero syncData lo usa internamente.
        // Vamos a probar haciendo un push de un documento de prueba.
        $testData = [
            'test_sync' => true,
            'timestamp' => now()->toIso8601String(),
            'message' => 'System connection test'
        ];

        $success = $syncService->syncData($testData, 'system_logs', 'connectivity_test');

        if ($success) {
            $this->info('✅ ¡ÉXITO! Conexión establecida y datos de prueba enviados a la colección [system_logs].');
        } else {
            $this->error('❌ FALLÓ la conexión. Revisa los logs de Laravel (storage/logs/laravel.log) para ver el error detallado.');
            $this->line('Causas comunes:');
            $this->line('1. La variable FIREBASE_CREDENTIALS_JSON no está definida en Dokploy.');
            $this->line('2. El archivo JSON de credenciales no existe (si no usas la variable de entorno).');
            $this->line('3. El JSON está mal formado o no tiene la "private_key".');
            $this->line('4. El ID del proyecto en el .env no coincide con el del JSON.');
        }

        return $success ? 0 : 1;
    }
}
