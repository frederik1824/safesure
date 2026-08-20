<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SafesureApiController;

Route::middleware('safesure.auth')->prefix('v1/safesure')->group(function () {
    Route::get('/estados', [SafesureApiController::class, 'getEstados']);
    Route::get('/afiliados', [SafesureApiController::class, 'getAfiliados']);
    Route::get('/afiliados/{cedula}', [SafesureApiController::class, 'getAfiliadoByCedula']);
    Route::post('/afiliados/{cedula}/estado', [SafesureApiController::class, 'updateEstado']);
    Route::post('/afiliados/{cedula}/evidencia', [SafesureApiController::class, 'uploadEvidencia']);
});
