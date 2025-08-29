<?php

use App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de API para el Módulo de Facturación
|--------------------------------------------------------------------------
|
| Estas rutas son cargadas por el FacturacionServiceProvider.
| El prefijo 'tenant.' y el middleware de autenticación ya se aplican
| automáticamente. Este archivo agrupa las rutas para mantener el código limpio.
|
*/

// Agrupamos todas las rutas de la API de facturación bajo un prefijo y nombre común.
// El nombre base será 'tenant.api.facturacion.'
Route::prefix('facturacion')->name('facturacion.')->controller(CatalogosApiController::class)->group(function () {

    // Devuelve todos los catálogos estáticos del SAT (Formas de pago, Usos de CFDI, etc.)
    // Nombre: tenant.api.facturacion.catalogos
    Route::get('catalogos', 'getAll')->name('catalogos');

    // Devuelve las series y folios disponibles para el tenant.
    // Nombre: tenant.api.facturacion.series
    Route::get('series', 'series')->name('series');

    // Búsqueda de clientes por nombre o RFC.
    // Nombre: tenant.api.facturacion.clientes.search
    Route::get('clientes/search', 'searchClients')->name('clientes.search');

    // Búsqueda de productos/servicios del catálogo del SAT.
    // Nombre: tenant.api.facturacion.productos-servicios.search
    Route::get('productos-servicios/search', 'productosServicios')->name('productos-servicios.search');

    // Búsqueda de CFDI por folio o UUID
    // Nombre: tenant.api.facturacion.search-cfdis
    Route::get('search-cfdis', 'searchCfdis')->name('search-cfdis');

    // Búsqueda genérica de catálogos del SAT
    // Nombre: tenant.api.facturacion.sat-catalogs
    Route::get('sat-catalogs/{catalogName}', 'getSatCatalog')->name('sat-catalogs');
});

// --- RUTA DE DEPURACIÓN TEMPORAL ---
// Úsala para verificar si el servicio está obteniendo las formas de pago correctamente.
Route::get('/facturacion/debug/formas-pago', [CatalogosApiController::class, 'debugFormasPago'])->name('api.facturacion.debug.formas-pago');

// --- RUTA DE DEPURACIÓN PARA RETENCIONES ---
Route::get('/facturacion/debug/retenciones', [CatalogosApiController::class, 'debugRetenciones'])->name('api.facturacion.debug.retenciones');