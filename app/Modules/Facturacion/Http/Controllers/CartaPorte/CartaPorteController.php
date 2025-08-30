<?php

namespace App\Modules\Facturacion\Http\Controllers\CartaPorte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Facturacion\Http\Requests\StoreCartaPorteRequest;
use App\Modules\Facturacion\Models\CartaPorte\CartaPorte;
use App\Modules\Facturacion\Services\CartaPorteService;

class CartaPorteController extends Controller
{
    protected $cartaPorteService;

    public function __construct(CartaPorteService $cartaPorteService)
    {
        $this->cartaPorteService = $cartaPorteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartaportes = CartaPorte::all();
        return view('tenant.modules.facturacion.cartaporte.index', compact('cartaportes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenant.modules.facturacion.cartaporte.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartaPorteRequest $request)
    {
        try {
            $cartaPorte = $this->cartaPorteService->generateAndStamp($request->validated());
            return redirect()->route('tenant.facturacion.cartaporte.show', $cartaPorte->id)
                             ->with('success', 'Carta Porte generada y timbrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CartaPorte $cartaporte)
    {
        return view('tenant.modules.facturacion.cartaporte.show', compact('cartaporte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CartaPorte $cartaporte)
    {
        if ($cartaporte->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.cartaporte.show', $cartaporte->id)
                             ->with('error', 'Solo se pueden editar Cartas Porte en estado de borrador.');
        }
        return view('tenant.modules.facturacion.cartaporte.edit', compact('cartaporte'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCartaPorteRequest $request, CartaPorte $cartaporte)
    {
        if ($cartaporte->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.cartaporte.show', $cartaporte->id)
                             ->with('error', 'Solo se pueden actualizar Cartas Porte en estado de borrador.');
        }

        try {
            $data = $request->validated();
            $cartaporte->update($data);

            // Aquí puedes agregar la lógica para actualizar los datos relacionados
            // de ubicaciones, mercancías, etc., similar a como lo haría el servicio.

            return redirect()->route('tenant.facturacion.cartaporte.show', $cartaporte->id)
                             ->with('success', 'Carta Porte actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implement destroy logic if needed
    }

    public function storeAsDraft(StoreCartaPorteRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = 'borrador';
            
            $cartaPorte = CartaPorte::create($data);

            // Aquí puedes agregar la lógica para guardar los datos relacionados
            // de ubicaciones, mercancías, etc., similar a como lo haría el servicio.

            return redirect()->route('tenant.facturacion.cartaporte.index')
                             ->with('success', 'Carta Porte guardada como borrador exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
