<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Envío de Factura</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <h2>Estimado(a) {{ $factura->cliente->nombre_completo }},</h2>

    <p>Le hacemos llegar su Comprobante Fiscal Digital por Internet (CFDI) con folio <strong>{{ $factura->serie }}-{{ $factura->folio }}</strong>.</p>

    <p>Los archivos XML y PDF se encuentran adjuntos en este correo.</p>

    <p>Agradecemos su preferencia.</p>

    <p>Saludos cordiales,<br>
    <strong>{{ $factura->cliente->tenant->razon_social ?? 'Su Proveedor' }}</strong>
    </p>

</body>
</html>