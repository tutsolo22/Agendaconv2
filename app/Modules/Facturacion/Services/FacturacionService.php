<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Utils\SatCatalogs;
use Illuminate\Support\Facades\DB;
use Exception;

class FacturacionService
{
    protected TimbradoServiceInterface $timbradoService;
    protected ComprobanteEmailService $emailService;
    protected PdfService $pdfService;

    /**
     * Inyectamos los servicios necesarios.
     */
    public function __construct(
        TimbradoServiceInterface $timbradoService, 
        ComprobanteEmailService $emailService,
        PdfService $pdfService
    ) {
        $this->timbradoService = $timbradoService;
        $this->emailService = $emailService;
        $this->pdfService = $pdfService;
    }

    /**
     * Guarda un CFDI como borrador en la base de datos.
     *
     * @param array $data Los datos validados del CFDI.
     * @return Cfdi El modelo del CFDI creado como borrador.
     */
    public function guardarBorrador(array $data): Cfdi
    {
        return DB::transaction(function () use ($data) {
            $data['receptor']['nombre'] = SatCatalogs::cleanRazonSocial($data['receptor']['nombre']);
            
            // Lógica para crear el CFDI en estado 'borrador'.
            // ...

            return new Cfdi(['folio' => 'Borrador-1']); // Placeholder
        });
    }

    /**
     * Crea un CFDI, lo timbra, lo guarda y lo envía por correo.
     *
     * @param array $data Los datos validados del CFDI.
     * @return Cfdi El modelo del CFDI timbrado y guardado.
     * @throws Exception Si el timbrado o el guardado fallan.
     */
    public function crearYTimbrar(array $data): Cfdi
    {
        // 1. Limpiar y preparar datos.
        $data['receptor']['nombre'] = SatCatalogs::cleanRazonSocial($data['receptor']['nombre']);

        // 2. Timbrar el CFDI.
        $resultadoTimbrado = $this->timbradoService->timbrar($data);

        if (!$resultadoTimbrado->success) {
            throw new Exception("Error del PAC: " . ($resultadoTimbrado->message ?? 'Error desconocido.'));
        }

        // 3. Guardar el CFDI en la base de datos.
        $cfdi = DB::transaction(function () use ($data, $resultadoTimbrado) {
            // Lógica para crear el CFDI en la BD.
            // ...
            // Se retorna un placeholder por ahora.
            return new Cfdi([
                'folio' => 'F-123', 
                'uuid_fiscal' => $resultadoTimbrado->uuid, 
                'xml' => $resultadoTimbrado->xml,
                'status' => 'timbrado',
                // Asegúrate de que el modelo Cfdi tenga los fillables correctos.
            ]);
        });

        // 4. Generar el PDF.
        $pdfContent = $this->pdfService->generate($cfdi);

        // 5. Enviar el correo electrónico.
        $this->emailService->send(
            $data['receptor']['email'], 
            "Comprobante Fiscal Digital {$cfdi->serie}-{$cfdi->folio}", 
            $resultadoTimbrado->xml, 
            $pdfContent
        );

        return $cfdi;
    }
}
