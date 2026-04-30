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

// Tarea de Limpieza: Resetear Circuit Breaker si quedó abierto
Schedule::call(function () {
    Cache::forget('firebase_circuit_open');
    Cache::forget('firebase_failure_count');
})->hourly();
