<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\CartaPorte\CartaPorte;
use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Services\Helpers\CartaPorteCreatorHelper;
use App\Modules\Facturacion\Services\SatCredentialService;
use Exception;

class CartaPorteService
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
     * Genera el XML de la Carta Porte, lo timbra con el PAC activo y guarda el resultado.
     *
     * @param array $data Los datos validados del request.
     * @return CartaPorte El modelo de la Carta Porte creada.
     * @throws Exception Si ocurre un error durante el proceso.
     */
    public function generateAndStamp(array $data): CartaPorte
    {
        // 1. Crear el XML de la Carta Porte, sellado.
        $xmlSellado = CartaPorteCreatorHelper::crearXml($data, $this->credentialService);

        // 2. Timbrar el XML con el servicio correspondiente (inyectado por el Service Provider)
        $timbradoResponse = $this->timbradoService->timbrar($xmlSellado);

        if (!$timbradoResponse->success) {
            throw new Exception('Error al timbrar la Carta Porte: ' . ($timbradoResponse->message ?? 'Error desconocido del PAC.'));
        }

        // 3. Guardar el resultado en la base de datos
        $cartaPorte = CartaPorte::create([
            'tenant_id' => tenant('id'),
            'facturacion_cfdi_id' => $data['facturacion_cfdi_id'] ?? null,
            'version' => $data['version'],
            'transp_internac' => $data['transp_internac'],
            'id_ccp' => $data['id_ccp'] ?? null, // Este ID debería generarse en el Helper
            'uuid_fiscal' => $timbradoResponse->uuid,
            'xml' => $timbradoResponse->xml, // Guardar el XML timbrado
            'status' => 'timbrado',
            'origen' => json_encode($data['origen']),
            'destino' => json_encode($data['destino']),
            'mercancias' => json_encode($data['mercancias']),
            'autotransporte' => json_encode($data['autotransporte']),
            'figura_transporte' => json_encode($data['figura_transporte']),
        ]);

        return $cartaPorte;
    }
}
