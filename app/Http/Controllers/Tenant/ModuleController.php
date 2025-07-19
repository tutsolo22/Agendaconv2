<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Muestra la página principal de un módulo específico.
     *
     * @param string $moduleSlug El identificador del módulo.
     * @return \Illuminate\View\View
     */
    public function show(string $moduleSlug): View
    {
        // Buscamos el módulo por su nombre (que usamos como slug).
        $module = Modulo::where('nombre', $moduleSlug)->firstOrFail();

        return view('tenant.module.show', compact('module'));
    }
}