<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\HexaFac\HexafacClientApplication;
use App\Models\HexaFac\HexafacWebhookConfiguration;
use Illuminate\Support\Facades\Http;
use App\Models\HexaFac\HexafacApiKey;

class ProcesarFacturaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $applicationId;
    protected $transaccionId;
    protected $isSandbox = false;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $applicationId, string $transaccionId)
    {
        $this->data = $data;
        $this->applicationId = $applicationId;
        $this->transaccionId = $transaccionId;

        // Determine if this is a sandbox request
        $application = HexafacClientApplication::find($applicationId);
        if ($application) {
            // Check if any active API key for this application is marked as sandbox
            // This is a simplified check for now. A more robust solution would pass the specific API key ID.
            $this->isSandbox = $application->apiKeys()->where('active', true)->where('is_sandbox', true)->exists();
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('salud')->info('Iniciando procesamiento de factura para:', [
            'application_id' => $this->applicationId,
            'transaccion_id' => $this->transaccionId,
            'data' => $this->data,
            'is_sandbox' => $this->isSandbox
        ]);

        $result = [];

        if ($this->isSandbox) {
            // Simulate sandbox processing
            Log::channel('salud')->info('Simulando procesamiento en Sandbox.');
            $result = [
                'status' => 'timbrada_sandbox',
                'uuid_fiscal' => 'SANDBOX-UUID-' . $this->transaccionId,
                'folio_factura' => 'S-1234',
                'url_pdf' => 'https://hexafac.com/sandbox/descargas/uuid/pdf',
                'url_xml' => 'https://hexafac.com/sandbox/descargas/uuid/xml'
            ];
        } else {
            // Real processing logic here
            Log::channel('salud')->info('Procesando factura en producción.');
            // Here we would call the FacturacionService to create the invoice
            // $facturacionService = new \App\Modules\Facturacion\Services\FacturacionService();
            // $facturacionService->createInvoice($this->data);

            $result = [
                'status' => 'timbrada',
                'uuid_fiscal' => 'ABC-DEF-GHI-JKL',
                'folio_factura' => 'F-1234',
                'url_pdf' => 'https://hexafac.com/descargas/uuid/pdf',
                'url_xml' => 'https://hexafac.com/descargas/uuid/xml'
            ];
        }

        // Simulate a long-running process
        sleep(5);

        // Find the webhook configuration for the application
        $webhookConfig = HexafacWebhookConfiguration::where('application_id', $this->applicationId)
                                                    ->where('active', true)
                                                    ->first();

        if ($webhookConfig && $webhookConfig->url) {
            $payload = [
                'evento' => ($this->isSandbox ? 'factura.timbrada_sandbox' : 'factura.timbrada'),
                'transaccion_id' => $this->transaccionId,
                'data' => $result
            ];

            try {
                Http::post($webhookConfig->url, $payload);
                Log::channel('salud')->info('Webhook enviado exitosamente para transaccion:', ['transaccion_id' => $this->transaccionId]);
            } catch (\Exception $e) {
                Log::channel('salud')->error('Error al enviar webhook para transaccion:', [
                    'transaccion_id' => $this->transaccionId,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::channel('salud')->warning('No se encontró configuración de webhook activa para la aplicación:', ['application_id' => $this->applicationId]);
        }

        Log::channel('salud')->info('Factura procesada y webhook intentado.');
    }
}
