<?php

namespace App\Http\Controllers\Api\HexaFac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HexaFac\StoreFacturaApiRequest;

use App\Jobs\ProcesarFacturaJob;
use Illuminate\Support\Str;

class FacturaApiController extends Controller
{
    public function store(StoreFacturaApiRequest $request)
    {
        $validatedData = $request->validated();

        $applicationId = $request->user()->id; // Assuming the authenticated user is the HexafacClientApplication
        $transaccionId = (string) Str::uuid();

        ProcesarFacturaJob::dispatch($validatedData, $applicationId, $transaccionId);

        return response()->json([
            'status' => 'peticion_recibida',
            'mensaje' => 'La factura ha sido encolada para su procesamiento.',
            'transaccion_id' => $transaccionId
        ], 202);
    }
}
