<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HexaFacController extends Controller
{
    /**
     * Muestra el dashboard principal de HexaFac.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('admin.hexafac.dashboard');
    }
}
