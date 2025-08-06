<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                {{ __('Facturación CFDI 4.0') }}
            </h2>
            <a href="{{ route('tenant.facturacion.cfdis.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Crear Factura
            </a>
        </div>
    </x-slot>

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>UUID Fiscal</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($facturas as $factura)
                            <tr>
                                <td>
                                    <a href="{{ route('tenant.facturacion.cfdis.show', $factura) }}" class="fw-bold">{{ $factura->serie }}-{{ $factura->folio }}</a>
                                </td>
                                <td>{{ $factura->cliente->nombre_completo }}</td>
                                <td>{{ $factura->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">${{ number_format($factura->total, 2) }}</td>
                                <td class="font-monospace small">{{ $factura->uuid_fiscal ?? 'N/A' }}</td>
                                <td>
                                    @if($factura->status === 'timbrado')
                                        <span class="badge bg-success">Timbrado</span>
                                    @elseif($factura->status === 'borrador')
                                        <span class="badge bg-warning text-dark">Borrador</span>
                                    @elseif($factura->status === 'cancelado')
                                        <span class="badge bg-danger">Cancelado</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($factura->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($factura->status === 'borrador')
                                        <form action="{{ route('tenant.facturacion.cfdis.timbrar', $factura) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea timbrar esta factura? Esta acción no se puede deshacer.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Timbrar Factura">
                                                <i class="fa-solid fa-check-double"></i> Timbrar
                                            </button>
                                        </form>
                                    @endif
                                    @if($factura->status === 'timbrado')
                                        <div class="btn-group">
                                            <a href="{{ route('tenant.facturacion.cfdis.download.xml', $factura) }}" class="btn btn-sm btn-outline-secondary" title="Descargar XML"><i class="fa-solid fa-file-code"></i></a>
                                            <a href="{{ route('tenant.facturacion.cfdis.download.pdf', $factura) }}" class="btn btn-sm btn-outline-secondary" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                            <form action="{{ route('tenant.facturacion.cfdis.enviar-correo', $factura) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Enviar por Correo">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $factura->id }}" title="Cancelar CFDI">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </div>
                                    @elseif(in_array($factura->status, ['cancelado']))
                                        <div class="btn-group">
                                            <a href="{{ route('tenant.facturacion.cfdis.download.xml', $factura) }}" class="btn btn-sm btn-outline-secondary" title="Descargar XML"><i class="fa-solid fa-file-code"></i></a>
                                            <a href="{{ route('tenant.facturacion.cfdis.download.pdf', $factura) }}" class="btn btn-sm btn-outline-secondary" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                            {{-- Solo se pueden crear notas de crédito si el motivo fue "con relación" --}}
                                            @if($factura->motivo_cancelacion === '01')
                                                <a href="{{ route('tenant.facturacion.cfdis.create-credit-note', $factura) }}" class="btn btn-sm btn-outline-primary" title="Crear Nota de Crédito">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No se han emitido facturas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modales de Cancelación --}}
    @foreach ($facturas as $factura)
        @if($factura->status === 'timbrado')
        <div class="modal fade" id="cancelModal-{{ $factura->id }}" tabindex="-1" aria-labelledby="cancelModalLabel-{{ $factura->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('tenant.facturacion.cfdis.cancelar', $factura) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="cancelModalLabel-{{ $factura->id }}">Cancelar Factura {{ $factura->serie }}-{{ $factura->folio }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="motivo_cancelacion-{{ $factura->id }}" class="form-label">Motivo de la Cancelación (Oficial SAT)</label>
                                <select name="motivo_cancelacion" id="motivo_cancelacion-{{ $factura->id }}" class="form-select" required>
                                    <option value="">Seleccione un motivo...</option>
                                    <option value="01">01 - Comprobante emitido con errores con relación.</option>
                                    <option value="02">02 - Comprobante emitido con errores sin relación.</option>
                                    <option value="03">03 - No se llevó a cabo la operación.</option>
                                    <option value="04">04 - Operación nominativa relacionada en una factura global.</option>
                                </select>
                            </div>
                            <p class="small text-muted">Esta acción es irreversible y se notificará al SAT (simulado).</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</x-layouts.app>