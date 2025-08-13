<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-file-alt me-2"></i>
                Detalle de Comprobante: {{ $facturacion->serie }}-{{ $facturacion->folio }}
            </h2>
            <a href="{{ route('tenant.facturacion.cfdis.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-8">
            {{-- Panel de Relaciones --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">CFDI Relacionados</h5>
                </div>
                <div class="card-body">
                    @if($facturaOriginal)
                        <h6><i class="fa-solid fa-link text-primary me-2"></i>Esta Nota de Crédito sustituye a:</h6>
                        <p class="mb-0">
                            <a href="{{ route('tenant.facturacion.cfdis.show', $facturaOriginal) }}">
                                Factura: {{ $facturaOriginal->serie }}-{{ $facturaOriginal->folio }}
                            </a>
                            <br>
                            <span class="font-monospace small text-muted">UUID: {{ $facturaOriginal->uuid_fiscal }}</span>
                        </p>
                    @endif

                    @if($relacionadoPor->isNotEmpty())
                        <h6><i class="fa-solid fa-link text-primary me-2"></i>Este comprobante tiene las siguientes Notas de Crédito asociadas:</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($relacionadoPor as $relacion)
                                <li class="mb-2">
                                    <a href="{{ route('tenant.facturacion.cfdis.show', $relacion->cfdi) }}">
                                        Nota de Crédito: {{ $relacion->cfdi->serie }}-{{ $relacion->cfdi->folio }}
                                    </a>
                                    <br>
                                    <span class="font-monospace small text-muted">UUID: {{ $relacion->cfdi->uuid_fiscal }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!$facturaOriginal && $relacionadoPor->isEmpty())
                        <p class="text-muted mb-0">Este comprobante no tiene CFDI relacionados.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{-- Panel de Detalles --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong><br>{{ $facturacion->cliente->nombre_completo }}</p>
                    <p><strong>RFC Cliente:</strong><br>{{ $facturacion->cliente->rfc }}</p>
                    <p><strong>Fecha de Emisión:</strong><br>{{ $facturacion->created_at->format('d/m/Y H:i:s') }}</p>
                    <p><strong>Total:</strong><br><span class="fs-5 fw-bold">${{ number_format($facturacion->total, 2) }}</span></p>
                    <p class="mb-0"><strong>Estatus:</strong><br>
                        @if($facturacion->status === 'timbrado')
                            <span class="badge bg-success fs-6">Timbrado</span>
                        @elseif($facturacion->status === 'borrador')
                            <span class="badge bg-warning text-dark fs-6">Borrador</span>
                        @elseif($facturacion->status === 'cancelado')
                            <span class="badge bg-danger fs-6">Cancelado</span>
                        @else
                            <span class="badge bg-secondary fs-6">{{ ucfirst($facturacion->status) }}</span>
                        @endif
                    </p>
                    @if($facturacion->status === 'cancelado')
                        <p class="mb-0 mt-2">
                            <strong>Motivo Cancelación:</strong><br>
                            <span class="text-danger">{{ $facturacion->motivo_cancelacion }}</span>
                        </p>
                    @endif
                </div>
                @if($facturacion->status === 'timbrado' || $facturacion->status === 'cancelado')
                <div class="card-footer text-center">
                    <div class="btn-group">
                        <a href="{{ route('tenant.facturacion.cfdis.download.xml', $facturacion) }}" class="btn btn-outline-secondary" title="Descargar XML">
                            <i class="fa-solid fa-file-code me-1"></i> XML
                        </a>
                        <a href="{{ route('tenant.facturacion.cfdis.download.pdf', $facturacion) }}" class="btn btn-outline-secondary" title="Descargar PDF">
                            <i class="fa-solid fa-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.app>