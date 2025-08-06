<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Complemento de Pago {{ $pago->serie }}-{{ $pago->folio }}</title>
    <style>
        /* Estilos para el PDF */
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; }
        .header, .footer { width: 100%; text-align: center; position: fixed; }
        .header { top: 0px; }
        .footer { bottom: 0px; }
        .content { margin-top: 150px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        {{-- Aquí puedes agregar el logo de la empresa --}}
        <h2>Complemento de Pago</h2>
    </div>

    <div class="content">
        <h3>Detalles del Pago</h3>
        <p><strong>Serie y Folio:</strong> {{ $pago->serie }}-{{ $pago->folio }}</p>
        <p><strong>Cliente:</strong> {{ $pago->cliente->nombre_completo }}</p>
        <p><strong>RFC:</strong> {{ $pago->cliente->rfc }}</p>
        <p><strong>Fecha de Pago:</strong> {{ $pago->fecha_pago }}</p>
        <p><strong>Monto Total:</strong> {{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</p>

        <hr>

        <h4>Documentos Relacionados</h4>
        <table>
            <thead>
                <tr>
                    <th>UUID</th>
                    <th>Serie-Folio</th>
                    <th>Parcialidad</th>
                    <th>Saldo Ant.</th>
                    <th>Pagado</th>
                    <th>Saldo Ins.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pago->documentos as $docto)
                    <tr>
                        <td>{{ $docto->id_documento }}</td>
                        <td>{{ $docto->serie }}-{{ $docto->folio }}</td>
                        <td>{{ $docto->num_parcialidad }}</td>
                        <td>${{ number_format($docto->imp_saldo_ant, 2) }}</td>
                        <td>${{ number_format($docto->imp_pagado, 2) }}</td>
                        <td>${{ number_format($docto->imp_saldo_insoluto, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Este es un comprobante de pago.</p>
    </div>
</body>
</html>