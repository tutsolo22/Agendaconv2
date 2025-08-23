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
                    @if($facturacion->status === 'timbrado')
                        <button type="button" class="btn btn-warning mt-2 w-100" data-bs-toggle="modal" data-bs-target="#cancelarCfdiModal">
                            <i class="fa-solid fa-ban me-2"></i>Cancelar CFDI
                        </button>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

</x-layouts.app>

{{-- Solo renderizar el modal y el script si el CFDI se puede cancelar --}}
@if($facturacion->status === 'timbrado')
<!-- Modal de Cancelación -->
<div class="modal fade" id="cancelarCfdiModal" tabindex="-1" aria-labelledby="cancelarCfdiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tenant.facturacion.cfdis.cancelar', $facturacion) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cancelarCfdiModalLabel">Cancelar CFDI: {{ $facturacion->serie }}-{{ $facturacion->folio }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning" role="alert">
                        <strong>Atención:</strong> Este proceso es irreversible. Al confirmar, se enviará la solicitud de cancelación al SAT.
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de Cancelación <span class="text-danger">*</span></label>
                        <select name="motivo" id="motivo" class="form-select" required>
                            <option value="" selected disabled>Seleccione un motivo...</option>
                            <option value="01">01 - Comprobante emitido con errores con relación</option>
                            <option value="02">02 - Comprobante emitido con errores sin relación</option>
                            <option value="03">03 - No se llevó a cabo la operación</option>
                            <option value="04">04 - Operación nominativa relacionada en una factura global</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="folioSustitucionContainer">
                        <label for="folio_sustitucion" class="form-label">Folio Fiscal (UUID) que Sustituye <span class="text-danger">*</span></label>
                        <input type="text" name="folio_sustitucion" id="folio_sustitucion" class="form-control" placeholder="Ingrese el UUID del nuevo CFDI que lo reemplaza">
                        <div class="form-text">Este campo es obligatorio cuando el motivo es "01".</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Este script es específico para la vista show.blade.php
    document.addEventListener('DOMContentLoaded', function () {
        const motivoSelect = document.getElementById('motivo');
        const folioSustitucionContainer = document.getElementById('folioSustitucionContainer');
        const folioSustitucionInput = document.getElementById('folio_sustitucion');

        if (motivoSelect) {
            motivoSelect.addEventListener('change', function() {
                if (this.value === '01') {
                    folioSustitucionContainer.classList.remove('d-none');
                    folioSustitucionInput.required = true;
                } else {
                    folioSustitucionContainer.classList.add('d-none');
                    folioSustitucionInput.required = false;
                    folioSustitucionInput.value = '';
                }
            });
        }
    });
</script>
@endpush
@endif
