<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Mail\ComprobanteMail;
use Illuminate\Support\Facades\Mail;

class ComprobanteEmailService
{
    public function send(string $recipient, string $subject, string $xmlContent, string $pdfContent)
    {
        try {
            Mail::to($recipient)->send(new ComprobanteMail($subject, $xmlContent, $pdfContent));
            return (object)['success' => true, 'message' => 'Correo enviado exitosamente.'];
        } catch (\Exception $e) {
            return (object)['success' => false, 'message' => 'Error al enviar el correo: ' . $e->getMessage()];
        }
    }
}
