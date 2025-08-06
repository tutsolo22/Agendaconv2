<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Facturacion Module Routes
|--------------------------------------------------------------------------
|
| The FacturacionServiceProvider already applies the 'tenant' prefix,
| the 'tenant.' name, and the necessary middleware.
|
*/

use App\Modules\Facturacion\Http\Controllers\CfdiController;
use App\Modules\Facturacion\Http\Controllers\DatoFiscalController;
use App\Modules\Facturacion\Http\Controllers\PacController;
use App\Modules\Facturacion\Http\Controllers\PagoController;
use App\Modules\Facturacion\Http\Controllers\SerieFolioController;
use App\Modules\Facturacion\Http\Controllers\VentaPublicoController;

Route::prefix('facturacion')->name('facturacion.')->group(function () {
    // --- Recursos de Facturas (CFDI) ---
    // Esto crea rutas como: tenant.facturacion.cfdis.index, .create, .store, etc.
    Route::resource('cfdis', CfdiController::class);

    // --- Rutas Adicionales para CFDI (acciones específicas) ---
    Route::post('/cfdis/{cfdi}/timbrar', [CfdiController::class, 'timbrar'])->name('cfdis.timbrar');
    Route::post('/cfdis/{cfdi}/cancelar', [CfdiController::class, 'cancelar'])->name('cfdis.cancelar');
    Route::get('/cfdis/{cfdi}/download/xml', [CfdiController::class, 'downloadXml'])->name('cfdis.download.xml');
    Route::get('/cfdis/{cfdi}/download/pdf', [CfdiController::class, 'downloadPdf'])->name('cfdis.download.pdf');
    Route::get('/cfdis/search-clients', [CfdiController::class, 'searchClients'])->name('cfdis.search.clients');
    Route::post('/cfdis/search-ventas', [CfdiController::class, 'searchVentas'])->name('cfdis.search.ventas');
    Route::post('/cfdis/{cfdi}/enviar-correo', [CfdiController::class, 'enviarPorCorreo'])->name('cfdis.enviar-correo');
    Route::get('/cfdis/{cfdi}/create-credit-note', [CfdiController::class, 'createCreditNote'])->name('cfdis.create-credit-note');

    // --- Recursos de Complementos de Pago ---
    // Esto crea rutas como: tenant.facturacion.pagos.index, .create, .store, etc.
    Route::resource('pagos', PagoController::class);

    // --- Rutas Adicionales para Pagos (acciones específicas) ---
    Route::get('/pagos/search-invoices', [PagoController::class, 'searchInvoices'])->name('pagos.search.invoices');
    Route::post('/pagos/{pago}/timbrar', [PagoController::class, 'timbrar'])->name('pagos.timbrar');
    Route::post('/pagos/{pago}/cancelar', [PagoController::class, 'cancelar'])->name('pagos.cancelar');
    Route::get('/pagos/{pago}/download/xml', [PagoController::class, 'downloadXml'])->name('pagos.download.xml');
    Route::get('/pagos/{pago}/download/pdf', [PagoController::class, 'downloadPdf'])->name('pagos.download.pdf');

    // --- Rutas de Configuración y otras funcionalidades ---

    // Factura Global (acción única)
    Route::get('create-global', [CfdiController::class, 'createGlobal'])->name('create-global');
    Route::post('store-global', [CfdiController::class, 'storeGlobal'])->name('store-global');

    // Secciones de configuración (tratadas como recursos para futura expansión)
    Route::resource('ventas-publico', VentaPublicoController::class)->names('ventas-publico');
    Route::resource('datos-fiscales', DatoFiscalController::class)->only(['index', 'store'])->names('datos-fiscales');
    Route::resource('series-folios', SerieFolioController::class)->names('series-folios');
    Route::resource('pacs', PacController::class)->names('pacs');
});