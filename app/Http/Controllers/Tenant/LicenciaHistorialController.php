<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LicenciaHistorialController extends Controller
{
    /**
     * Muestra el historial de todas las licencias adquiridas por el tenant.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Obtenemos el tenant del usuario autenticado.
        $tenant = Auth::user()->tenant;

        // Cargamos todas las licencias asociadas a este tenant,
        // incluyendo la información del módulo relacionado para mostrar su nombre.
        // Ordenamos por fecha de creación descendente para mostrar lo más reciente primero.
        $licencias = $tenant->licencias()->with('modulo')->orderBy('created_at', 'desc')->get();

        // Pasamos los datos a la vista.
        return view('tenant.licencias.historial', compact('licencias'));
    }
}