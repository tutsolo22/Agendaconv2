<?php

namespace App\Http\Controllers\Api\HexaFac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HexaFac\StoreFacturaApiRequest;

use App\Jobs\ProcesarFacturaJob;

class FacturaApiController extends Controller
{
    public function store(StoreFacturaApiRequest $request)
    {
        $validatedData = $request->validated();

        // Add the application id to the data
        // $validatedData['application_id'] = $request->user()->id; // Assuming the user is the application

        ProcesarFacturaJob::dispatch($validatedData);

        return response()->json([
            'status' => 'peticion_recibida',
            'mensaje' => 'La factura ha sido encolada para su procesamiento.',
            'transaccion_id' => uniqid() // Placeholder
        ], 202);
    }
}
