<?php

use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ModuloController;

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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', TenantController::class);
    Route::resource('modulos', ModuloController::class);
    Route::get('tenants/{tenant}/assign-modules', [TenantController::class, 'assignModules'])->name('tenants.assignModules');
    Route::put('tenants/{tenant}/assign-modules', [TenantController::class, 'updateAssignedModules'])->name('tenants.updateAssignedModules');
});

require __DIR__.'/auth.php';