<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Configuracion\DatoFiscal;
use App\Modules\Facturacion\Models\Configuracion\Pac;
use PhpCfdi\Credentials\Credential;
use Exception;
use Illuminate\Support\Facades\Storage;

class SatCredentialService
{
    private ?DatoFiscal $datoFiscal = null;
    private ?Credential $credential = null;
    private ?Pac $pacActivo = null;

    /**
     * Obtiene la configuración de datos fiscales del tenant.
     *
     * @return DatoFiscal
     * @throws Exception
     */
    public function getDatosFiscales(): DatoFiscal
    {
        if ($this->datoFiscal === null) {
            // Usamos first() para poder lanzar una excepción más clara.
            $datoFiscal = DatoFiscal::first();
            if (!$datoFiscal) {
                throw new Exception("No se han configurado los datos fiscales del emisor.");
            }
            $this->datoFiscal = $datoFiscal;
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
        if ($this->pacActivo === null) {
            $pac = Pac::where('is_active', true)->first();
            if (!$pac) {
                throw new Exception("No hay un Proveedor de Timbrado (PAC) activo y configurado.");
            }
            $this->pacActivo = $pac;
        }

        return $this->pacActivo;
    }

    /**
     * Obtiene el contenido del archivo de certificado (.cer.pem).
     *
     * @return string
     */
    public function getCertificadoPemContent(): string
    {
        $path = $this->getDatosFiscales()->path_cer_pem;
        if (!$path || !Storage::exists($path)) {
            throw new Exception("El archivo del certificado (.cer.pem) no se encuentra.");
        }
        return Storage::get($path);
    }

    /**
     * Obtiene el contenido del archivo de llave privada (.key.pem).
     *
     * @return string
     */
    public function getLlavePrivadaPemContent(): string
    {
        $path = $this->getDatosFiscales()->path_key_pem;
        if (!$path || !Storage::exists($path)) {
            throw new Exception("El archivo de la llave privada (.key.pem) no se encuentra.");
        }
        return Storage::get($path);
    }

    /**
     * Obtiene el objeto Credential que contiene el CSD.
     *
     * @return Credential
     * @throws Exception
     */
    public function getCredential(): Credential
    {
        if ($this->credential === null) {
            $datosFiscales = $this->getDatosFiscales();
            $cerContent = $this->getCertificadoPemContent();
            $keyContent = $this->getLlavePrivadaPemContent();
            $password = $datosFiscales->password_csd;
            $this->credential = Credential::create($cerContent, $keyContent, $password);
        }
        return $this->credential;
    }
}