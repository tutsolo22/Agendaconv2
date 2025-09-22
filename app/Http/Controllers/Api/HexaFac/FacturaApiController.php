<?php

namespace App\Http\Controllers\Api\HexaFac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\HexaFac\StoreFacturaApiRequest;
use App\Jobs\ProcesarFacturaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="HexaFac API Documentation",
 *      description="API para la plataforma de facturación centralizada HexaFac."
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="HexaFac API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="sanctum",
 *      type="apiKey",
 *      in="header",
 *      name="Authorization",
 *      description="API Key para autenticación (Bearer <token>)"
 * )
 */
class FacturaApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/hexafac/v1/facturas",
     *     summary="Crear una nueva factura",
     *     operationId="createFactura",
     *     tags={"Facturas"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para crear una factura",
     *         @OA\JsonContent(
     *             required={
     *                 "cliente",
     *                 "conceptos",
     *                 "opciones"
     *             },
     *             @OA\Property(property="cliente", type="object",
     *                 @OA\Property(property="id_externo", type="string", nullable=true, description="ID del cliente en la aplicación externa"),
     *                 @OA\Property(property="crear_si_no_existe", type="boolean", description="Indica si el cliente debe crearse si no existe"),
     *                 @OA\Property(property="rfc", type="string", description="RFC del cliente"),
     *                 @OA\Property(property="razon_social", type="string", description="Razón social del cliente"),
     *                 @OA\Property(property="cfdi_uso", type="string", description="Uso de CFDI"),
     *                 @OA\Property(property="regimen_fiscal", type="string", description="Régimen fiscal"),
     *                 @OA\Property(property="domicilio", type="object",
     *                     @OA\Property(property="codigo_postal", type="string", description="Código postal del domicilio fiscal")
     *                 )
     *             ),
     *             @OA\Property(property="conceptos", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="clave_sat", type="string", description="Clave de producto/servicio del SAT"),
     *                     @OA\Property(property="id_externo", type="string", nullable=true, description="ID del producto en la aplicación externa"),
     *                     @OA\Property(property="descripcion", type="string", description="Descripción del concepto"),
     *                     @OA\Property(property="cantidad", type="number", format="float", description="Cantidad"),
     *                     @OA\Property(property="valor_unitario", type="number", format="float", description="Valor unitario"),
     *                     @OA\Property(property="clave_unidad", type="string", description="Clave de unidad del SAT"),
     *                     @OA\Property(property="impuestos", type="object",
     *                         @OA\Property(property="iva_tasa", type="number", format="float", nullable=true, description="Tasa de IVA")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="opciones", type="object",
     *                 @OA\Property(property="tipo_comprobante", type="string", description="Tipo de comprobante (I, E, T, P, N)"),
     *                 @OA\Property(property="moneda", type="string", description="Moneda"),
     *                 @OA\Property(property="forma_pago", type="string", description="Forma de pago"),
     *                 @OA\Property(property="metodo_pago", type="string", description="Método de pago")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Petición de factura recibida y encolada para procesamiento asíncrono",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="peticion_recibida"),
     *             @OA\Property(property="mensaje", type="string", example="La factura ha sido encolada para su procesamiento."),
     *             @OA\Property(property="transaccion_id", type="string", format="uuid", example="uuid-para-seguimiento-1234")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

    public function cancelar(Request $request, $uuid)
    {
        // Logic to cancel an invoice
    }

    public function show(Request $request, $uuid)
    {
        // Logic to show invoice status
    }
}
