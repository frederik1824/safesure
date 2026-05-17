<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tarea Nocturna: Conciliación de datos con Firebase
Schedule::command('firebase:conciliate')->dailyAt('02:00');

// Tarea Crítica: Reintento de sincronización pendiente o fallida
Schedule::command('firebase:retry')->everyFiveMinutes()->withoutOverlapping();

// Sincronización Automática Incremental de Traspasos (Módulo Receptor)
Schedule::command('firebase:sync-traspasos')->everyTenMinutes()->withoutOverlapping();

// Tarea de Limpieza: Resetear Circuit Breaker si quedó abierto
Schedule::call(function () {
    Cache::forget('firebase_circuit_open');
    Cache::forget('firebase_failure_count');
})->hourly();

// Mantenimiento de Datos: Purgar logs antiguos de sincronización (Domingos a las 2 AM)
Schedule::command('nexus:purge-logs --days=30')->weeklyOn(0, '02:00');

