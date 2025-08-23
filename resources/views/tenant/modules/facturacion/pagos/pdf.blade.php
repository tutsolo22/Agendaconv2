<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Complemento de Pago {{ $pago->serie }}-{{ $pago->folio }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 10px; }
        .container { width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .w-50 { width: 50%; }
        .w-100 { width: 100%; }
        .mt-4 { margin-top: 1.5rem; }
        .mb-0 { margin-bottom: 0; }
        .small-text { font-size: 8px; word-wrap: break-word; }
        .qr-section { float: left; width: 25%; }
        .fiscal-section { float: right; width: 70%; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Aquí puedes agregar el logo de la empresa --}}
        <h2>Complemento de Pago</h2>
        <p><strong>Serie y Folio:</strong> {{ $pago->serie }}-{{ $pago->folio }}</p>
        <p><strong>UUID Fiscal:</strong> {{ $pago->uuid_fiscal }}</p>

        <table class="w-100">
            <tr>
                <td class="w-50" style="vertical-align: top;">
                    <strong>Receptor</strong><br>
                    {{ $pago->cliente->nombre_completo }}<br>
                    RFC: {{ $pago->cliente->rfc }}
                </td>
                <td class="w-50" style="vertical-align: top;">
                    <strong>Datos del Pago</strong><br>
                    Fecha de Pago: {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i:s') }}<br>
                    Monto Total: ${{ number_format($pago->monto, 2) }} {{ $pago->moneda }}
                </td>
            </tr>
        </table>

        <h4 class="mt-4">Documentos Relacionados</h4>
        <table>
            <thead>
                <tr>
                    <th>UUID</th>
                    <th>Serie-Folio</th>
                    <th>Parcialidad</th>
                    <th class="text-end">Saldo Ant.</th>
                    <th class="text-end">Pagado</th>
                    <th class="text-end">Saldo Ins.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pago->documentos as $docto)
                    <tr>
                        <td>{{ substr($docto->id_documento, 0, 8) }}...{{ substr($docto->id_documento, -4) }}</td>
                        <td>{{ $docto->serie }}-{{ $docto->folio }}</td>
                        <td>{{ $docto->num_parcialidad }}</td>
                        <td class="text-end">${{ number_format($docto->imp_saldo_ant, 2) }}</td>
                        <td class="text-end">${{ number_format($docto->imp_pagado, 2) }}</td>
                        <td class="text-end">${{ number_format($docto->imp_saldo_insoluto, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 clearfix">
            <div class="qr-section">
                @isset($qrCode)
                    <img src="data:image/png;base64, {{ $qrCode }}" alt="Código QR" style="width: 120px; height: 120px;">
                @endisset
            </div>
            <div class="fiscal-section">
                @isset($tfd)
                    <p class="mb-0 small-text"><strong>Sello Digital del CFDI:</strong><br>{{ wordwrap($tfd['SelloCFD'], 100, "\n", true) }}</p>
                    <p class="mb-0 small-text"><strong>Sello del SAT:</strong><br>{{ wordwrap($tfd['SelloSAT'], 100, "\n", true) }}</p>
                    <p class="mb-0 small-text"><strong>Cadena Original del Timbre:</strong><br>||{{ wordwrap($tfd->getTfdSourceString(), 100, "\n", true) }}||</p>
                @endisset
            </div>
        </div>

        <div style="position: fixed; bottom: 0px; width: 100%; text-align: center;">
            <p>Este es un comprobante de pago.</p>
        </div>
    </div>
</body>
</html>