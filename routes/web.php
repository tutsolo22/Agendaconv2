<?php

use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LicenciaController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\ModuleController as TenantModuleController;
use App\Http\Controllers\Tenant\SucursalController;
use App\Http\Controllers\Tenant\UserController as TenantUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Rutas del Panel de Administración
|--------------------------------------------------------------------------
|
| Aquí se agrupan todas las rutas para el Super-Admin.
|
*/
Route::middleware(['auth', 'verified', 'isSuperAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('tenants', TenantController::class);
    Route::resource('modulos', ModuloController::class);
    Route::resource('licencias', LicenciaController::class)->except(['show']);
    Route::get('tenants/{tenant}/assign-modules', [TenantController::class, 'assignModules'])->name('tenants.assignModules');
    Route::put('tenants/{tenant}/assign-modules', [TenantController::class, 'updateAssignedModules'])->name('tenants.updateAssignedModules');
});

/*
|--------------------------------------------------------------------------
| Rutas para los Tenants
|--------------------------------------------------------------------------
|
| Aquí irán las rutas a las que acceden los usuarios de los tenants.
| Cada grupo de rutas de módulo estará protegido por el middleware de licencia.
|
*/
Route::middleware(['auth', 'verified'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', TenantDashboardController::class)->name('dashboard');
    // Esta ruta dinámica mostrará la página de cada módulo y está protegida por el middleware de licencia.
    Route::get('/module/{moduleSlug}', [TenantModuleController::class, 'show'])->name('module.show')->middleware('tenant.license:{moduleSlug}');
    Route::resource('users', TenantUserController::class)->except(['show']);
    Route::resource('sucursales', SucursalController::class)->except(['show']);
});

require __DIR__.'/auth.php';