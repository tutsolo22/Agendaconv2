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

class ProcesarFacturaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $applicationId;
    protected $transaccionId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $applicationId, string $transaccionId)
    {
        $this->data = $data;
        $this->applicationId = $applicationId;
        $this->transaccionId = $transaccionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('salud')->info('Iniciando procesamiento de factura para:', [
            'application_id' => $this->applicationId,
            'transaccion_id' => $this->transaccionId,
            'data' => $this->data
        ]);

        $result = [
            'status' => 'timbrada',
            'uuid_fiscal' => 'ABC-DEF-GHI-JKL',
            'folio_factura' => 'F-1234',
            'url_pdf' => 'https://hexafac.com/descargas/uuid/pdf',
            'url_xml' => 'https://hexafac.com/descargas/uuid/xml'
        ];

        // Simulate a long-running process
        sleep(5);

        // Find the webhook configuration for the application
        $webhookConfig = HexafacWebhookConfiguration::where('application_id', $this->applicationId)
                                                    ->where('active', true)
                                                    ->first();

        if ($webhookConfig && $webhookConfig->url) {
            $payload = [
                'evento' => 'factura.timbrada',
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
