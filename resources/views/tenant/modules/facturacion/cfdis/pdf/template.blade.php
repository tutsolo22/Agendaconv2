<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factura {{ $data['serie'] }}-{{ $data['folio'] }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header, .footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }
        .header { top: 0px; }
        .footer { bottom: 0px; }
        .content {
            margin-top: 100px;
            margin-bottom: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .w-50 { width: 50%; }
        .w-100 { width: 100%; }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }
        .section-title {
            background-color: #333;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .seals {
            word-wrap: break-word;
            font-family: monospace;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <div class="container">
        <table class="w-100 mb-20">
            <tr>
                <td style="width: 20%; border: none;">
                    {{-- Aquí puedes poner el logo de la empresa --}}
                    {{-- <img src="{{ public_path('storage/' . $data['emisor']['logo']) }}" alt="Logo" width="100"> --}}
                </td>
                <td style="width: 50%; border: none;">
                    <h3 style="margin:0;">{{ $data['emisor']['nombre'] }}</h3>
                    <p style="margin:0;">{{ $data['emisor']['rfc'] }}</p>
                    <p style="margin:0;">Régimen Fiscal: {{ $data['emisor']['regimen_fiscal'] }}</p>
                </td>
                <td style="width: 30%; border: 1px solid #ddd; padding: 10px;">
                    <h4 style="margin:0;">FACTURA</h4>
                    <p style="margin:0; font-size: 16px; color: red;">{{ $data['serie'] }}-{{ $data['folio'] }}</p>
                    <p style="margin:0;"><strong>UUID:</strong> {{ $data['uuid'] }}</p>
                    <p style="margin:0;"><strong>Fecha:</strong> {{ $data['fecha'] }}</p>
                </td>
            </tr>
        </table>

        <div class="section-title">DATOS DEL RECEPTOR</div>
        <table>
            <tr>
                <th style="width: 15%;">Nombre:</th>
                <td>{{ $data['receptor']['nombre'] }}</td>
            </tr>
            <tr>
                <th>RFC:</th>
                <td>{{ $data['receptor']['rfc'] }}</td>
            </tr>
            <tr>
                <th>Uso de CFDI:</th>
                <td>{{ $data['receptor']['uso_cfdi'] }}</td>
            </tr>
        </table>

        <div class="section-title">CONCEPTOS</div>
        <table>
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
                @foreach($data['conceptos'] as $concepto)
                <tr>
                    <td>{{ $concepto['clave_prod_serv'] }}</td>
                    <td class="text-center">{{ $concepto['cantidad'] }}</td>
                    <td class="text-center">{{ $concepto['clave_unidad'] }}</td>
                    <td>{{ $concepto['descripcion'] }}</td>
                    <td class="text-right">${{ number_format($concepto['valor_unitario'], 2) }}</td>
                    <td class="text-right">${{ number_format($concepto['importe'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="w-100">
            <tr>
                <td style="width: 70%; border: none;">
                    <p><strong>Forma de Pago:</strong> {{ $data['forma_pago'] }}</p>
                    <p><strong>Método de Pago:</strong> {{ $data['metodo_pago'] }}</p>
                </td>
                <td style="width: 30%; border: none;">
                    <table class="w-100">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-right">${{ number_format($data['subtotal'], 2) }}</td>
                        </tr>
                        {{-- Aquí iría la lógica para mostrar impuestos --}}
                        <tr>
                            <th>Total</th>
                            <td class="text-right"><strong>${{ number_format($data['total'], 2) }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="mt-20">
            <tr>
                <td style="width: 25%;" class="text-center">
                    <img src="data:image/png;base64, {!! base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(120)->generate($data['qr_url'])) !!} ">
                </td>
                <td style="width: 75%;">
                    <div class="seals">
                        <strong>Sello Digital del CFDI:</strong><br>
                        <span>{{ $data['sello_cfdi'] }}</span>
                        <hr>
                        <strong>Sello del SAT:</strong><br>
                        <span>{{ $data['sello_sat'] }}</span>
                        <hr>
                        <strong>Cadena Original del Timbre:</strong><br>
                        <span>{{ $data['cadena_original_tfd'] }}</span>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>