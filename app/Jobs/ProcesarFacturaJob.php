<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarFacturaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate processing
        Log::channel('salud')->info('Procesando factura para:', $this->data);

        // Here we would call the FacturacionService to create the invoice
        // $facturacionService = new \App\Modules\Facturacion\Services\FacturacionService();
        // $facturacionService->createInvoice($this->data);

        // After processing, we would dispatch a webhook notification
        // WebhookNotificationJob::dispatch($this->data['application_id'], $result);

        sleep(10); // Simulate a long-running process

        Log::channel('salud')->info('Factura procesada.');
    }
}
