<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Facturacion\Http\Controllers\Cfdi_40\CfdiController;
use App\Modules\Facturacion\Http\Controllers\Complemento_Pago\PagoController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\DatoFiscalController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\PacController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\SerieFolioController;

/*
|--------------------------------------------------------------------------
| Web Routes for Facturacion Module
|--------------------------------------------------------------------------
|
| Aquí se registran todas las rutas de la interfaz de usuario para el módulo.
| El FacturacionServiceProvider se encarga de aplicar los prefijos
| 'tenant/facturacion' y los nombres 'tenant.facturacion.'.
*/

// Rutas de Comprobantes
Route::get('cfdis/create-credit-note/{factura}', [CfdiController::class, 'createCreditNote'])->name('cfdis.create-credit-note');
Route::get('cfdis/create-global', [CfdiController::class, 'createGlobal'])->name('cfdis.create-global');
Route::get('cfdis/search-ventas', [CfdiController::class, 'searchVentas'])->name('cfdis.search-ventas');
Route::post('cfdis/store-global', [CfdiController::class, 'storeGlobal'])->name('cfdis.store-global');
Route::resource('cfdis', CfdiController::class);

// Rutas de Complementos de Pago
Route::get('pagos/search-invoices', [PagoController::class, 'searchInvoices'])->name('pagos.search.invoices');
Route::resource('pagos', PagoController::class);

// Rutas de Configuración del Módulo
Route::prefix('configuracion')->name('configuracion.')->group(function () {

    // Rutas de Datos Fiscales
    Route::get('datos-fiscales', [DatoFiscalController::class, 'index'])->name('datos-fiscales.index');
    Route::get('datos-fiscales/create', [DatoFiscalController::class, 'create'])->name('datos-fiscales.create');
    Route::post('datos-fiscales', [DatoFiscalController::class, 'store'])->name('datos-fiscales.store');
    Route::get('datos-fiscales/{datoFiscal}/edit', [DatoFiscalController::class, 'edit'])->name('datos-fiscales.edit');
    Route::put('datos-fiscales/{datoFiscal}', [DatoFiscalController::class, 'update'])->name('datos-fiscales.update');
    Route::delete('datos-fiscales/{datoFiscal}', [DatoFiscalController::class, 'destroy'])->name('datos-fiscales.destroy');

    Route::resource('pacs', PacController::class);
    Route::resource('series-folios', SerieFolioController::class);
});
