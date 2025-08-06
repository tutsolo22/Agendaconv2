<?php

namespace App\Mail;

use App\Modules\Facturacion\Models\Cfdi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FacturaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public function __new(public Cfdi $factura)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Factura Electrónica: {$this->factura->serie}-{$this->factura->folio}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'facturacion::emails.factura-enviada',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorage($this->factura->path_xml),
            Attachment::fromStorageDisk('local', $this->factura->path_pdf), // Asumiendo que el PDF se generará y guardará
        ];
    }
}