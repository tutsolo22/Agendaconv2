<?php

use App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/tenant/facturacion')->name('tenant.api.facturacion.')->group(function () {
    Route::controller(CatalogosApiController::class)->group(function() {
        Route::get('catalogos', 'getAll')->name('catalogos');
        Route::get('series', 'series')->name('series');
        Route::get('clientes/search', 'searchClients')->name('clientes.search');
        Route::get('productos-servicios/search', 'productosServicios')->name('productos-servicios.search');
        Route::get('search-cfdis', 'searchCfdis')->name('search-cfdis');
        Route::get('sat-catalogs/{catalogName}', 'getSatCatalog')->name('sat-catalogs');
        Route::get('codigopostal/{codigoPostal}', 'getCodigoPostalInfo')->name('codigopostal.info');
    });

    Route::prefix('nomina')->name('nomina.')->group(function() {
        Route::get('catalogs', [CatalogosApiController::class, 'getNominaCatalogs'])->name('catalogs');
        Route::get('search-empleados', [\App\Modules\Facturacion\Http\Controllers\Nomina\NominaController::class, 'searchEmpleados'])->name('search.empleados');
    });
});
