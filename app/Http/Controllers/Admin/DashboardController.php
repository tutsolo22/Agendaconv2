<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de administración principal.
     *
     * La lógica para determinar qué barra de navegación mostrar (Super Admin vs Tenant Admin)
     * se gestiona directamente en la plantilla de diseño 'components.layouts.admin'.
     * Este controlador simplemente devuelve la vista del dashboard.
     */
    public function __invoke(Request $request): View
    {
        return view('admin.dashboard');
    }
}