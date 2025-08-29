<?php

use App\Modules\Facturacion\Http\Controllers\Cfdi_40\CfdiController;
use App\Modules\Facturacion\Http\Controllers\Complemento_Pago\PagoController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\DatoFiscalController;
use App\Modules\Facturacion\Http\Controllers\Retencion\RetencionController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\PacController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\SerieFolioController;
use App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController;
use App\Modules\Facturacion\Http\Controllers\CartaPorteController; // Added
use Illuminate\Support\Facades\Route;

// --- INICIO: Rutas de API para el Frontend ---
// Se definen explícitamente para evitar conflictos con prefijos de proveedor de servicios.
Route::get('api/catalogos', [CatalogosApiController::class, 'getAll'])->name('tenant.api.facturacion.catalogos');
Route::get('api/series', [CatalogosApiController::class, 'series'])->name('tenant.api.facturacion.series');
Route::get('api/clientes/search', [CatalogosApiController::class, 'searchClients'])->name('tenant.api.facturacion.clientes.search');
Route::get('api/productos-servicios/search', [CatalogosApiController::class, 'productosServicios'])->name('tenant.api.facturacion.productos-servicios.search');
Route::get('api/sat-catalogs/{catalogName}', [CatalogosApiController::class, 'getSatCatalog'])->name('tenant.api.facturacion.sat-catalogs.get');
// --- FIN: Rutas de API para el Frontend ---


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

// Rutas para descarga de archivos
Route::get('cfdis/{cfdi}/pdf', [CfdiController::class, 'downloadPdf'])->name('cfdis.download.pdf');
Route::get('cfdis/{cfdi}/xml', [CfdiController::class, 'downloadXml'])->name('cfdis.download.xml');
Route::post('cfdis/{cfdi}/cancelar', [CfdiController::class, 'cancelar'])->name('cfdis.cancelar');


Route::resource('cfdis', CfdiController::class);

// Rutas de Complementos de Pago
Route::get('pagos/search-invoices', [PagoController::class, 'searchInvoices'])->name('pagos.search.invoices');
Route::post('pagos/{pago}/timbrar', [PagoController::class, 'timbrar'])->name('pagos.timbrar');
Route::post('pagos/{pago}/cancelar', [PagoController::class, 'cancelar'])->name('pagos.cancelar');
Route::get('pagos/{pago}/xml', [PagoController::class, 'downloadXml'])->name('pagos.download.xml');
Route::get('pagos/{pago}/pdf', [PagoController::class, 'downloadPdf'])->name('pagos.download.pdf');

Route::resource('pagos', PagoController::class);

// Rutas de Carta Porte (Added)
Route::resource('cartaporte', CartaPorteController::class)->except([
    'destroy'
]);

Route::post('cartaporte/draft', [CartaPorteController::class, 'storeAsDraft'])->name('cartaporte.storeAsDraft');

// Grupo de rutas para Retenciones (Nuevo)
Route::prefix('retenciones')->name('retenciones.')->group(function () {
    Route::resource('/', RetencionController::class)->parameters(['' => 'retencion']);
});

// Rutas de Configuración del Módulo
Route::prefix('configuracion')->name('configuracion.')->group(function () {
    // Refactorizado a Route::resource para mayor claridad
    Route::resource('datos-fiscales', DatoFiscalController::class);
    Route::resource('pacs', PacController::class);
    Route::resource('series-folios', SerieFolioController::class);
});
