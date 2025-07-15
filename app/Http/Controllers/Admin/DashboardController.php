<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del panel de administración.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}