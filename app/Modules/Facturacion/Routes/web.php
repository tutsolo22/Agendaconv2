<?php

use App\Modules\Facturacion\Http\Controllers\Cfdi_40\CfdiController;
use App\Modules\Facturacion\Http\Controllers\Complemento_Pago\PagoController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\DatoFiscalController;
use App\Modules\Facturacion\Http\Controllers\Retencion\RetencionController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\PacController;
use App\Modules\Facturacion\Http\Controllers\Configuracion\SerieFolioController;
use App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController;
use App\Modules\Facturacion\Http\Controllers\CartaPorte\CartaPorteController;
use App\Modules\Facturacion\Http\Controllers\Nomina\NominaController;
use Illuminate\Support\Facades\Route;

Route::prefix('tenant/facturacion')->name('tenant.facturacion.')->group(function () {
    // Rutas de Comprobantes
    Route::get('cfdis/create-credit-note/{factura}', [CfdiController::class, 'createCreditNote'])->name('cfdis.create-credit-note');
    Route::get('cfdis/create-global', [CfdiController::class, 'createGlobal'])->name('cfdis.create-global');
    Route::get('cfdis/search-ventas', [CfdiController::class, 'searchVentas'])->name('cfdis.search-ventas');
    Route::post('cfdis/store-global', [CfdiController::class, 'storeGlobal'])->name('cfdis.store-global');

    // Rutas para descarga de archivos
    Route::get('cfdis/{cfdi}/pdf', [CfdiController::class, 'downloadPdf'])->name('cfdis.download.pdf');
    Route::get('cfdis/{cfdi}/xml', [CfdiController::class, 'downloadXml'])->name('cfdis.download.xml');
    Route::post('cfdis/{cfdi}/cancelar', [CfdiController::class, 'cancelar'])->name('cfdis.cancelar');
    Route::post('cfdis/{cfdi}/enviar-correo', [CfdiController::class, 'enviarCorreo'])->name('cfdis.enviar-correo');


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

    // Rutas de Nómina (Nuevo)
    Route::resource('nomina', NominaController::class);

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
});