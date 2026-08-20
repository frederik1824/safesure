<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'firebase/webhook'
        ]);
        
        $middleware->append(\App\Http\Middleware\MaintenanceLockMiddleware::class);
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\RequirePolicySignatures::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'access_module' => \App\Http\Middleware\CheckApplicationAccess::class,
            'app_access' => \App\Http\Middleware\CheckApplicationAccess::class,
            'safesure.auth' => \App\Http\Middleware\SafesureApiAuth::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Cerrar turnos olvidados antes de medianoche
        $schedule->command('asistencia:close-pending-shifts')->dailyAt('23:55');
        
        // Verificar ausencias y alertas críticas durante la jornada
        $schedule->command('asistencia:check-alerts')->everyFifteenMinutes();

        // Daemon de Auto-Sanación de Firebase (Resuelve Drifts en la madrugada)
        $schedule->command('firebase:auto-heal')->dailyAt('02:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            $errorCode = $e->getCode();
            $pdoCode = $e->errorInfo[1] ?? null; // For MySQL, PDO error 2002 is Connection Refused
            
            if ($errorCode == '2002' || $pdoCode === 2002 || str_contains($e->getMessage(), 'SQLSTATE[HY000] [2002]')) {
                return response()->view('errors.db-connection', ['exception' => $e], 500);
            }
        });
    })->create();
