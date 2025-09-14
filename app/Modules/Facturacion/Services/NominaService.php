<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Complemento\Nomina\Recibo;
use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Services\Helpers\NominaCreatorHelper;
use App\Modules\Facturacion\Services\SatCredentialService;
use Illuminate\Support\Facades\DB;
use Exception;

class NominaService
{
    protected TimbradoServiceInterface $timbradoService;
    protected SatCredentialService $credentialService;

    public function __construct(
        TimbradoServiceInterface $timbradoService,
        SatCredentialService $credentialService
    ) {
        $this->timbradoService = $timbradoService;
        $this->credentialService = $credentialService;
    }

    /**
     * Crea, timbra y guarda un recibo de nómina.
     *
     * @param array $data
     * @return Recibo
     * @throws Exception
     */
    public function crearYTimbrar(array $data): Recibo
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear el XML de la Nómina, sellado.
            $xmlSellado = NominaCreatorHelper::crearXml($data, $this->credentialService);

            // 2. Timbrar el XML con el servicio del PAC.
            $timbradoResponse = $this->timbradoService->timbrar($xmlSellado);

            if (!$timbradoResponse->success) {
                throw new Exception('Error al timbrar el recibo de nómina: ' . ($timbradoResponse->message ?? 'Error desconocido del PAC.'));
            }

            // 3. Guardar el recibo y sus detalles en la base de datos.
            $recibo = Recibo::create([
                // ... Llenar los campos del recibo a partir de $data y $timbradoResponse
                'uuid' => $timbradoResponse->uuid,
                'xml' => $timbradoResponse->xml,
                'status' => 'timbrado',
            ]);

            // ... Guardar percepciones, deducciones, etc.

            return $recibo;
        });
    }
}
