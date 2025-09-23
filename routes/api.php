<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Facturacion API
    Route::get('/facturacion/clientes/search', [CatalogosApiController::class, 'searchClients'])
         ->name('api.facturacion.clientes.search');

    // HexaFac API
    Route::prefix('hexafac/v1')->name('api.hexafac.v1.')->group(function () {
        // Facturas
        Route::post('facturas', [\App\Http\Controllers\Api\HexaFac\FacturaApiController::class, 'store'])->name('facturas.store');
        Route::post('facturas/{uuid}/cancelar', [\App\Http\Controllers\Api\HexaFac\FacturaApiController::class, 'cancelar'])->name('facturas.cancelar');
        Route::get('facturas/{uuid}', [\App\Http\Controllers\Api\HexaFac\FacturaApiController::class, 'show'])->name('facturas.show');

        // Clientes
        Route::get('clientes', [\App\Http\Controllers\Api\HexaFac\ClienteApiController::class, 'index'])->name('clientes.index');
        Route::post('clientes', [\App\Http\Controllers\Api\HexaFac\ClienteApiController::class, 'store'])->name('clientes.store');

        // Notas de Crédito
        Route::post('notas-credito', [\App\Http\Controllers\Api\HexaFac\NotaCreditoApiController::class, 'store'])->name('notas_credito.store');

        // Complementos de Pago
        Route::post('complementos-pago', [\App\Http\Controllers\Api\HexaFac\ComplementoPagoApiController::class, 'store'])->name('complementos_pago.store');
    });
});
