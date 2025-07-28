<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenciaController;
use App\Http\Controllers\Admin\ModuloController;
use App\Http\Controllers\Admin\SuperAdminUserController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Conmtroller\Tenant\LicenciaHistorialController;
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
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grupo de rutas para el Super-Admin
Route::middleware(['auth', 'role:Super-Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('tenants', App\Http\Controllers\Admin\TenantController::class);
    Route::resource('modulos', App\Http\Controllers\Admin\ModuloController::class);
    Route::resource('licencias', App\Http\Controllers\Admin\LicenciaController::class);
});

// Grupo de rutas para el Tenant-Admin
Route::middleware(['auth', 'role:Tenant-Admin'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas para licencias
    Route::get('licencias', [App\Http\Controllers\Tenant\TenantLicenciaController::class, 'index'])->name('licencias.index');
    Route::post('licencias/activar', [App\Http\Controllers\Tenant\TenantLicenciaController::class, 'activar'])->name('licencias.activar');

    // CRUD de Usuarios del Tenant
    Route::resource('users', App\Http\Controllers\Tenant\UserController::class);
    // Route::resource('sucursales', App\Http\Controllers\Tenant\SucursalController::class);
});


require __DIR__.'/auth.php';
