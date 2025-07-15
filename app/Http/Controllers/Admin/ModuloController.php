<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modulos = Modulo::latest()->paginate(10);
        return view('admin.modulos.index', compact('modulos'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.modulos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:modulos',
            'descripcion' => 'nullable|string',
            'is_active'   => 'required|boolean',
        ]);

        Modulo::create($request->all());

        return redirect()->route('admin.modulos.index')
                         ->with('success', 'Módulo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function show(Modulo $modulo)
    {
        return view('admin.modulos.show', compact('modulo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function edit(Modulo $modulo)
    {
        return view('admin.modulos.show', compact('modulo'));
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Modulo $modulo)
    {
       $request->validate([
            'nombre'      => ['required', 'string', 'max:255', Rule::unique('modulos')->ignore($modulo->id)],
            'descripcion' => 'nullable|string',
            'is_active'   => 'required|boolean',
        ]);

        $modulo->update($request->all());

        return redirect()->route('admin.modulos.index')
                         ->with('success', 'Módulo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Modulo $modulo)
    {
        $modulo->delete();

        return redirect()->route('admin.modulos.index')
                         ->with('success', 'Módulo eliminado exitosamente.');
    }
}