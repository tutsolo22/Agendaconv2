<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control principal del Super-Admin.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}