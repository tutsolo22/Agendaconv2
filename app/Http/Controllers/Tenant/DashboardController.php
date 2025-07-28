<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal del tenant.
     */
    public function index(): View
    {
        return view('tenant.dashboard');
    }
}