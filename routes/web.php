<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulos de Configuración (Restringido - Spatie Permission)
    Route::middleware('permission:access_admin_panel')->group(function() {
        Route::get('catalogo', function() {
            return view('catalogo.index');
        })->name('catalogo.index');
        
        Route::resource('cortes', \App\Http\Controllers\CorteController::class);
        Route::resource('responsables', \App\Http\Controllers\ResponsableController::class);
        Route::resource('estados', \App\Http\Controllers\EstadoController::class);
        Route::resource('proveedores', \App\Http\Controllers\ProveedorController::class);
        
        // Centro de Sincronización Firebase
        Route::get('firebase-sync', [\App\Http\Controllers\FirebaseSyncController::class, 'index'])->name('admin.sync.index');
        Route::post('firebase-sync/trigger', [\App\Http\Controllers\FirebaseSyncController::class, 'trigger'])->name('admin.sync.trigger');
        
        Route::get('auditoria', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('admin.audit.index');
    });

    // Módulo de Empresas (Spatie Permission)
    Route::middleware('permission:manage_companies')->group(function() {
        Route::get('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'enrich'])->name('empresas.enrich');
        Route::post('empresas/enrich', [\App\Http\Controllers\EmpresaController::class, 'processEnrich'])->name('empresas.processEnrich');
        Route::resource('empresas', \App\Http\Controllers\EmpresaController::class);
        Route::post('empresas/{empresa}/interaccion', [\App\Http\Controllers\EmpresaController::class, 'storeInteraction'])->name('empresas.interaction');
    });
    
    Route::get('municipios/{provincia_id}', [\App\Http\Controllers\EmpresaController::class, 'getMunicipios'])->name('api.municipios');

    // Módulo de Afiliados
    Route::middleware('permission:manage_affiliates')->group(function() {
        // Búsqueda e Exportación de Afiliados (DEBEN ir antes del resource)
        Route::get('afiliados/check-duplicate', [\App\Http\Controllers\AfiliadoController::class, 'checkDuplicate'])->name('afiliados.check_duplicate');
        Route::get('afiliados/search-ajax', [\App\Http\Controllers\AfiliadoController::class, 'searchAjax'])->middleware('throttle:60,1')->name('afiliados.search_ajax');
        Route::get('afiliados/export', [\App\Http\Controllers\AfiliadoController::class, 'export'])->name('afiliados.export');
        Route::post('afiliados/sanitize', [\App\Http\Controllers\AfiliadoController::class, 'sanitizeAddresses'])->name('afiliados.sanitize');
        
        // Afiliados Segmentados
        Route::get('afiliados/cmd', [\App\Http\Controllers\AfiliadoController::class, 'indexCmd'])->name('afiliados.cmd');
        Route::get('afiliados/otros', [\App\Http\Controllers\AfiliadoController::class, 'indexOtros'])->name('afiliados.otros');
        Route::get('afiliados/salida-inmediata', [\App\Http\Controllers\AfiliadoController::class, 'indexSalidaInmediata'])->name('afiliados.salida_inmediata');

        // Procesos Bulk y CRUD
        Route::post('afiliados/bulk-assign', [\App\Http\Controllers\AfiliadoController::class, 'bulkAssign'])->name('afiliados.bulk_assign');
        Route::post('afiliados/bulk-company', [\App\Http\Controllers\AfiliadoController::class, 'bulkCompany'])->name('afiliados.bulk_company');
        Route::post('afiliados/{uuid}/reassign', [\App\Http\Controllers\AfiliadoController::class, 'reassign'])->name('afiliados.reassign');
        Route::post('afiliados/bulk-status', [\App\Http\Controllers\AfiliadoController::class, 'bulkStatus'])->name('afiliados.bulk_status');
        Route::post('afiliados/{uuid}/estado_single', [\App\Http\Controllers\AfiliadoController::class, 'updateStatus'])->name('afiliados.update_status');
        Route::post('afiliados/{uuid}/evidencia', [\App\Http\Controllers\AfiliadoController::class, 'uploadEvidencia'])->name('afiliados.upload_evidencia');
        Route::post('afiliados/{uuid}/reopen', [\App\Http\Controllers\AfiliadoController::class, 'reopen'])->name('afiliados.reopen');
        Route::post('afiliados/{afiliado}/confirm-reception', [\App\Http\Controllers\AfiliadoController::class, 'confirmReception'])->name('afiliados.confirm_reception');
        Route::resource('afiliados', \App\Http\Controllers\AfiliadoController::class)->whereUuid('afiliado');
    });

    // Módulo de Evidencias
    Route::middleware('permission:manage_evidencias')->group(function() {
        Route::get('evidencias', [\App\Http\Controllers\EvidenciaController::class, 'index'])->name('evidencias.index');
        Route::post('evidencias/{evidencia}/status', [\App\Http\Controllers\EvidenciaController::class, 'updateStatus'])->name('evidencias.status');
        Route::post('evidencias/physical', [\App\Http\Controllers\EvidenciaController::class, 'validatePhysical'])->name('evidencias.physical');
    });

    // Módulo de Lotes (Batch)
    Route::middleware('permission:manage_logistics')->group(function() {
        Route::get('lotes', [\App\Http\Controllers\LoteController::class, 'index'])->name('lotes.index');
        Route::post('lotes/proceso', [\App\Http\Controllers\LoteController::class, 'process'])->name('lotes.process');
    });

    // Módulo de Cierre (Carga de Acuses Físicos)
    Route::middleware('permission:manage_closures')->group(function() {
        Route::get('cierre', [\App\Http\Controllers\CierreController::class, 'index'])->name('cierre.index');
        Route::post('cierre', [\App\Http\Controllers\CierreController::class, 'store'])->name('cierre.store');
    });

    // Gestión de Usuarios y Roles (Super-Admin y Admin)
    Route::middleware('permission:manage_users')->group(function() {
        Route::resource('usuarios', \App\Http\Controllers\UserController::class)->names('usuarios');
        Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['index', 'edit', 'update']);
    });

    // Módulo de Liquidación (Spatie Permission)
    Route::middleware('permission:manage_liquidations')->group(function() {
        Route::get('liquidacion', \App\Http\Controllers\LiquidacionController::class)->name('liquidacion.index');
        Route::post('liquidacion/proceso', [\App\Http\Controllers\LiquidacionController::class, 'process'])->name('liquidacion.process');
        Route::get('liquidacion/historial', [\App\Http\Controllers\LiquidacionController::class, 'history'])->name('liquidacion.history');
        Route::get('liquidacion/print/{recibo}', [\App\Http\Controllers\LiquidacionController::class, 'print'])->name('liquidacion.print');
    });

    // Módulo de Reportes
    Route::middleware('permission:view_reports')->group(function() {
        Route::get('reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/supervision', [\App\Http\Controllers\ReporteController::class, 'supervision'])->name('reportes.supervision');
        Route::get('reportes/export', [\App\Http\Controllers\ReporteController::class, 'export'])->name('reportes.export');
        Route::get('reportes/heatmap', [\App\Http\Controllers\ReporteController::class, 'heatmap'])->name('reportes.heatmap');
        Route::get('reportes/alertas-sla', [\App\Http\Controllers\ReporteController::class, 'slaAlerts'])->name('reportes.sla_alerts');
        Route::get('reportes/comparativa', [\App\Http\Controllers\ReporteController::class, 'comparison'])->name('reportes.comparativa');
    });

    // Modulo de Importación Masiva (Spatie Permission) - Vinculado a manage_affiliates
    Route::middleware('permission:manage_affiliates')->group(function() {
        Route::get('importar', [\App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('importar', [\App\Http\Controllers\ImportController::class, 'store'])->name('import.store');
        Route::get('importar/progreso/{lote_id}', [\App\Http\Controllers\ImportController::class, 'getProgress'])->name('import.progress');
        Route::get('importar/plantilla', [\App\Http\Controllers\ImportController::class, 'downloadTemplate'])->name('import.template');
    });

    // Notas de Afiliados
    Route::middleware('permission:manage_affiliates')->group(function() {
        Route::post('notas', [\App\Http\Controllers\NotaController::class, 'store'])->name('notas.store');
    });

    // Notificaciones
    Route::post('notifications/mark-all-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    })->name('notifications.markAllAsRead');

    // Módulo de Logística & Despacho
    Route::middleware('permission:manage_logistics')->group(function() {
        Route::get('logistica/dashboard', [\App\Http\Controllers\LogisticaDashboardController::class, 'index'])->name('logistica.dashboard');
        Route::resource('mensajeros', \App\Http\Controllers\MensajeroController::class);
        Route::resource('rutas', \App\Http\Controllers\RutaController::class);
        
        // Rutas específicas de Despacho antes del resource
        Route::get('despachos/crear', [\App\Http\Controllers\DespachoController::class, 'createBatch'])->name('despachos.create_batch');
        Route::post('despachos/proceso', [\App\Http\Controllers\DespachoController::class, 'processBatch'])->name('despachos.process_batch');
        Route::get('despachos/{despacho}/print', [\App\Http\Controllers\DespachoController::class, 'print'])->name('despachos.print');
        Route::post('despachos/{despacho}/status', [\App\Http\Controllers\DespachoController::class, 'updateStatus'])->name('despachos.update_status');
        Route::post('despachos/item/{item}/status', [\App\Http\Controllers\DespachoController::class, 'updateItemStatus'])->name('despachos.item_status');
        Route::resource('despachos', \App\Http\Controllers\DespachoController::class);
    });
});

require __DIR__.'/auth.php';
