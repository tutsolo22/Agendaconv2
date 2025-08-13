<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura {{ $factura->serie }}-{{ $factura->folio }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header, .footer { width: 100%; }
        .header { margin-bottom: 20px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .w-50 { width: 50%; }
        .w-100 { width: 100%; }
        .pull-left { float: left; }
        .pull-right { float: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .section-title { background-color: #333; color: #fff; padding: 5px; margin-bottom: 10px; font-size: 12px; }
        .small-text { font-size: 8px; word-wrap: break-word; }
    </style>
</head>
<body>
    <div class="container">
        <table class="w-100" style="border: none;">
            <tr>
                <td class="w-50" style="border: none; vertical-align: top;">
                    {{-- Aquí podría ir el logo del tenant si lo tuviéramos --}}
                    <h2 style="margin: 0;">{{ $emisor->razon_social }}</h2>
                    <p>
                        {{ $emisor->rfc }}<br>
                        Régimen Fiscal: {{ $emisor->regimen_fiscal_clave }}<br>
                        Lugar de Expedición: {{ $emisor->cp_fiscal }}
                    </p>
                </td>
                <td class="w-50 text-right" style="border: none; vertical-align: top;">
                    <h3 style="margin: 0;">Factura (CFDI)</h3>
                    <p>
                        <span class="font-bold">Serie y Folio:</span> {{ $factura->serie }}-{{ $factura->folio }}<br>
                        <span class="font-bold">Folio Fiscal (UUID):</span> {{ $factura->uuid_fiscal }}<br>
                        <span class="font-bold">Fecha de Emisión:</span> {{ $factura->created_at->format('Y-m-d H:i:s') }}<br>
                        <span class="font-bold">Tipo de Comprobante:</span> {{ $factura->tipo_comprobante }} - Ingreso
                    </p>
                </td>
            </tr>
        </table>

        <div class="section-title">Datos del Receptor</div>
        <table class="w-100" style="border: none; margin-bottom: 20px;">
            <tr>
                <td style="border: none;">
                    <span class="font-bold">Cliente:</span> {{ $receptor->nombre_completo }}<br>
                    <span class="font-bold">RFC:</span> {{ $receptor->rfc }}<br>
                    <span class="font-bold">Uso de CFDI:</span> {{ $factura->uso_cfdi }}<br>
                    <span class="font-bold">Domicilio Fiscal:</span> {{ $receptor->direccion_fiscal }}
                </td>
            </tr>
        </table>

        <div class="section-title">Conceptos</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Clave Prod/Serv</th>
                    <th>Cantidad</th>
                    <th>Clave Unidad</th>
                    <th>Descripción</th>
                    <th class="text-right">Valor Unitario</th>
                    <th class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conceptos as $concepto)
                <tr>
                    <td>{{ $concepto->clave_prod_serv }}</td>
                    <td>{{ number_format($concepto->cantidad, 2) }}</td>
                    <td>{{ $concepto->clave_unidad }}</td>
                    <td>{{ $concepto->descripcion }}</td>
                    <td class="text-right">${{ number_format($concepto->valor_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($concepto->importe, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix" style="margin-top: 20px;">
            <div class="pull-right" style="width: 35%;">
                <table class="table">
                    <tr>
                        <th class="text-right">Subtotal:</th>
                        <td class="text-right">${{ number_format($factura->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-right">Impuestos (IVA 16%):</th>
                        <td class="text-right">${{ number_format($factura->impuestos, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="text-right font-bold">Total:</th>
                        <td class="text-right font-bold">${{ number_format($factura->total, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="pull-left" style="width: 60%;">
                <p>
                    <span class="font-bold">Total con letra:</span><br>
                    <span>{{ $total_en_letras }}</span>
                </p>
                <p>
                    <span class="font-bold">Forma de Pago:</span> {{ $factura->forma_pago }}<br>
                    <span class="font-bold">Método de Pago:</span> {{ $factura->metodo_pago }}
                </p>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <table class="w-100" style="border: none;">
                <tr>
                    <td style="width: 20%; border: none;">
                        <img src="data:image/png;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(120)->generate($qr_code)) !!} ">
                    </td>
                    <td style="width: 80%; border: none; vertical-align: top;">
                        <div class="section-title">Sellos Digitales</div>
                        <p class="small-text">
                            <span class="font-bold">Sello Digital del CFDI:</span><br>
                            {{ wordwrap($sello_cfd, 100, "\n", true) }}
                        </p>
                        <p class="small-text">
                            <span class="font-bold">Sello Digital del SAT:</span><br>
                            {{ wordwrap($sello_sat, 100, "\n", true) }}
                        </p>
                        <p class="small-text">
                            <span class="font-bold">Cadena Original del Complemento de Certificación Digital del SAT:</span><br>
                            {{ wordwrap($cadena_original_tfd, 100, "\n", true) }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer text-center">
            <p>Este documento es una representación impresa de un CFDI.</p>
        </div>
    </div>
</body>
</html>
