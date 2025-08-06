<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\DatoFiscal;
use App\Modules\Facturacion\Models\Pac;
use PhpCfdi\Credentials\Credential;
use Exception;
use Illuminate\Support\Facades\Storage;

class SatCredentialService
{
    private ?DatoFiscal $datoFiscal = null;
    private ?Credential $credential = null;

    /**
     * Obtiene la configuración de datos fiscales del tenant.
     *
     * @return DatoFiscal
     * @throws Exception
     */
    public function getDatosFiscales(): DatoFiscal
    {
        if ($this->datoFiscal === null) {
            $this->datoFiscal = DatoFiscal::firstOrFail();
        }
        return $this->datoFiscal;
    }

    /**
     * Obtiene el PAC activo configurado para el tenant.
     *
     * @return Pac
     * @throws Exception
     */
    public function getPacActivo(): Pac
    {
        $datoFiscal = $this->getDatosFiscales();
        if (!$datoFiscal->pac || !$datoFiscal->pac->is_active) {
            throw new Exception("No hay un Proveedor de Timbrado (PAC) activo y configurado.");
        }
        return $datoFiscal->pac;
    }

    /**
     * Obtiene el contenido del archivo de certificado (.cer.pem).
     *
     * @return string
     */
    public function getCertificadoPemContent(): string
    {
        return Storage::get($this->getDatosFiscales()->path_cer_pem);
    }

    /**
     * Obtiene el contenido del archivo de llave privada (.key.pem).
     *
     * @return string
     */
    public function getLlavePrivadaPemContent(): string
    {
        return Storage::get($this->getDatosFiscales()->path_key_pem);
    }
}