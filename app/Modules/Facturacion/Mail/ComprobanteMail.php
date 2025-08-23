<?php

namespace App\Modules\Facturacion\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class ComprobanteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $xmlContent;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $xmlContent, string $pdfContent)
    {
        $this->subject = $subject;
        $this->xmlContent = $xmlContent;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'facturacion::emails.comprobante',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->xmlContent, 'comprobante.xml')
                ->withMime('application/xml'),
            Attachment::fromData(fn () => $this->pdfContent, 'comprobante.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
