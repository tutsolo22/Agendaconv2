<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenciaController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\SuperAdminUserController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Tenant\LicenciaHistorialController;
use App\Http\Controllers\Tenant\ConfigurationController;
use App\Http\Controllers\Tenant\DocumentUploadController;
use App\Http\Controllers\Tenant\ClienteController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Esta ruta genérica de dashboard es un fallback, pero los usuarios son redirigidos
// a sus dashboards específicos (admin o tenant) por la lógica de login y middleware.
//Route::get('/dashboard', function () {
  //  return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

// Grupo de rutas para el Super-Admin
Route::middleware(['auth', 'role:Super-Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', App\Http\Controllers\Admin\TenantController::class);
    Route::resource('modulos', App\Http\Controllers\Admin\ModuloController::class);
    Route::resource('licencias', App\Http\Controllers\Admin\LicenciaController::class);

    // Rutas para el panel de configuración del Super-Admin
    Route::get('configuration/{tenant?}', [App\Http\Controllers\Admin\ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('configuration/{tenant}', [App\Http\Controllers\Admin\ConfigurationController::class, 'update'])->name('configuration.update');
});

// Grupo de rutas para el Tenant-Admin
Route::middleware(['auth', 'role:Tenant-Admin'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas para licencias
    Route::get('licencias', [App\Http\Controllers\Tenant\TenantLicenciaController::class, 'index'])->name('licencias.index');
    Route::post('licencias/activar', [App\Http\Controllers\Tenant\TenantLicenciaController::class, 'activar'])->name('licencias.activar');

    // CRUD de Usuarios del Tenant
    Route::resource('users', App\Http\Controllers\Tenant\UserController::class);
    Route::resource('sucursales', App\Http\Controllers\Tenant\SucursalController::class);
    
    // CRUD de Clientes del Tenant
    Route::resource('clientes', ClienteController::class);

    // Rutas para el panel de configuración del Tenant
    Route::get('configuration', [App\Http\Controllers\Tenant\ConfigurationController::class, 'index'])->name('configuration.index');
    Route::post('configuration', [App\Http\Controllers\Tenant\ConfigurationController::class, 'update'])->name('configuration.update');

    // Rutas para el gestor de documentos
    Route::get('documents/upload', [DocumentUploadController::class, 'index'])->name('documents.upload.index');
    Route::post('documents/upload', [DocumentUploadController::class, 'store'])->name('documents.upload.store');
    Route::delete('documents/{documento}', [DocumentUploadController::class, 'destroy'])->name('documents.destroy');
    Route::get('documents/search-clients', [DocumentUploadController::class, 'searchClients'])->name('documents.search.clients');
     
    // Aquí irían las rutas de los módulos específicos
    // Ejemplo para un módulo de "Citas Médicas"
    // Route::get('citas-medicas', [App\Http\Controllers\Tenant\Citas\DashboardController::class, 'index'])->name('citas.index');
    
    // Route::resource('sucursales', App\Http\Controllers\Tenant\SucursalController::class); // Ejemplo para otro módulo

    // Rutas para el Módulo de Facturación
    Route::prefix('facturacion')->name('facturacion.')->group(function () {
        Route::resource('cfdis', App\Modules\Facturacion\Http\Controllers\CfdiController::class);
        Route::resource('pagos', App\Modules\Facturacion\Http\Controllers\PagoController::class);
        Route::get('pagos/search-invoices', [App\Modules\Facturacion\Http\Controllers\PagoController::class, 'searchInvoices'])->name('pagos.search.invoices');
    });
});


require __DIR__.'/auth.php';
